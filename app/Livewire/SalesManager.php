<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SalesManager extends Component
{
    // 1. Quadrant 1: Customer Info
    public ?int $selected_customer_id = null;
    public string $customer_search = '';
    public string $customer_name = '';
    public string $customer_phone = '';
    public string $customer_address = '';
    public string $sale_notes = '';

    // 2. Quadrant 2: Available Products Catalog
    public string $catalogCategory = 'ALL';
    public string $catalogSearch = '';

    // 3. Quadrant 3: Selected Products (Cart)
    public array $items = [];

    // 4. Quadrant 4: Payment Section & Promotions
    public string $payment_type = 'full'; // 'full', 'layaway'
    public string $downpayment_amount = '';
    public string $cash_tendered = '';
    public ?int $selected_promotion_id = null;
    public string $promo_code_input = '';
    public string $promoErrorMessage = '';

    // Receipt Modal (No print, pure display)
    public bool $showReceiptModal = false;
    public ?array $receiptData = null;

    // Feedback
    public string $successMessage = '';

    public function mount()
    {
        $this->items = [];
    }

    // Customer Search & Selection Handling
    public function selectCustomer(int $id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $this->selected_customer_id = $customer->id;
            $this->customer_name = $customer->name;
            $this->customer_phone = $customer->phone ?? '';
            $this->customer_address = $customer->address ?? '';
            $this->customer_search = '';
        }
    }

    public function clearSelectedCustomer()
    {
        $this->selected_customer_id = null;
        $this->customer_name = '';
        $this->customer_phone = '';
        $this->customer_address = '';
        $this->customer_search = '';
    }

    // Product Catalog & Cart Management
    public function addProductToCart(int $productId)
    {
        $product = Product::find($productId);
        if (!$product || $product->quantity_in_stock <= 0) {
            return;
        }

        // Check if already in cart
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $currentQty = (int)$item['quantity'];
                if ($currentQty < $product->quantity_in_stock) {
                    $this->items[$index]['quantity'] = $currentQty + 1;
                    $this->items[$index]['subtotal'] = ($currentQty + 1) * (float)$item['unit_price'];
                }
                return;
            }
        }

        // Add new item to cart
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'unit_price' => (float)$product->tagged_price,
            'quantity' => 1,
            'subtotal' => (float)$product->tagged_price,
            'max_stock' => $product->quantity_in_stock,
        ];

        // If layaway, auto-adjust min downpayment
        if ($this->payment_type === 'layaway') {
            $this->downpayment_amount = (string)$this->minDownpayment;
        }
    }

    public function incrementQuantity(int $index)
    {
        if (isset($this->items[$index])) {
            $product = Product::find($this->items[$index]['product_id']);
            $maxStock = $product ? $product->quantity_in_stock : 1;
            $currentQty = (int)$this->items[$index]['quantity'];

            if ($currentQty < $maxStock) {
                $this->items[$index]['quantity'] = $currentQty + 1;
                $this->items[$index]['subtotal'] = ($currentQty + 1) * (float)$this->items[$index]['unit_price'];
            }
        }
    }

    public function decrementQuantity(int $index)
    {
        if (isset($this->items[$index])) {
            $currentQty = (int)$this->items[$index]['quantity'];
            if ($currentQty > 1) {
                $this->items[$index]['quantity'] = $currentQty - 1;
                $this->items[$index]['subtotal'] = ($currentQty - 1) * (float)$this->items[$index]['unit_price'];
            } else {
                $this->removeItem($index);
            }
        }
    }

    public function removeItem(int $index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);

            if ($this->payment_type === 'layaway') {
                $this->downpayment_amount = (string)$this->minDownpayment;
            }
        }
    }

    public function clearCart()
    {
        $this->items = [];
        $this->selected_promotion_id = null;
        $this->promo_code_input = '';
        $this->promoErrorMessage = '';
        $this->cash_tendered = '';
        $this->downpayment_amount = '';
    }

    // Calculations
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

    public function getMinDownpaymentProperty(): float
    {
        $min = round($this->netAmount * 0.20, 2);
        return max(1.0, min($min, $this->netAmount));
    }

    public function setMinimumDownpayment(): void
    {
        $this->downpayment_amount = (string)$this->minDownpayment;
    }

    public function getPayableTodayProperty(): float
    {
        if ($this->payment_type === 'layaway') {
            return (float)$this->downpayment_amount;
        }
        return (float)$this->netAmount;
    }

    public function getChangeDueProperty(): float
    {
        $tendered = (float)$this->cash_tendered;
        $payable = $this->payableToday;
        if ($tendered < $payable) {
            return 0.0;
        }
        return max(0.0, $tendered - $payable);
    }

    public function setExactCashTendered(): void
    {
        $this->cash_tendered = (string)$this->payableToday;
    }

    public function updatedPaymentType($val)
    {
        if ($val === 'layaway') {
            $this->downpayment_amount = (string)$this->minDownpayment;
            $this->cash_tendered = '';
        } else {
            $this->downpayment_amount = '';
            $this->cash_tendered = '';
        }
    }

    // Checkout & Transaction Execution
    public function recordSale()
    {
        $this->successMessage = '';

        $payableToday = ($this->payment_type === 'layaway') ? (float)$this->downpayment_amount : (float)$this->netAmount;

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

        // Cash validation only applies when payment_type is 'full'
        if ($this->payment_type === 'full') {
            $rules['cash_tendered'] = ['required', 'numeric', 'min:' . max(0.01, $payableToday)];
        } else {
            $rules['downpayment_amount'] = ['required', 'numeric', 'min:' . $this->minDownpayment, 'max:' . $this->netAmount];
        }

        $this->validate($rules, [
            'customer_name.regex' => 'Customer name must contain letters only (numbers are not permitted).',
            'customer_name.required' => 'Customer name is required.',
            'customer_phone.regex' => 'Please enter a valid contact number (digits, spaces, +).',
            'customer_phone.required' => 'Customer contact number is required.',
            'customer_address.required' => 'Customer complete address is required.',
            'items.min' => 'Please add at least one product to the order.',
            'downpayment_amount.required' => 'Please enter the initial downpayment for lay-away.',
            'downpayment_amount.min' => 'A minimum 20% downpayment of ₱' . number_format($this->minDownpayment, 2) . ' is required for lay-away reservations.',
            'downpayment_amount.max' => 'Downpayment cannot exceed the total order amount.',
            'cash_tendered.required' => 'Please enter the cash amount tendered by the customer.',
            'cash_tendered.numeric' => 'Cash tendered must be a valid numeric amount.',
            'cash_tendered.min' => 'Cash tendered cannot be less than the payable amount of ₱' . number_format($payableToday, 2) . '.',
        ]);

        // Preliminary stock check
        $groupedQuantities = [];
        foreach ($this->items as $it) {
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

        $createdSale = null;

        try {
            DB::transaction(function () use ($groupedQuantities, &$createdSale) {
                $productIds = array_keys($groupedQuantities);
                sort($productIds, SORT_NUMERIC);

                $lockedProducts = [];
                foreach ($productIds as $pId) {
                    $lockedProduct = Product::where('id', $pId)->lockForUpdate()->first();
                    $requiredQty = $groupedQuantities[$pId];

                    if (!$lockedProduct || $lockedProduct->status !== 'active') {
                        $pName = $lockedProduct ? $lockedProduct->name : "Product #{$pId}";
                        throw new \RuntimeException("Product '{$pName}' is currently inactive or unavailable.");
                    }

                    if ($lockedProduct->quantity_in_stock < $requiredQty) {
                        throw new \RuntimeException("Insufficient stock for '{$lockedProduct->name}'. Requested: {$requiredQty}, Available: {$lockedProduct->quantity_in_stock}.");
                    }

                    $lockedProducts[$pId] = $lockedProduct;
                }

                $totalAmount = $this->totalAmount;
                $discountAmount = $this->discountAmount;
                $netAmount = $this->netAmount;

                $promo = $this->selected_promotion_id ? Promotion::find($this->selected_promotion_id) : null;
                $promoName = $promo ? $promo->name : null;

                $colors = ['#2563eb', '#ea580c', '#7c3aed', '#16a34a', '#e11d48', '#0284c7'];
                $color = $colors[array_rand($colors)];

                $firstProductId = (int)$this->items[0]['product_id'];
                $firstProduct = $lockedProducts[$firstProductId] ?? Product::find($firstProductId);
                $productCount = count($this->items);
                $productSummary = $firstProduct?->name ?? 'Items';
                if ($productCount > 1) {
                    $productSummary .= ' + ' . ($productCount - 1) . ' other item' . ($productCount > 2 ? 's' : '');
                }

                $isLayaway = ($this->payment_type === 'layaway');
                $downpayment = $isLayaway ? (float)$this->downpayment_amount : $netAmount;
                $remainingBal = $isLayaway ? max(0.0, $netAmount - $downpayment) : 0.0;
                $isCompleted = (!$isLayaway || $remainingBal <= 0);
                $status = $isCompleted ? 'completed' : 'layaway';
                $expiryDate = $isCompleted ? null : now()->addMonths(3)->toDateString();

                // 1. Sync or Create Customer Record
                $customer = null;
                if ($this->selected_customer_id) {
                    $customer = Customer::find($this->selected_customer_id);
                }

                if (!$customer) {
                    $customer = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($this->customer_name))])->first();
                }

                if (!$customer) {
                    $maxCustId = (Customer::withTrashed()->max('id') ?? 0) + 1;
                    $customer = Customer::create([
                        'customer_number' => 'CUST-' . date('Y') . '-' . str_pad($maxCustId, 4, '0', STR_PAD_LEFT),
                        'name' => trim($this->customer_name),
                        'phone' => trim($this->customer_phone) ?: null,
                        'address' => trim($this->customer_address) ?: null,
                        'created_by' => Auth::id() ?? 1,
                    ]);
                }

                if ($customer) {
                    $customer->phone = trim($this->customer_phone) ?: $customer->phone;
                    $customer->address = trim($this->customer_address) ?: $customer->address;
                    $customer->increment('total_orders_count', 1);
                    $customer->increment('total_spent', $downpayment);
                    $customer->save();
                }

                // 2. Create Sale Record
                $sale = Sale::create([
                    'customer_id' => $customer->id,
                    'sale_number' => null,
                    'customer_name' => trim($this->customer_name),
                    'customer_phone' => trim($this->customer_phone),
                    'customer_address' => trim($this->customer_address),
                    'customer_initials' => $customer->initials,
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
                    'notes' => $this->sale_notes ?: ($isLayaway ? 'Lay-Away Reservation (Store Pickup, 3-month expiry)' : 'Store Pickup (Cash Basis)'),
                    'created_by' => Auth::id() ?? 1,
                ]);

                // 3. Collision-Safe Monotonic Order Number
                $candidateNumber = sprintf('ORD-%s-%03d', date('Y'), $sale->id);
                $collisionOffset = 0;
                while (Sale::withTrashed()->where('sale_number', $candidateNumber)->where('id', '!=', $sale->id)->exists()) {
                    $collisionOffset++;
                    $candidateNumber = sprintf('ORD-%s-%03d', date('Y'), $sale->id + $collisionOffset);
                }
                $sale->update(['sale_number' => $candidateNumber]);

                // 4. Save Line Items & Deduct Physical Stock with Audit Trail
                foreach ($this->items as $it) {
                    $pId = (int)$it['product_id'];
                    $qty = (int)$it['quantity'];

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $pId,
                        'quantity' => $qty,
                        'unit_price' => $it['unit_price'],
                        'subtotal' => $it['subtotal'],
                    ]);

                    $lockedProduct = $lockedProducts[$pId];
                    $oldStock = (int)$lockedProduct->quantity_in_stock;
                    $lockedProduct->decrement('quantity_in_stock', $qty);
                    $newStock = $oldStock - $qty;

                    \App\Models\InventoryAdjustment::create([
                        'product_id' => $pId,
                        'user_id' => Auth::id() ?? 1,
                        'old_quantity' => $oldStock,
                        'new_quantity' => $newStock,
                        'quantity_change' => -$qty,
                        'reason' => 'Customer Sale Stock Deduction',
                        'notes' => "Deducted {$qty} unit(s) for Sale Order {$candidateNumber} (Customer: {$customer->name})",
                    ]);
                }

                // 5. Financial Ledger Entry
                if ($downpayment > 0) {
                    $tendered = $this->payment_type === 'full' ? (float)$this->cash_tendered : $downpayment;
                    $change = max(0.0, $tendered - $downpayment);

                    Payment::create([
                        'sale_id' => $sale->id,
                        'amount' => $downpayment,
                        'payment_date' => now(),
                        'payment_method' => 'cash',
                        'reference_number' => null,
                        'notes' => $isLayaway
                            ? "Initial Lay-Away Downpayment (20% Min Deposit, Store Pickup)"
                            : "Full Cash Settlement (Tendered: ₱" . number_format($tendered, 2) . ", Change: ₱" . number_format($change, 2) . ")",
                        'recorded_by' => Auth::id() ?? 1,
                    ]);
                }

                $createdSale = $sale;
            });
        } catch (\RuntimeException $e) {
            $this->addError('items', $e->getMessage());
            return;
        } catch (\Throwable $e) {
            $this->addError('items', 'Transaction failed: ' . $e->getMessage());
            return;
        }

        $tendered = $this->payment_type === 'full' ? (float)$this->cash_tendered : (float)$this->downpayment_amount;
        $payable = ($this->payment_type === 'layaway') ? (float)$this->downpayment_amount : (float)$this->netAmount;
        $change = max(0.0, $tendered - $payable);

        // Prepare Receipt Data (pure display, no print buttons)
        $receiptItems = [];
        foreach ($this->items as $it) {
            $receiptItems[] = [
                'name' => $it['name'],
                'quantity' => (int)$it['quantity'],
                'unit_price' => (float)$it['unit_price'],
                'subtotal' => (float)$it['subtotal'],
            ];
        }

        $this->receiptData = [
            'order_number' => $createdSale->sale_number,
            'sale_date' => now()->format('M d, Y h:i A'),
            'cashier' => Auth::user()?->name ?? 'Staff',
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'items' => $receiptItems,
            'total_amount' => $this->totalAmount,
            'discount_amount' => $this->discountAmount,
            'promo_name' => $createdSale->promo_name,
            'net_amount' => $this->netAmount,
            'payment_type' => $this->payment_type,
            'cash_tendered' => $tendered,
            'change_due' => $change,
            'downpayment_amount' => ($this->payment_type === 'layaway') ? (float)$this->downpayment_amount : 0,
            'remaining_balance' => ($this->payment_type === 'layaway') ? max(0.0, $this->netAmount - (float)$this->downpayment_amount) : 0,
            'expiry_date' => ($this->payment_type === 'layaway') ? now()->addMonths(3)->format('M d, Y') : null,
            'notes' => $this->sale_notes,
        ];
        $this->showReceiptModal = true;

        $this->successMessage = ($this->payment_type === 'layaway')
            ? "Order {$createdSale->sale_number} recorded! Downpayment of ₱" . number_format($payable, 2) . " received. Items reserved until " . now()->addMonths(3)->format('M d, Y') . "."
            : "Sale {$createdSale->sale_number} completed! Cash Tendered: ₱" . number_format($tendered, 2) . " (Change: ₱" . number_format($change, 2) . ").";

        // Reset inputs for next sale
        $this->reset([
            'customer_name',
            'customer_phone',
            'customer_address',
            'sale_notes',
            'selected_customer_id',
            'customer_search',
            'items',
            'downpayment_amount',
            'cash_tendered',
            'selected_promotion_id',
            'promo_code_input',
            'promoErrorMessage'
        ]);
        $this->payment_type = 'full';
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
    }

    public function render()
    {
        // 1. Available products for Quadrant 2
        $catalogQuery = Product::where('status', 'active')->where('quantity_in_stock', '>', 0);

        if ($this->catalogCategory !== 'ALL') {
            $catalogQuery->where('category', $this->catalogCategory);
        }

        if (!empty($this->catalogSearch)) {
            $s = '%' . trim($this->catalogSearch) . '%';
            $catalogQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('description', 'like', $s);
            });
        }

        $availableProducts = $catalogQuery->orderBy('name')->get();

        // 2. Fetch distinct existing categories merged with standard furniture categories
        $dbCategories = Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
        $existingCategories = array_values(array_unique(array_merge(Product::CATEGORIES, $dbCategories)));

        // 3. Customer search query for Quadrant 1
        $searchedCustomers = [];
        if (strlen(trim($this->customer_search)) >= 1) {
            $cs = '%' . trim($this->customer_search) . '%';
            $searchedCustomers = Customer::where('name', 'like', $cs)
                ->orWhere('phone', 'like', $cs)
                ->orWhere('customer_number', 'like', $cs)
                ->take(8)
                ->get();
        }

        return view('livewire.sales-manager', [
            'availableProducts' => $availableProducts,
            'existingCategories' => $existingCategories,
            'searchedCustomers' => $searchedCustomers,
        ]);
    }
}
