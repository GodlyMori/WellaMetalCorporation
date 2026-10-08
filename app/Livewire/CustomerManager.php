<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Modal state: Add/Edit
    public bool $showCustomerModal = false;
    public ?int $editingCustomerId = null;
    public string $name = '';
    public string $phone = '';
    public string $address = '';
    public string $email = '';
    public string $notes = '';

    // Modal state: Purchase History
    public bool $showHistoryModal = false;
    public ?Customer $selectedCustomer = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
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
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->email = '';
        $this->notes = '';
        $this->showCustomerModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetValidation();
        $customer = Customer::findOrFail($id);
        $this->editingCustomerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone ?? '';
        $this->address = $customer->address ?? '';
        $this->email = $customer->email ?? '';
        $this->notes = $customer->notes ?? '';
        $this->showCustomerModal = true;
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($this->editingCustomerId) {
            $customer = Customer::findOrFail($this->editingCustomerId);
            $customer->update([
                'name' => trim($this->name),
                'phone' => trim($this->phone) ?: null,
                'address' => trim($this->address) ?: null,
                'email' => trim($this->email) ?: null,
                'notes' => trim($this->notes) ?: null,
            ]);
            session()->flash('success', 'Customer record updated successfully.');
        } else {
            $maxId = (Customer::withTrashed()->max('id') ?? 0) + 1;
            $customerNumber = 'CUST-' . date('Y') . '-' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
            Customer::create([
                'customer_number' => $customerNumber,
                'name' => trim($this->name),
                'phone' => trim($this->phone) ?: null,
                'address' => trim($this->address) ?: null,
                'email' => trim($this->email) ?: null,
                'notes' => trim($this->notes) ?: null,
                'created_by' => Auth::id(),
            ]);
            session()->flash('success', 'New customer created successfully.');
        }

        $this->showCustomerModal = false;
    }

    public function viewHistory(int $id)
    {
        $this->selectedCustomer = Customer::with(['sales.items.product', 'sales.payments'])->findOrFail($id);
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedCustomer = null;
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->search) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('customer_number', 'like', $s)
                  ->orWhere('phone', 'like', $s)
                  ->orWhere('address', 'like', $s);
            });
        }

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('total_orders_count', '>', 0)->count();
        $totalCustomerRevenue = Customer::sum('total_spent');
        $avgSpent = $activeCustomers > 0 ? ($totalCustomerRevenue / $activeCustomers) : 0;

        $customers = $query->orderBy($this->sortField, $this->sortDirection)->paginate(15);

        return view('livewire.customer-manager', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'totalCustomerRevenue' => $totalCustomerRevenue,
            'avgSpent' => $avgSpent,
        ]);
    }
}
