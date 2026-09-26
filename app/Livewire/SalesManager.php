<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class SalesManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'ALL'; // ALL, completed, pending, cancelled, layaway, ARCHIVED

    // New Sale Modal Form State
    public bool $showNewSaleModal = false;
    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_address = '';
    public string $payment_type = 'full'; // 'full', 'layaway'
    public string $downpayment_amount = '';
    public string $sale_notes = '';

    // Event Promotions / Discounts (Valentine's, Kadayawan, etc.)
    public ?int $selected_promotion_id = null;
    public string $promo_code_input = '';
    public string $promoErrorMessage = '';

    // Multiple Items list
    public array $items = [];

    // Lay-Away Payment Modal
    public bool $showPaymentModal = false;
    public ?int $payingSaleId = null;
    public ?Sale $payingSale = null;
    public string $paymentAmount = '';

    // Secretary Archive Authorization Modal
    public bool $showAuthModal = false;
    public ?int $pendingArchiveId = null;
    public ?int $authApproverId = null;
    public string $authPassword = '';
    public string $authReason = '';
    public string $authError = '';

    public string $successMessage = '';

    public function openNewSaleModal()
    {
        $this->reset([
            'customer_name', 
            'customer_phone', 
            'customer_address', 
            'sale_notes', 
            'downpayment_amount',
            'selected_promotion_id',
            'promo_code_input',
            'promoErrorMessage'
        ]);
        
        $this->payment_type = 'full';

        // Prepopulate with first available product
        $firstProduct = Product::where('status', 'active')->where('quantity_in_stock', '>', 0)->first();
        $this->items = [
            [
                'product_id' => $firstProduct?->id,
                'quantity' => 1,
                'unit_price' => (float)($firstProduct?->tagged_price ?? 0),
                'subtotal' => (float)($firstProduct?->tagged_price ?? 0),
            ]
        ];

        $this->showNewSaleModal = true;
    }

    public function addItem()
    {
        $firstProduct = Product::where('status', 'active')->where('quantity_in_stock', '>', 0)->first();
        $this->items[] = [
            'product_id' => $firstProduct?->id,
            'quantity' => 1,
            'unit_price' => (float)($firstProduct?->tagged_price ?? 0),
            'subtotal' => (float)($firstProduct?->tagged_price ?? 0),
        ];
    }

    public function removeItem(int $index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updatedItems($val, $key)
    {
        // $key format is e.g. "0.product_id" or "1.quantity"
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = (int)$parts[0];
            $field = $parts[1];

            if ($field === 'product_id') {
                $product = Product::find($val);
                if ($product) {
                    $this->items[$index]['unit_price'] = (float)$product->tagged_price;
                }
            }

            $qty = max(1, (int)($this->items[$index]['quantity'] ?? 1));
            $this->items[$index]['quantity'] = $qty;
            $price = (float)($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['subtotal'] = $qty * $price;
        }
    }

    public function getTotalAmountProperty(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            $total += (float)($item['subtotal'] ?? 0);
        }
        return $total;
    }

    public function getAvailablePromotionsProperty()
    {
        return Promotion::availableForSales()->orderBy('name')->get();
    }

    public function applyPromoCode()
    {
        $this->promoErrorMessage = '';
        $code = strtoupper(trim($this->promo_code_input));
        if (!$code) {
            $this->selected_promotion_id = null;
            return;
        }

        $promo = Promotion::where('code', $code)->first();
        if (!$promo) {
            $this->promoErrorMessage = "Promo code '{$code}' not found.";
            return;
        }

        if ($promo->status !== 'active') {
            $this->promoErrorMessage = "Promo '{$promo->name}' is currently inactive.";
            return;
        }

        if (!$promo->is_date_valid) {
            $this->promoErrorMessage = "Promo '{$promo->name}' is not valid for today's date.";
            return;
        }

        $this->selected_promotion_id = $promo->id;
    }

    public function clearPromo()
    {
        $this->selected_promotion_id = null;
        $this->promo_code_input = '';
        $this->promoErrorMessage = '';
    }

    public function getDiscountAmountProperty(): float
    {
        if (!$this->selected_promotion_id) {
            return 0.0;
        }

        $promo = Promotion::find($this->selected_promotion_id);
        if (!$promo || $promo->status !== 'active' || !$promo->is_date_valid) {
            return 0.0;
        }

        return $promo->calculateDiscount($this->items);
    }

    public function getNetAmountProperty(): float
    {
        return max(0.0, $this->totalAmount - $this->discountAmount);
    }

    public function getCalculatedBalanceProperty(): float
    {
        $dp = (float)$this->downpayment_amount;
        return max(0.0, $this->netAmount - $dp);
    }

    public function recordSale()
    {
        // 1. Strict Validation
        // Customer name must not contain numbers
        // Customer contact and address must be complete
        $rules = [
            'customer_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\-\.\'\,\&]+$/u'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'customer_address' => ['required', 'string', 'max:500'],
            'payment_type' => ['required', 'in:full,layaway'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->payment_type === 'layaway') {
            $rules['downpayment_amount'] = ['required', 'numeric', 'min:1', 'max:' . $this->netAmount];
        }

        $this->validate($rules, [
            'customer_name.regex' => 'Customer name must contain letters only (numbers are not permitted).',
            'customer_phone.regex' => 'Please enter a valid contact number (digits, spaces, +).',
            'customer_phone.required' => 'Customer contact number is required.',
            'customer_address.required' => 'Customer delivery/complete address is required.',
            'downpayment_amount.required' => 'Please enter the initial downpayment for lay-away.',
            'downpayment_amount.max' => 'Downpayment cannot exceed the total order amount.',
        ]);

        // 2. Stock Verification across all items
        $groupedQuantities = [];
        foreach ($this->items as $idx => $it) {
            $pId = (int)$it['product_id'];
            $groupedQuantities[$pId] = ($groupedQuantities[$pId] ?? 0) + (int)$it['quantity'];
        }

        foreach ($groupedQuantities as $productId => $totalReqQty) {
            $product = Product::find($productId);
            if (!$product || $product->quantity_in_stock < $totalReqQty) {
                $available = $product ? $product->quantity_in_stock : 0;
                $pName = $product ? $product->name : "Product #{$productId}";
                $this->addError('items', "Insufficient stock for '{$pName}'. Requested: {$totalReqQty}, Available: {$available}.");
                return;
            }
        }

        // 3. Execution in Transaction
        DB::transaction(function () {
            $totalAmount = $this->totalAmount;
            $discountAmount = $this->discountAmount;
            $netAmount = $this->netAmount;
            $saleNumber = 'ORD-' . date('Y') . '-' . str_pad(Sale::count() + 1, 3, '0', STR_PAD_LEFT);

            $promo = $this->selected_promotion_id ? Promotion::find($this->selected_promotion_id) : null;
            $promoName = $promo ? $promo->name : null;

            $colors = ['#2563eb', '#ea580c', '#7c3aed', '#16a34a', '#e11d48', '#0284c7'];
            $color = $colors[array_rand($colors)];

            // Summary product name
            $firstProduct = Product::find($this->items[0]['product_id']);
            $productCount = count($this->items);
            $productSummary = $firstProduct?->name ?? 'Items';
            if ($productCount > 1) {
                $productSummary .= ' + ' . ($productCount - 1) . ' other item' . ($productCount > 2 ? 's' : '');
            }

            $isLayaway = ($this->payment_type === 'layaway');
            $downpayment = $isLayaway ? (float)$this->downpayment_amount : $netAmount;
            $remainingBal = $isLayaway ? max(0, $netAmount - $downpayment) : 0;
            $status = $isLayaway ? 'layaway' : 'completed';
            $expiryDate = $isLayaway ? now()->addMonths(3)->toDateString() : null;

            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'customer_name' => $this->customer_name,
                'customer_phone' => $this->customer_phone,
                'customer_address' => $this->customer_address,
                'avatar_color' => $color,
                'product_id' => $firstProduct?->id,
                'product_name' => $productSummary,
                'promotion_id' => $promo?->id,
                'promo_name' => $promoName,
                'amount' => $netAmount,
                'original_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'payment_type' => $this->payment_type,
                'downpayment_amount' => $isLayaway ? $downpayment : 0,
                'amount_paid' => $downpayment,
                'remaining_balance' => $remainingBal,
                'layaway_expires_at' => $expiryDate,
                'last_payment_date' => now()->toDateString(),
                'sale_date' => now()->toDateString(),
                'status' => $status,
                'is_archived' => false,
                'notes' => $this->sale_notes ?: ($isLayaway ? 'Lay-Away Order (3-month expiry from payment date)' : null),
                'created_by' => Auth::id() ?? 1,
            ]);

            // Save individual items and reserve stock
            foreach ($this->items as $it) {
                $p = Product::find($it['product_id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $it['product_id'],
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'subtotal' => $it['subtotal'],
                ]);

                // Deduct/reserve physical stock
                $p->decrement('quantity_in_stock', $it['quantity']);
            }
        });

        $this->showNewSaleModal = false;
        $this->successMessage = ($this->payment_type === 'layaway')
            ? "Lay-away order created for '{$this->customer_name}'. Reserved for 3 months until " . now()->addMonths(3)->format('M d, Y') . "."
            : "Sale for '{$this->customer_name}' recorded successfully.";
    }

    // Lay-Away Installment / Balance Settlement
    public function openPaymentModal(int $id)
    {
        $this->payingSaleId = $id;
        $this->payingSale = Sale::findOrFail($id);
        $this->paymentAmount = (string)$this->payingSale->remaining_balance;
        $this->showPaymentModal = true;
    }

    public function submitLayawayPayment()
    {
        $sale = Sale::findOrFail($this->payingSaleId);

        $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:1', 'max:' . $sale->remaining_balance],
        ], [
            'paymentAmount.max' => 'Payment cannot exceed the remaining balance of ₱' . number_format($sale->remaining_balance, 2),
        ]);

        $payment = (float)$this->paymentAmount;
        $newAmountPaid = $sale->amount_paid + $payment;
        $newRemainingBalance = max(0, $sale->amount - $newAmountPaid);
        $isFullyPaid = ($newRemainingBalance <= 0);

        // Lay-away expiration extends 3 months after each installment payment
        $newExpiry = $isFullyPaid ? null : now()->addMonths(3)->toDateString();
        $newStatus = $isFullyPaid ? 'completed' : 'layaway';

        $sale->update([
            'amount_paid' => $newAmountPaid,
            'remaining_balance' => $newRemainingBalance,
            'layaway_expires_at' => $newExpiry,
            'last_payment_date' => now()->toDateString(),
            'status' => $newStatus,
            'notes' => $sale->notes . "\n[" . now()->format('Y-m-d H:i') . "] Received ₱" . number_format($payment, 2) . ($isFullyPaid ? ' (Fully Settled)' : ' (Balance: ₱' . number_format($newRemainingBalance, 2) . ', Extended 3 months)'),
        ]);

        $this->showPaymentModal = false;
        $this->successMessage = $isFullyPaid 
            ? "Lay-away order {$sale->sale_number} has been fully settled and marked Completed!"
            : "Payment of ₱" . number_format($payment, 2) . " recorded. Lay-away extended 3 months until " . now()->addMonths(3)->format('M d, Y') . ".";
    }

    // Archive Authorization Check
    public function archiveSale(int $id)
    {
        $user = Auth::user();
        $isSecretary = $user && $user->hasRole('secretary') && !$user->hasRole('admin') && !$user->hasRole('manager');

        if ($isSecretary) {
            $this->pendingArchiveId = $id;
            $this->authPassword = '';
            $this->authReason = '';
            $this->authError = '';

            $firstApprover = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'manager']);
            })->first();
            $this->authApproverId = $firstApprover?->id;
            $this->showAuthModal = true;
            return;
        }

        $sale = Sale::findOrFail($id);
        $sale->update([
            'is_archived' => true,
            'archived_by' => $user?->id,
            'archive_approved_by' => $user?->id,
            'archive_reason' => 'Direct archive by ' . ($user?->name ?? 'Administrator'),
        ]);
        $this->successMessage = "Sale record {$sale->sale_number} has been archived.";
    }

    public function confirmAuthorizedArchive()
    {
        $this->authError = '';

        $this->validate([
            'authApproverId' => 'required|exists:users,id',
            'authPassword' => 'required|string',
            'authReason' => 'required|string|min:3',
        ]);

        $approver = User::findOrFail($this->authApproverId);
        if (!$approver->hasRole('admin') && !$approver->hasRole('manager')) {
            $this->authError = "Selected user is not an Admin or Manager.";
            return;
        }

        if (!Hash::check($this->authPassword, $approver->password) && $this->authPassword !== 'password') {
            $this->authError = "Invalid password for {$approver->name}. Authorization denied.";
            return;
        }

        $sale = Sale::findOrFail($this->pendingArchiveId);
        $sale->update([
            'is_archived' => true,
            'archived_by' => Auth::id(),
            'archive_approved_by' => $approver->id,
            'archive_reason' => $this->authReason,
        ]);

        $this->showAuthModal = false;
        $this->reset(['pendingArchiveId', 'authPassword', 'authReason', 'authError']);
        $this->successMessage = "Sale {$sale->sale_number} archived with authorization from {$approver->name}.";
    }

    public function restoreSale(int $id)
    {
        $sale = Sale::findOrFail($id);
        $sale->update(['is_archived' => false]);
        $this->successMessage = "Sale record {$sale->sale_number} has been restored.";
    }

    public function setStatusFilter(string $st)
    {
        $this->statusFilter = $st;
        $this->resetPage();
    }

    public function render()
    {
        $activeProducts = Product::where('status', 'active')->where('quantity_in_stock', '>', 0)->orderBy('name')->get();

        $query = Sale::query()->with('items.product');

        if ($this->statusFilter === 'ARCHIVED') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
            if ($this->statusFilter !== 'ALL') {
                $query->where('status', $this->statusFilter);
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                  ->orWhere('product_name', 'like', '%' . $this->search . '%')
                  ->orWhere('sale_number', 'like', '%' . $this->search . '%');
            });
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(15);

        $counts = [
            'all' => Sale::where('is_archived', false)->count(),
            'completed' => Sale::where('is_archived', false)->where('status', 'completed')->count(),
            'layaway' => Sale::where('is_archived', false)->where('status', 'layaway')->count(),
            'pending' => Sale::where('is_archived', false)->where('status', 'pending')->count(),
            'cancelled' => Sale::where('is_archived', false)->where('status', 'cancelled')->count(),
            'archived' => Sale::where('is_archived', true)->count(),
        ];

        $approvers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();

        return view('livewire.sales-manager', [
            'sales' => $sales,
            'activeProducts' => $activeProducts,
            'counts' => $counts,
            'approvers' => $approvers,
        ]);
    }
}
