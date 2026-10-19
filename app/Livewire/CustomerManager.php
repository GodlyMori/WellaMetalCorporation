<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'active'; // 'active', 'archived', 'all'

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    // Modal state: Add/Edit
    public bool $showCustomerModal = false;

    public ?int $editingCustomerId = null;

    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public string $address = '';

    public string $email = '';

    public string $notes = '';

    // Modal state: Purchase History
    public bool $showHistoryModal = false;

    public ?Customer $selectedCustomer = null;

    // Digital Receipt Modal (Accessible from purchase history)
    public bool $showReceiptModal = false;

    public ?array $receiptData = null;

    public bool $emailSent = false;

    public ?string $emailDeliveryStatus = null;

    public string $successMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openAddModal()
    {
        $this->resetValidation();
        $this->editingCustomerId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->phone = '';
        $this->address = '';
        $this->email = '';
        $this->notes = '';
        $this->showCustomerModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetValidation();
        $customer = Customer::withTrashed()->findOrFail($id);
        $this->editingCustomerId = $customer->id;
        $this->first_name = $customer->first_name ?? '';
        $this->last_name = $customer->last_name ?? '';

        // Fallback if first_name was empty but name was present
        if (empty($this->first_name) && ! empty($customer->name)) {
            $parts = explode(' ', trim($customer->name));
            $this->last_name = count($parts) > 1 ? array_pop($parts) : '';
            $this->first_name = implode(' ', $parts);
        }

        $this->phone = $customer->phone ?? '';
        $this->address = $customer->address ?? '';
        $this->email = $customer->email ?? '';
        $this->notes = $customer->notes ?? '';
        $this->showCustomerModal = true;
    }

    public function saveCustomer()
    {
        $this->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $fullName = trim("{$this->first_name} {$this->last_name}");

        $data = [
            'first_name' => trim($this->first_name),
            'last_name' => trim($this->last_name),
            'name' => $fullName,
            'phone' => trim($this->phone) ?: null,
            'address' => trim($this->address) ?: null,
            'email' => trim($this->email) ?: null,
            'notes' => trim($this->notes) ?: null,
        ];

        if ($this->editingCustomerId) {
            $customer = Customer::withTrashed()->findOrFail($this->editingCustomerId);
            $customer->update($data);
            $this->successMessage = "Customer '{$fullName}' updated successfully.";
        } else {
            $maxId = (Customer::withTrashed()->max('id') ?? 0) + 1;
            $data['customer_number'] = 'CUST-'.date('Y').'-'.str_pad($maxId, 4, '0', STR_PAD_LEFT);
            $data['created_by'] = Auth::id();
            Customer::create($data);
            $this->successMessage = "New customer '{$fullName}' registered successfully.";
        }

        $this->showCustomerModal = false;
    }

    public function archiveCustomer(int $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete(); // Soft delete
        $this->successMessage = "Customer '{$customer->name}' has been archived.";
    }

    public function restoreCustomer(int $id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();
        $this->successMessage = "Customer '{$customer->name}' has been restored.";
    }

    public function viewHistory(int $id)
    {
        $this->selectedCustomer = Customer::withTrashed()->with(['sales.items.product', 'sales.payments'])->findOrFail($id);
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedCustomer = null;
    }

    public function openReceipt(int $saleId): void
    {
        $sale = Sale::with(['customer', 'items.product', 'payments.recordedBy', 'promotion', 'creator'])->find($saleId);
        if (! $sale) {
            return;
        }

        $receiptItems = [];
        foreach ($sale->items as $it) {
            $receiptItems[] = [
                'name' => $it->product?->name ?? 'Custom Item',
                'quantity' => (int) $it->quantity,
                'unit_price' => (float) $it->unit_price,
                'subtotal' => (float) $it->subtotal,
            ];
        }

        $tendered = (float) ($sale->payments->sum('amount') ?: $sale->amount_paid);

        $this->receiptData = [
            'order_number' => $sale->sale_number ?? ('ORD-'.$sale->id),
            'sale_date' => $sale->sale_date ? Carbon::parse($sale->sale_date)->format('M d, Y h:i A') : $sale->created_at->format('M d, Y h:i A'),
            'cashier' => $sale->creator?->name ?? 'Staff',
            'customer_name' => $sale->customer_name ?: ($sale->customer?->name ?? 'Valued Customer'),
            'customer_email' => $sale->customer_email ?: ($sale->customer?->email ?? null),
            'customer_phone' => $sale->customer_phone ?: ($sale->customer?->phone ?? '—'),
            'customer_address' => $sale->customer_address ?: ($sale->customer?->address ?? '—'),
            'items' => $receiptItems,
            'total_amount' => (float) ($sale->original_amount ?? $sale->amount),
            'discount_amount' => (float) ($sale->discount_amount ?? 0),
            'promo_name' => $sale->promo_name,
            'net_amount' => (float) $sale->amount,
            'payment_type' => $sale->payment_type,
            'cash_tendered' => $tendered,
            'change_due' => 0.0,
            'downpayment_amount' => (float) $sale->downpayment_amount,
            'remaining_balance' => (float) $sale->remaining_balance,
            'expiry_date' => $sale->layaway_expires_at ? Carbon::parse($sale->layaway_expires_at)->format('M d, Y') : null,
            'notes' => $sale->notes,
        ];

        $this->emailSent = false;
        $this->emailDeliveryStatus = null;
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
        $this->emailSent = false;
        $this->emailDeliveryStatus = null;
    }

    public function resendReceiptEmail(): void
    {
        // Email receipt feature has been removed
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->statusFilter === 'active') {
            $query->whereNull('deleted_at');
        } elseif ($this->statusFilter === 'archived') {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }

        if ($this->search) {
            $s = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('first_name', 'like', $s)
                    ->orWhere('last_name', 'like', $s)
                    ->orWhere('customer_number', 'like', $s)
                    ->orWhere('phone', 'like', $s)
                    ->orWhere('address', 'like', $s);
            });
        }

        $totalCustomers = Customer::withTrashed()->count();
        $activeCount = Customer::whereNull('deleted_at')->count();
        $archivedCount = Customer::onlyTrashed()->count();
        $totalCustomerRevenue = Customer::sum('total_spent');
        $activeCustomersWithOrders = Customer::where('total_orders_count', '>', 0)->count();
        $avgSpent = $activeCustomersWithOrders > 0 ? ($totalCustomerRevenue / $activeCustomersWithOrders) : 0;

        $customers = $query->orderBy($this->sortField, $this->sortDirection)->paginate(15);

        return view('livewire.customer-manager', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'activeCount' => $activeCount,
            'archivedCount' => $archivedCount,
            'totalCustomerRevenue' => $totalCustomerRevenue,
            'avgSpent' => $avgSpent,
        ]);
    }
}
