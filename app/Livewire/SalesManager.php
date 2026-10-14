<?php

namespace App\Livewire;

use App\Mail\SalesReceiptMail;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionRedemption;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class SalesManager extends Component
{
    // 1. Quadrant 1: Customer Info
    public ?int $selected_customer_id = null;

    public string $customer_search = '';

    public string $customer_first_name = '';

    public string $customer_last_name = '';

    public string $customer_name = '';

    public string $customer_email = '';

    public string $customer_phone = '';

    public string $customer_address = '';

    public string $sale_notes = '';

    // Email receipt status
    public bool $emailSent = false;

    public ?string $emailDeliveryStatus = null;

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
            $this->customer_first_name = $customer->first_name ?? '';
            $this->customer_last_name = $customer->last_name ?? '';

            if (empty($this->customer_first_name) && ! empty($customer->name)) {
                $parts = explode(' ', trim($customer->name));
                $this->customer_last_name = count($parts) > 1 ? array_pop($parts) : '';
                $this->customer_first_name = implode(' ', $parts);
            }

            $this->customer_name = $customer->name;
            $this->customer_email = $customer->email ?? '';
            $this->customer_phone = $customer->phone ?? '';
            $this->customer_address = $customer->address ?? '';
            $this->customer_search = '';

            // Enforce one-time-use rule if a promotion is already selected
            if ($this->selected_promotion_id) {
                $hasRedeemed = PromotionRedemption::where('promotion_id', $this->selected_promotion_id)
                    ->where('customer_id', $customer->id)
                    ->exists();

                if ($hasRedeemed) {
                    $this->selected_promotion_id = null;
                    $this->promoErrorMessage = 'This promotion has already been redeemed for this customer account.';
                }
            }
        }
    }

    public function clearSelectedCustomer()
    {
        $this->selected_customer_id = null;
        $this->customer_first_name = '';
        $this->customer_last_name = '';
        $this->customer_name = '';
        $this->customer_email = '';
        $this->customer_phone = '';
        $this->customer_address = '';
        $this->customer_search = '';
    }

    /**
     * Resolve the current customer ID from selection or matched profile.
     */
    protected function resolveCurrentCustomerId(): ?int
    {
        if ($this->selected_customer_id) {
            return (int) $this->selected_customer_id;
        }

        $fName = trim($this->customer_first_name);
        $lName = trim($this->customer_last_name);
        $fullName = trim("{$fName} {$lName}");
        $name = trim($this->customer_name ?: $fullName);

        if ($name !== '') {
            $customer = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($name)])->first();
            if ($customer) {
                return (int) $customer->id;
            }
        }

        if ($this->customer_phone !== '') {
            $customer = Customer::where('phone', trim($this->customer_phone))->first();
            if ($customer) {
                return (int) $customer->id;
            }
        }

        return null;
    }

    // Product Catalog & Cart Management
    public function addProductToCart(int $productId)
    {
        $product = Product::find($productId);
        if (! $product || $product->quantity_in_stock <= 0) {
            return;
        }

        // Check if already in cart
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $currentQty = (int) $item['quantity'];
                if ($currentQty < $product->quantity_in_stock) {
                    $this->items[$index]['quantity'] = $currentQty + 1;
                    $this->items[$index]['subtotal'] = ($currentQty + 1) * (float) $item['unit_price'];
                }

                return;
            }
        }

        // Add new item to cart
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'unit_price' => (float) $product->tagged_price,
            'quantity' => 1,
            'subtotal' => (float) $product->tagged_price,
            'max_stock' => $product->quantity_in_stock,
        ];

        // If layaway, auto-adjust min downpayment
        if ($this->payment_type === 'layaway') {
            $this->downpayment_amount = (string) $this->minDownpayment;
        }
    }

    public function updateItemQuantity(int $index, $quantity)
    {
        if (isset($this->items[$index])) {
            $product = Product::find($this->items[$index]['product_id']);
            $maxStock = $product ? (int) $product->quantity_in_stock : 1;
            $qty = max(1, min((int) $quantity, $maxStock));
            $this->items[$index]['quantity'] = $qty;
            $this->items[$index]['subtotal'] = $qty * (float) $this->items[$index]['unit_price'];

            if ($this->payment_type === 'layaway') {
                $this->downpayment_amount = (string) $this->minDownpayment;
            }
        }
    }

    public function incrementQuantity(int $index)
    {
        if (isset($this->items[$index])) {
            $product = Product::find($this->items[$index]['product_id']);
            $maxStock = $product ? $product->quantity_in_stock : 1;
            $currentQty = (int) $this->items[$index]['quantity'];

            if ($currentQty < $maxStock) {
                $this->items[$index]['quantity'] = $currentQty + 1;
                $this->items[$index]['subtotal'] = ($currentQty + 1) * (float) $this->items[$index]['unit_price'];
            }
        }
    }

    public function decrementQuantity(int $index)
    {
        if (isset($this->items[$index])) {
            $currentQty = (int) $this->items[$index]['quantity'];
            if ($currentQty > 1) {
                $this->items[$index]['quantity'] = $currentQty - 1;
                $this->items[$index]['subtotal'] = ($currentQty - 1) * (float) $this->items[$index]['unit_price'];
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
                $this->downpayment_amount = (string) $this->minDownpayment;
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
            $total += (float) ($item['subtotal'] ?? 0);
        }

        return $total;
    }

    public function getAvailablePromotionsProperty()
    {
        return Promotion::availableForSales()->orderBy('name')->get();
    }

    public function updatedSelectedPromotionId($value)
    {
        $this->promoErrorMessage = '';
        if (! $value) {
            return;
        }

        $promo = Promotion::find($value);
        if (! $promo || $promo->status !== 'active' || ! $promo->is_date_valid) {
            $this->selected_promotion_id = null;
            $this->promoErrorMessage = 'This promotion is currently unavailable or expired.';

            return;
        }

        $customerId = $this->resolveCurrentCustomerId();
        if ($customerId) {
            $hasRedeemed = PromotionRedemption::where('promotion_id', $promo->id)
                ->where('customer_id', $customerId)
                ->exists();

            if ($hasRedeemed) {
                $this->selected_promotion_id = null;
                $this->promoErrorMessage = 'This promotion has already been redeemed for this customer account.';

                return;
            }
        }
    }

    public function applyPromoCode()
    {
        $this->promoErrorMessage = '';
        $code = strtoupper(trim($this->promo_code_input));
        if (! $code) {
            $this->selected_promotion_id = null;

            return;
        }

        $promo = Promotion::where('code', $code)->first();
        if (! $promo) {
            $this->promoErrorMessage = "Promo code '{$code}' not found.";

            return;
        }

        if ($promo->status !== 'active') {
            $this->promoErrorMessage = "Promo '{$promo->name}' is currently inactive.";

            return;
        }

        if (! $promo->is_date_valid) {
            $this->promoErrorMessage = "Promo '{$promo->name}' is not valid for today's date.";

            return;
        }

        $customerId = $this->resolveCurrentCustomerId();
        if ($customerId) {
            $hasRedeemed = PromotionRedemption::where('promotion_id', $promo->id)
                ->where('customer_id', $customerId)
                ->exists();

            if ($hasRedeemed) {
                $this->promoErrorMessage = 'This promotion has already been redeemed for this customer account.';
                $this->selected_promotion_id = null;

                return;
            }
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
        if (! $this->selected_promotion_id) {
            return 0.0;
        }

        $promo = Promotion::find($this->selected_promotion_id);
        if (! $promo || $promo->status !== 'active' || ! $promo->is_date_valid) {
            return 0.0;
        }

        $customerId = $this->resolveCurrentCustomerId();
        if ($customerId) {
            $hasRedeemed = PromotionRedemption::where('promotion_id', $promo->id)
                ->where('customer_id', $customerId)
                ->exists();

            if ($hasRedeemed) {
                return 0.0;
            }
        }

        return $promo->calculateDiscount($this->items);
    }

    public function getNetAmountProperty(): float
    {
        return max(0.0, $this->totalAmount - $this->discountAmount);
    }

    public function getCalculatedBalanceProperty(): float
    {
        $dp = (float) $this->downpayment_amount;

        return max(0.0, $this->netAmount - $dp);
    }

    public function getMinDownpaymentProperty(): float
    {
        $min = round($this->netAmount * 0.20, 2);

        return max(1.0, min($min, $this->netAmount));
    }

    public function setMinimumDownpayment(): void
    {
        $this->downpayment_amount = (string) $this->minDownpayment;
    }

    public function getPayableTodayProperty(): float
    {
        if ($this->payment_type === 'layaway') {
            return (float) $this->downpayment_amount;
        }

        return (float) $this->netAmount;
    }

    public function getChangeDueProperty(): float
    {
        $tendered = (float) $this->cash_tendered;
        $payable = $this->payableToday;
        if ($tendered < $payable) {
            return 0.0;
        }

        return max(0.0, $tendered - $payable);
    }

    public function setExactCashTendered(): void
    {
        $this->cash_tendered = (string) $this->payableToday;
    }

    public function updatedPaymentType($val)
    {
        if ($val === 'layaway') {
            $this->downpayment_amount = (string) $this->minDownpayment;
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

        $fName = trim($this->customer_first_name);
        $lName = trim($this->customer_last_name);
        $this->customer_name = trim("{$fName} {$lName}");

        $payableToday = ($this->payment_type === 'layaway') ? (float) $this->downpayment_amount : (float) $this->netAmount;

        $rules = [
            'customer_first_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\.\'\,\&]+$/u'],
            'customer_last_name' => ['required', 'string', 'max:100', 'regex:/^[\pL\s\-\.\'\,\&]+$/u'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'customer_address' => ['required', 'string', 'max:500'],
            'payment_type' => ['required', 'in:full,layaway'],
            'cash_tendered' => ['required', 'numeric', 'min:'.max(0.01, $payableToday)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->payment_type === 'layaway') {
            $rules['downpayment_amount'] = ['required', 'numeric', 'min:'.$this->minDownpayment, 'max:'.$this->netAmount];
        }

        $this->validate($rules, [
            'customer_first_name.required' => 'Customer first name is required.',
            'customer_first_name.regex' => 'Customer first name must contain letters only (numbers are not permitted).',
            'customer_last_name.required' => 'Customer last name is required.',
            'customer_last_name.regex' => 'Customer last name must contain letters only (numbers are not permitted).',
            'customer_name.required' => 'Customer name is required.',
            'customer_email.email' => 'Please enter a valid email address for digital receipt delivery.',
            'customer_phone.regex' => 'Please enter a valid contact number (digits, spaces, +).',
            'customer_phone.required' => 'Customer contact number is required.',
            'customer_address.required' => 'Customer complete address is required.',
            'items.min' => 'Please add at least one product to the order.',
            'downpayment_amount.required' => 'Please enter the initial downpayment for lay-away.',
            'downpayment_amount.min' => 'A minimum 20% downpayment of ₱'.number_format($this->minDownpayment, 2).' is required for lay-away reservations.',
            'downpayment_amount.max' => 'Downpayment cannot exceed the total order amount.',
            'cash_tendered.required' => 'Please enter the cash amount tendered by the customer.',
            'cash_tendered.numeric' => 'Cash tendered must be a valid numeric amount.',
            'cash_tendered.min' => ($this->payment_type === 'layaway')
                ? 'Cash tendered cannot be less than the downpayment amount of ₱'.number_format($payableToday, 2).'.'
                : 'Cash tendered cannot be less than the payable amount of ₱'.number_format($payableToday, 2).'.',
        ]);

        // Preliminary stock check
        $groupedQuantities = [];
        foreach ($this->items as $it) {
            $pId = (int) $it['product_id'];
            $groupedQuantities[$pId] = ($groupedQuantities[$pId] ?? 0) + (int) $it['quantity'];
        }

        foreach ($groupedQuantities as $productId => $totalReqQty) {
            $product = Product::find($productId);
            if (! $product || $product->quantity_in_stock < $totalReqQty) {
                $available = $product ? $product->quantity_in_stock : 0;
                $pName = $product ? $product->name : "Product #{$productId}";
                $this->addError('items', "Insufficient stock for '{$pName}'. Requested: {$totalReqQty}, Available: {$available}.");

                return;
            }
        }

        $createdSale = null;

        DB::beginTransaction();

        try {
            $productIds = array_keys($groupedQuantities);
            sort($productIds, SORT_NUMERIC);

            $lockedProducts = [];
            foreach ($productIds as $pId) {
                $lockedProduct = Product::where('id', $pId)->lockForUpdate()->first();
                $requiredQty = $groupedQuantities[$pId];

                if (! $lockedProduct || $lockedProduct->status !== 'active') {
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

            $firstProductId = (int) $this->items[0]['product_id'];
            $firstProduct = $lockedProducts[$firstProductId] ?? Product::find($firstProductId);
            $productCount = count($this->items);
            $productSummary = $firstProduct?->name ?? 'Items';
            if ($productCount > 1) {
                $productSummary .= ' + '.($productCount - 1).' other item'.($productCount > 2 ? 's' : '');
            }

            $isLayaway = ($this->payment_type === 'layaway');
            $downpayment = $isLayaway ? (float) $this->downpayment_amount : $netAmount;
            $remainingBal = $isLayaway ? max(0.0, $netAmount - $downpayment) : 0.0;
            $isCompleted = (! $isLayaway || $remainingBal <= 0);
            $status = $isCompleted ? 'completed' : 'layaway';
            $expiryDate = $isCompleted ? null : now()->addMonths(3)->toDateString();

            // 1. Sync or Create Customer Record
            $customer = null;
            if ($this->selected_customer_id) {
                $customer = Customer::find($this->selected_customer_id);
            }

            if (! $customer) {
                $customer = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($this->customer_name))])->first();
            }

            if (! $customer) {
                $maxCustId = (Customer::withTrashed()->max('id') ?? 0) + 1;
                $fName = trim($this->customer_first_name);
                $lName = trim($this->customer_last_name);
                if (empty($fName) && ! empty($this->customer_name)) {
                    $parts = explode(' ', trim($this->customer_name));
                    $lName = count($parts) > 1 ? array_pop($parts) : '';
                    $fName = implode(' ', $parts);
                }

                $customer = Customer::create([
                    'customer_number' => 'CUST-'.date('Y').'-'.str_pad($maxCustId, 4, '0', STR_PAD_LEFT),
                    'first_name' => $fName,
                    'last_name' => $lName,
                    'name' => trim($this->customer_name),
                    'email' => trim($this->customer_email) ?: null,
                    'phone' => trim($this->customer_phone) ?: null,
                    'address' => trim($this->customer_address) ?: null,
                    'created_by' => Auth::id() ?? 1,
                ]);
            }

            if ($customer) {
                if (empty($customer->first_name) && ! empty($this->customer_first_name)) {
                    $customer->first_name = trim($this->customer_first_name);
                }
                if (empty($customer->last_name) && ! empty($this->customer_last_name)) {
                    $customer->last_name = trim($this->customer_last_name);
                }
                if (! empty(trim($this->customer_email))) {
                    $customer->email = trim($this->customer_email);
                }
                $customer->phone = trim($this->customer_phone) ?: $customer->phone;
                $customer->address = trim($this->customer_address) ?: $customer->address;
                $customer->increment('total_orders_count', 1);
                $customer->increment('total_spent', $downpayment);
                $customer->save();
            }

            // Enforce One-Time-Use Rule for Promotion
            if ($promo) {
                $priorRedemption = PromotionRedemption::where('promotion_id', $promo->id)
                    ->where('customer_id', $customer->id)
                    ->exists();

                if ($priorRedemption) {
                    throw new \RuntimeException('This promotion has already been redeemed for this customer account.');
                }
            }

            // 2. Create Sale Record
            $saleData = [
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
            ];

            if (Schema::hasColumn('sales', 'customer_email')) {
                $saleData['customer_email'] = trim($this->customer_email) ?: null;
            }

            $sale = Sale::create($saleData);

            // 3. Atomically Record Promotion Redemption
            if ($promo) {
                PromotionRedemption::create([
                    'promotion_id' => $promo->id,
                    'customer_id' => $customer->id,
                    'sale_id' => $sale->id,
                    'created_at' => now(),
                ]);
            }

            // 4. Collision-Safe Monotonic Order Number
            $candidateNumber = sprintf('ORD-%s-%03d', date('Y'), $sale->id);
            $collisionOffset = 0;
            while (Sale::withTrashed()->where('sale_number', $candidateNumber)->where('id', '!=', $sale->id)->exists()) {
                $collisionOffset++;
                $candidateNumber = sprintf('ORD-%s-%03d', date('Y'), $sale->id + $collisionOffset);
            }
            $sale->update(['sale_number' => $candidateNumber]);

            // 5. Save Line Items & Deduct Physical Stock with Audit Trail
            foreach ($this->items as $it) {
                $pId = (int) $it['product_id'];
                $qty = (int) $it['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $pId,
                    'quantity' => $qty,
                    'unit_price' => $it['unit_price'],
                    'subtotal' => $it['subtotal'],
                ]);

                $lockedProduct = $lockedProducts[$pId];
                $oldStock = (int) $lockedProduct->quantity_in_stock;
                $lockedProduct->decrement('quantity_in_stock', $qty);
                $newStock = $oldStock - $qty;

                InventoryAdjustment::create([
                    'product_id' => $pId,
                    'user_id' => Auth::id() ?? 1,
                    'old_quantity' => $oldStock,
                    'new_quantity' => $newStock,
                    'quantity_change' => -$qty,
                    'reason' => 'Customer Sale Stock Deduction',
                    'notes' => "Deducted {$qty} unit(s) for Sale Order {$candidateNumber} (Customer: {$customer->name})",
                ]);
            }

            // 6. Financial Ledger Entry
            if ($downpayment > 0) {
                $tendered = $this->payment_type === 'full' ? (float) $this->cash_tendered : $downpayment;
                $change = max(0.0, $tendered - $downpayment);

                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $downpayment,
                    'payment_date' => now(),
                    'payment_method' => 'cash',
                    'reference_number' => null,
                    'notes' => $isLayaway
                        ? 'Initial Lay-Away Downpayment (20% Min Deposit, Store Pickup)'
                        : 'Full Cash Settlement (Tendered: ₱'.number_format($tendered, 2).', Change: ₱'.number_format($change, 2).')',
                    'recorded_by' => Auth::id() ?? 1,
                ]);
            }

            $createdSale = $sale;

            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();
            $this->addError('items', $e->getMessage());

            return;
        } catch (UniqueConstraintViolationException $e) {
            DB::rollBack();
            $this->addError('items', 'This promotion has already been redeemed for this customer account.');

            return;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('items', 'Transaction failed: '.$e->getMessage());

            return;
        }

        $tendered = $this->payment_type === 'full' ? (float) $this->cash_tendered : (float) $this->downpayment_amount;
        $payable = ($this->payment_type === 'layaway') ? (float) $this->downpayment_amount : (float) $this->netAmount;
        $change = max(0.0, $tendered - $payable);

        // Prepare Receipt Data (pure display, no print buttons)
        $receiptItems = [];
        foreach ($this->items as $it) {
            $receiptItems[] = [
                'name' => $it['name'],
                'quantity' => (int) $it['quantity'],
                'unit_price' => (float) $it['unit_price'],
                'subtotal' => (float) $it['subtotal'],
            ];
        }

        $this->receiptData = [
            'order_number' => $createdSale->sale_number,
            'sale_date' => now()->format('M d, Y h:i A'),
            'cashier' => Auth::user()?->name ?? 'Staff',
            'customer_name' => $this->customer_name,
            'customer_email' => trim($this->customer_email) ?: null,
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
            'downpayment_amount' => ($this->payment_type === 'layaway') ? (float) $this->downpayment_amount : 0,
            'remaining_balance' => ($this->payment_type === 'layaway') ? max(0.0, $this->netAmount - (float) $this->downpayment_amount) : 0,
            'expiry_date' => ($this->payment_type === 'layaway') ? now()->addMonths(3)->format('M d, Y') : null,
            'notes' => $this->sale_notes,
        ];
        $this->showReceiptModal = true;

        // Dispatch digital receipt to customer's email if provided
        $this->emailSent = false;
        $this->emailDeliveryStatus = null;
        $targetEmail = trim($this->customer_email);
        if (! empty($targetEmail)) {
            try {
                Mail::to($targetEmail)
                    ->send(new SalesReceiptMail($createdSale, $this->receiptData));
                $this->emailSent = true;
                $this->emailDeliveryStatus = "Digital receipt delivered to {$targetEmail}.";
            } catch (\Throwable $mailError) {
                Log::warning("Digital receipt delivery failed for {$targetEmail}: ".$mailError->getMessage());
                $this->emailSent = false;
                $this->emailDeliveryStatus = 'Email queued/logged, but delivery notification encountered an issue: '.$mailError->getMessage();
            }
        }

        $this->successMessage = ($this->payment_type === 'layaway')
            ? "Order {$createdSale->sale_number} recorded! Downpayment of ₱".number_format($payable, 2).' received. Items reserved until '.now()->addMonths(3)->format('M d, Y').'.'
            : "Sale {$createdSale->sale_number} completed! Cash Tendered: ₱".number_format($tendered, 2).' (Change: ₱'.number_format($change, 2).').';

        // Reset inputs for next sale
        $this->reset([
            'customer_first_name',
            'customer_last_name',
            'customer_name',
            'customer_email',
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
            'promoErrorMessage',
        ]);
        $this->payment_type = 'full';
    }

    public function resendReceiptEmail()
    {
        if (empty($this->receiptData) || empty($this->receiptData['customer_email']) || empty($this->receiptData['order_number'])) {
            $this->emailDeliveryStatus = 'No valid customer email address found on this receipt.';

            return;
        }

        $sale = Sale::where('sale_number', $this->receiptData['order_number'])->first();
        if (! $sale) {
            $this->emailDeliveryStatus = 'Associated order record not found.';

            return;
        }

        try {
            Mail::to($this->receiptData['customer_email'])
                ->send(new SalesReceiptMail($sale, $this->receiptData));
            $this->emailSent = true;
            $this->emailDeliveryStatus = "Digital receipt re-sent successfully to {$this->receiptData['customer_email']}!";
        } catch (\Throwable $e) {
            Log::warning('Manual re-send receipt failed: '.$e->getMessage());
            $this->emailDeliveryStatus = 'Could not re-send receipt: '.$e->getMessage();
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
        $this->emailSent = false;
        $this->emailDeliveryStatus = null;
    }

    public function render()
    {
        // 1. Available products for Quadrant 2
        $catalogQuery = Product::where('status', 'active')->where('quantity_in_stock', '>', 0);

        if ($this->catalogCategory !== 'ALL') {
            $catalogQuery->where('category', $this->catalogCategory);
        }

        if (! empty($this->catalogSearch)) {
            $s = '%'.trim($this->catalogSearch).'%';
            $catalogQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('description', 'like', $s);
            });
        }

        $availableProducts = $catalogQuery->orderBy('name')->get();

        // 2. Fetch active categories from Category model
        $activeCategoryNames = Category::whereNull('deleted_at')->where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        $existingCategories = ! empty($activeCategoryNames) ? $activeCategoryNames : Product::CATEGORIES;

        // 3. Customer search query for Quadrant 1
        $searchedCustomers = [];
        if (strlen(trim($this->customer_search)) >= 1) {
            $cs = '%'.trim($this->customer_search).'%';
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
            'selected_customer_id' => $this->selected_customer_id,
            'customer_search' => $this->customer_search,
            'customer_first_name' => $this->customer_first_name,
            'customer_last_name' => $this->customer_last_name,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'sale_notes' => $this->sale_notes,
            'catalogCategory' => $this->catalogCategory,
            'catalogSearch' => $this->catalogSearch,
            'items' => $this->items,
            'payment_type' => $this->payment_type,
            'downpayment_amount' => $this->downpayment_amount,
            'cash_tendered' => $this->cash_tendered,
            'selected_promotion_id' => $this->selected_promotion_id,
            'promo_code_input' => $this->promo_code_input,
            'promoErrorMessage' => $this->promoErrorMessage,
            'showReceiptModal' => $this->showReceiptModal,
            'receiptData' => $this->receiptData,
            'successMessage' => $this->successMessage,
            'emailSent' => $this->emailSent,
            'emailDeliveryStatus' => $this->emailDeliveryStatus,
            'totalAmount' => $this->totalAmount,
            'discountAmount' => $this->discountAmount,
            'netAmount' => $this->netAmount,
            'availablePromotions' => $this->availablePromotions,
            'changeDue' => $this->changeDue,
            'minDownpayment' => $this->minDownpayment,
            'calculatedBalance' => $this->calculatedBalance,
        ]);
    }
}
