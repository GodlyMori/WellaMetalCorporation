<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\InventoryAdjustment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Services\LayawayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class LayawayManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'active'; // 'active', 'near_due', 'overdue', 'settled', 'cancelled', 'all'

    // Payment Modal State
    public bool $showPaymentModal = false;
    public ?int $payingSaleId = null;
    public ?Sale $payingSale = null;
    public string $paymentAmount = '';
    public string $payment_method = 'cash'; // 'cash', 'gcash', 'bank_transfer', 'check'
    public string $payment_reference = '';
    public string $payment_notes = '';
    public array $paymentHistory = [];

    // Extend Expiry Modal State
    public bool $showExtendModal = false;
    public ?int $extendingSaleId = null;
    public ?Sale $extendingSale = null;
    public int $extensionDays = 30;
    public string $extensionNotes = '';

    // Cancel / Restock Modal State
    public bool $showCancelModal = false;
    public ?int $cancellingSaleId = null;
    public ?Sale $cancellingSale = null;
    public string $cancelReason = 'Customer defaulted / Unclaimed past expiration deadline';

    // Receipt Modal State
    public bool $showReceiptModal = false;
    public ?array $receiptData = null;

    // Toast Feedback
    public string $successMessage = '';
    public string $errorMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setFilter(string $filter)
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }

    // ==============================================================
    // INSTALLMENT PAYMENT MODAL & SUBMISSION
    // ==============================================================
    public function openPaymentModal(int $saleId)
    {
        $this->payingSaleId = $saleId;
        $this->payingSale = Sale::with(['payments.recordedBy', 'customer', 'items.product'])->findOrFail($saleId);
        $this->paymentAmount = (string)$this->payingSale->remaining_balance;
        $this->payment_method = 'cash';
        $this->payment_reference = '';
        $this->payment_notes = '';
        $this->loadPaymentHistory();
        $this->showPaymentModal = true;
    }

    public function loadPaymentHistory(): void
    {
        if (!$this->payingSaleId) {
            $this->paymentHistory = [];
            return;
        }

        $payments = Payment::with('recordedBy')
            ->where('sale_id', $this->payingSaleId)
            ->orderBy('payment_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $this->paymentHistory = $payments->map(function ($p) {
            return [
                'id' => $p->id,
                'date' => $p->payment_date ? $p->payment_date->format('M d, Y h:i A') : '—',
                'amount' => (float)$p->amount,
                'method' => ucfirst(str_replace('_', ' ', $p->payment_method ?? 'cash')),
                'reference' => $p->reference_number ?: '—',
                'recorded_by' => $p->recordedBy?->name ?? 'System',
                'notes' => $p->notes,
            ];
        })->toArray();
    }

    public function submitLayawayPayment()
    {
        $sale = Sale::findOrFail($this->payingSaleId);

        $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01', 'max:' . $sale->remaining_balance],
            'payment_method' => ['required', 'string'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'payment_notes' => ['nullable', 'string', 'max:255'],
        ], [
            'paymentAmount.max' => 'Payment cannot exceed remaining balance of ₱' . number_format($sale->remaining_balance, 2),
            'paymentAmount.min' => 'Payment amount must be greater than zero.',
        ]);

        $payment = (float)$this->paymentAmount;
        $user = Auth::user();

        try {
            $newRemainingBalance = 0.0;
            $isFullyPaid = false;
            $newPaymentRecord = null;

            DB::transaction(function () use ($sale, $payment, $user, &$newRemainingBalance, &$isFullyPaid, &$newPaymentRecord) {
                $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->firstOrFail();

                if ($lockedSale->status !== 'layaway' && $lockedSale->remaining_balance <= 0) {
                    throw new \RuntimeException('This lay-away order has already been fully settled.');
                }

                if ($payment > (float)$lockedSale->remaining_balance) {
                    throw new \RuntimeException('Payment cannot exceed remaining balance of ₱' . number_format($lockedSale->remaining_balance, 2));
                }

                $newPaymentRecord = Payment::create([
                    'sale_id' => $lockedSale->id,
                    'amount' => $payment,
                    'payment_date' => now(),
                    'payment_method' => $this->payment_method ?: 'cash',
                    'reference_number' => $this->payment_reference ?: null,
                    'notes' => $this->payment_notes ?: 'Lay-Away Installment Payment',
                    'recorded_by' => $user?->id ?? 1,
                ]);

                $authoritativeAmountPaid = (float)Payment::where('sale_id', $lockedSale->id)->sum('amount');
                $newRemainingBalance = max(0.0, (float)$lockedSale->amount - $authoritativeAmountPaid);
                $isFullyPaid = ($newRemainingBalance <= 0);

                $newExpiry = $isFullyPaid ? null : now()->addMonths(3)->toDateString();
                $newStatus = $isFullyPaid ? 'completed' : 'layaway';

                $auditNote = "\n[" . now()->format('Y-m-d H:i') . "] Received ₱" . number_format($payment, 2) .
                    ($isFullyPaid ? ' (Fully Settled)' : ' (Balance: ₱' . number_format($newRemainingBalance, 2) . ', Extended 3 months)');

                $lockedSale->update([
                    'amount_paid' => $authoritativeAmountPaid,
                    'remaining_balance' => $newRemainingBalance,
                    'layaway_expires_at' => $newExpiry,
                    'last_payment_date' => now()->toDateString(),
                    'status' => $newStatus,
                    'notes' => ($lockedSale->notes ? $lockedSale->notes : '') . $auditNote,
                ]);

                // Increment customer spend
                if ($lockedSale->customer_id) {
                    $cust = Customer::find($lockedSale->customer_id);
                    if ($cust) {
                        $cust->increment('total_spent', $payment);
                    }
                }
            });

            // Prepare Receipt Data
            $this->receiptData = [
                'order_number' => $sale->sale_number,
                'customer_name' => $sale->customer_name,
                'customer_phone' => $sale->customer_phone,
                'items' => $sale->items->map(fn($it) => [
                    'name' => $it->product?->name ?? 'Reserved Furniture Item',
                    'category' => $it->product?->category ?? 'Furniture',
                    'quantity' => $it->quantity,
                    'unit_price' => (float)$it->unit_price,
                    'subtotal' => (float)$it->subtotal,
                ])->toArray(),
                'total_order_amount' => (float)$sale->amount,
                'payment_amount' => $payment,
                'payment_method' => ucfirst(str_replace('_', ' ', $this->payment_method)),
                'payment_reference' => $this->payment_reference ?: '—',
                'remaining_balance' => $newRemainingBalance,
                'is_fully_paid' => $isFullyPaid,
                'payment_date' => now()->format('M d, Y h:i A'),
                'cashier_name' => $user?->name ?? 'Cashier',
            ];

            $this->showPaymentModal = false;
            $this->showReceiptModal = true;
            $this->successMessage = "Installment payment of ₱" . number_format($payment, 2) . " recorded successfully." .
                ($isFullyPaid ? " Contract is now fully settled!" : " Remaining balance: ₱" . number_format($newRemainingBalance, 2));

        } catch (\Throwable $e) {
            $this->addError('paymentAmount', $e->getMessage());
        }
    }

    // ==============================================================
    // EXTEND DEADLINE MODAL & LOGIC
    // ==============================================================
    public function openExtendModal(int $saleId)
    {
        $this->extendingSaleId = $saleId;
        $this->extendingSale = Sale::with(['customer', 'items.product'])->findOrFail($saleId);
        $this->extensionDays = 30;
        $this->extensionNotes = '';
        $this->showExtendModal = true;
    }

    public function submitExtension()
    {
        $this->validate([
            'extensionDays' => ['required', 'integer', 'min:1', 'max:180'],
            'extensionNotes' => ['nullable', 'string', 'max:255'],
        ]);

        $sale = Sale::findOrFail($this->extendingSaleId);
        $baseDate = $sale->layaway_expires_at ? Carbon::parse($sale->layaway_expires_at) : now();
        // If already past expiry, extend from today
        if ($baseDate->isPast()) {
            $baseDate = now();
        }
        $newExpiry = $baseDate->addDays($this->extensionDays)->toDateString();

        $auditNote = "\n[" . now()->format('Y-m-d H:i') . "] Lay-away deadline extended by {$this->extensionDays} day(s) to {$newExpiry} by " . (Auth::user()?->name ?? 'Staff') .
            ($this->extensionNotes ? " (Reason: {$this->extensionNotes})" : "");

        $sale->update([
            'layaway_expires_at' => $newExpiry,
            'notes' => ($sale->notes ? $sale->notes : '') . $auditNote,
        ]);

        $this->showExtendModal = false;
        $this->successMessage = "Lay-away contract {$sale->sale_number} extended to " . Carbon::parse($newExpiry)->format('M d, Y') . ".";
    }

    // ==============================================================
    // FORFEIT / CANCEL & RESTOCK STOCK MODAL & LOGIC
    // ==============================================================
    public function openCancelModal(int $saleId)
    {
        $this->cancellingSaleId = $saleId;
        $this->cancellingSale = Sale::with(['customer', 'items.product'])->findOrFail($saleId);
        $this->cancelReason = 'Customer defaulted / Unclaimed past expiration deadline';
        $this->showCancelModal = true;
    }

    public function submitCancellation()
    {
        $this->validate([
            'cancelReason' => ['required', 'string', 'max:255'],
        ]);

        $sale = Sale::findOrFail($this->cancellingSaleId);
        $user = Auth::user();

        DB::transaction(function () use ($sale, $user) {
            $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->firstOrFail();

            if ($lockedSale->status !== 'layaway') {
                throw new \RuntimeException('Only active lay-away contracts can be cancelled & restocked.');
            }

            // Restore inventory for all items
            foreach ($lockedSale->items as $item) {
                $lockedProduct = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($lockedProduct) {
                    $oldStock = (int)$lockedProduct->quantity_in_stock;
                    $newStock = $oldStock + (int)$item->quantity;
                    $lockedProduct->increment('quantity_in_stock', (int)$item->quantity);

                    InventoryAdjustment::create([
                        'product_id' => $lockedProduct->id,
                        'user_id' => $user?->id ?? 1,
                        'old_quantity' => $oldStock,
                        'new_quantity' => $newStock,
                        'quantity_change' => (int)$item->quantity,
                        'reason' => 'Lay-Away Forfeiture Restock',
                        'notes' => "Restocked {$item->quantity} unit(s) of '{$lockedProduct->name}' from cancelled layaway {$lockedSale->sale_number}. Reason: {$this->cancelReason}",
                    ]);
                }
            }

            $auditNote = "\n[" . now()->format('Y-m-d H:i') . "] Lay-away cancelled & reserved items restocked to yard by " . ($user?->name ?? 'Staff') .
                ". Reason: {$this->cancelReason}. Retained payments: ₱" . number_format($lockedSale->amount_paid, 2);

            $lockedSale->update([
                'status' => 'cancelled',
                'notes' => ($lockedSale->notes ? $lockedSale->notes : '') . $auditNote,
            ]);
        });

        $this->showCancelModal = false;
        $this->successMessage = "Lay-away order {$sale->sale_number} cancelled and reserved stock restored to inventory.";
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
    }

    // ==============================================================
    // RENDER & QUERY
    // ==============================================================
    public function render()
    {
        // 1. KPI Calculations
        $baseLayaways = Sale::where('is_archived', false)->where('status', 'layaway');
        $totalReceivables = (float)(clone $baseLayaways)->sum('remaining_balance');
        $activeCount = (clone $baseLayaways)->count();

        $nearDueDate = now()->addDays(14)->toDateString();
        $nearDueCount = (clone $baseLayaways)
            ->whereNotNull('layaway_expires_at')
            ->whereDate('layaway_expires_at', '<=', $nearDueDate)
            ->whereDate('layaway_expires_at', '>=', now()->toDateString())
            ->count();

        $overdueCount = (clone $baseLayaways)
            ->whereNotNull('layaway_expires_at')
            ->whereDate('layaway_expires_at', '<', now()->toDateString())
            ->count();

        $settledThisMonthCount = Sale::where('is_archived', false)
            ->where('payment_type', 'layaway')
            ->where('status', 'completed')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        // 2. Main Query
        $query = Sale::with(['customer', 'items.product', 'payments'])
            ->where('is_archived', false)
            ->where('payment_type', 'layaway');

        // Status Filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 'layaway');
        } elseif ($this->statusFilter === 'near_due') {
            $query->where('status', 'layaway')
                ->whereNotNull('layaway_expires_at')
                ->whereDate('layaway_expires_at', '<=', $nearDueDate)
                ->whereDate('layaway_expires_at', '>=', now()->toDateString());
        } elseif ($this->statusFilter === 'overdue') {
            $query->where('status', 'layaway')
                ->whereNotNull('layaway_expires_at')
                ->whereDate('layaway_expires_at', '<', now()->toDateString());
        } elseif ($this->statusFilter === 'settled') {
            $query->where('status', 'completed');
        } elseif ($this->statusFilter === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        // Search Filter
        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('sale_number', 'like', $s)
                  ->orWhere('customer_name', 'like', $s)
                  ->orWhere('customer_phone', 'like', $s)
                  ->orWhere('notes', 'like', $s)
                  ->orWhereHas('items.product', function ($iq) use ($s) {
                      $iq->where('name', 'like', $s);
                  });
            });
        }

        $contracts = $query->orderByRaw("CASE WHEN status = 'layaway' THEN 0 ELSE 1 END")
            ->orderBy('layaway_expires_at', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('livewire.layaway-manager', [
            'contracts' => $contracts,
            'totalReceivables' => $totalReceivables,
            'activeCount' => $activeCount,
            'nearDueCount' => $nearDueCount,
            'overdueCount' => $overdueCount,
            'settledThisMonthCount' => $settledThisMonthCount,
        ]);
    }
}
