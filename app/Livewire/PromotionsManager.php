<?php

namespace App\Livewire;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionsManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'ALL'; // ALL, active, pending_approval, inactive

    // Create Modal state & fields
    public bool $showCreateModal = false;
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public string $discount_type = 'percentage'; // 'percentage', 'fixed'
    public string $discount_value = '';
    public string $applicable_category = 'ALL'; // 'ALL', 'Sofa', 'Dining Table', 'Closet'
    public string $min_order_amount = '0';
    public string $starts_at = '';
    public string $ends_at = '';

    // Feedback
    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount()
    {
        $this->starts_at = now()->toDateString();
        $this->ends_at = now()->addWeeks(2)->toDateString();
    }

    public function setStatusFilter($filter)
    {
        $this->statusFilter = $filter;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->discount_type = 'percentage';
        $this->discount_value = '';
        $this->applicable_category = 'ALL';
        $this->min_order_amount = '0';
        $this->starts_at = now()->toDateString();
        $this->ends_at = now()->addWeeks(2)->toDateString();
        $this->showCreateModal = true;
    }

    public function savePromotion()
    {
        $user = Auth::user();

        // 1. Role verification: Only Admin and Manager can create promos
        if (!$user || (!$user->hasRole('admin') && !$user->hasRole('manager'))) {
            $this->errorMessage = 'Unauthorized: Only Admins and Managers can create event promotions.';
            return;
        }

        // 2. Form Validation
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:promotions,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'applicable_category' => 'required|string',
            'min_order_amount' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        if ($this->discount_type === 'percentage' && (float)$this->discount_value > 100) {
            $this->addError('discount_value', 'Percentage discount cannot exceed 100%.');
            return;
        }

        // 3. User Decision Workflow:
        // Admin-created promos can be activated directly.
        // Manager-created promos require Admin approval (status = pending_approval).
        $isAdmin = $user->hasRole('admin');
        $status = $isAdmin ? 'active' : 'pending_approval';

        $promo = Promotion::create([
            'name' => $this->name,
            'code' => $this->code ? strtoupper(trim($this->code)) : null,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => (float)$this->discount_value,
            'applicable_category' => $this->applicable_category,
            'min_order_amount' => (float)$this->min_order_amount,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'status' => $status,
            'created_by' => $user->id,
            'approved_by' => $isAdmin ? $user->id : null,
            'approved_at' => $isAdmin ? now() : null,
        ]);

        $this->showCreateModal = false;

        if ($isAdmin) {
            $this->successMessage = "Event Promo '{$promo->name}' created and activated successfully!";
        } else {
            $this->successMessage = "Event Promo '{$promo->name}' submitted. It is pending Admin approval before secretaries can use it.";
        }
    }

    /**
     * Admin action: Approve & Activate a promo submitted by a manager
     */
    public function approvePromotion(int $promoId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            $this->errorMessage = 'Unauthorized: Only System Administrators can approve promotions.';
            return;
        }

        $promo = Promotion::findOrFail($promoId);
        $promo->update([
            'status' => 'active',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->successMessage = "Promotion '{$promo->name}' has been approved and activated!";
    }

    /**
     * Admin action: Toggle Activation (Activate / Deactivate)
     */
    public function toggleStatus(int $promoId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            $this->errorMessage = 'Unauthorized: Only System Administrators can activate/deactivate promotions.';
            return;
        }

        $promo = Promotion::findOrFail($promoId);
        $newStatus = ($promo->status === 'active') ? 'inactive' : 'active';
        
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'active' && !$promo->approved_by) {
            $updateData['approved_by'] = $user->id;
            $updateData['approved_at'] = now();
        }

        $promo->update($updateData);

        $statusLabel = ($newStatus === 'active') ? 'activated' : 'deactivated';
        $this->successMessage = "Promotion '{$promo->name}' has been {$statusLabel}.";
    }

    /**
     * Admin action: Reject promo
     */
    public function rejectPromotion(int $promoId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            $this->errorMessage = 'Unauthorized: Only System Administrators can reject promotions.';
            return;
        }

        $promo = Promotion::findOrFail($promoId);
        $promo->update(['status' => 'rejected']);
        $this->successMessage = "Promotion '{$promo->name}' has been rejected.";
    }

    public function render()
    {
        $query = Promotion::with(['creator', 'approver', 'sales']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('applicable_category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter && $this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(10);

        // Summary counts
        $totalActive = Promotion::where('status', 'active')->count();
        $totalPending = Promotion::where('status', 'pending_approval')->count();
        $totalPromos = Promotion::count();

        return view('livewire.promotions-manager', [
            'promotions' => $promotions,
            'totalActive' => $totalActive,
            'totalPending' => $totalPending,
            'totalPromos' => $totalPromos,
            'isAdmin' => Auth::user()?->hasRole('admin') ?? false,
            'isManager' => Auth::user()?->hasRole('manager') ?? false,
            'categories' => \App\Models\Product::CATEGORIES,
        ])->layout('layouts.app');
    }
}
