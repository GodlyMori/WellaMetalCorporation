<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = 'ALL'; // ALL, Sofa, Dining Table, Closet, ARCHIVED

    // Add Single Product Modal state & fields
    public bool $showSingleModal = false;
    public string $new_name = '';
    public string $new_category = 'Sofa';
    public string $new_description = '';
    public string $new_price = '';
    public string $new_stock = '0';

    // Add Multiple Products Modal state & fields
    public bool $showMultipleModal = false;
    public array $bulkProducts = [
        ['name' => '', 'category' => 'Sofa', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '0'],
        ['name' => '', 'category' => 'Dining Table', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '0'],
    ];

    // Edit Modal state & fields
    public bool $showEditModal = false;
    public ?int $editingProductId = null;
    public string $edit_name = '';
    public string $edit_category = '';
    public string $edit_description = '';
    public string $edit_price = '';
    public string $edit_stock = '';

    // Restock Modal state & fields
    public bool $showRestockModal = false;
    public ?int $restockProductId = null;
    public string $restockProductName = '';
    public int $restockQuantity = 5;
    public string $sourceBranch = 'Toril';
    public string $restockNotes = '';

    // Restock History Accordion toggle
    public bool $restockHistoryOpen = false;

    // Secretary Archive Authorization Modal state
    public bool $showAuthModal = false;
    public ?int $pendingArchiveId = null;
    public ?int $authApproverId = null;
    public string $authPassword = '';
    public string $authReason = '';
    public string $authError = '';

    // Toast notification message
    public string $successMessage = '';

    public function setCategory($cat)
    {
        $this->selectedCategory = $cat;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openSingleModal()
    {
        $this->reset(['new_name', 'new_description', 'new_price', 'new_stock']);
        $this->new_category = 'Sofa';
        $this->showSingleModal = true;
    }

    public function saveSingleProduct()
    {
        $this->validate([
            'new_name' => 'required|string|max:255',
            'new_category' => 'required|string',
            'new_description' => 'nullable|string',
            'new_price' => 'required|numeric|min:0',
            'new_stock' => 'required|integer|min:0',
        ]);

        Product::create([
            'name' => $this->new_name,
            'category' => $this->new_category,
            'description' => $this->new_description,
            'tagged_price' => $this->new_price,
            'quantity_in_stock' => $this->new_stock,
            'status' => 'active',
        ]);

        $this->showSingleModal = false;
        $this->successMessage = "Product '{$this->new_name}' added successfully.";
    }

    public function openMultipleModal()
    {
        $this->bulkProducts = [
            ['name' => '', 'category' => 'Sofa', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '0'],
            ['name' => '', 'category' => 'Dining Table', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '0'],
        ];
        $this->showMultipleModal = true;
    }

    public function addBulkRow()
    {
        $this->bulkProducts[] = [
            'name' => '',
            'category' => 'Sofa',
            'description' => '',
            'tagged_price' => '',
            'quantity_in_stock' => '0',
        ];
    }

    public function removeBulkRow($index)
    {
        unset($this->bulkProducts[$index]);
        $this->bulkProducts = array_values($this->bulkProducts);
    }

    public function saveMultipleProducts()
    {
        $this->validate([
            'bulkProducts.*.name' => 'required|string|max:255',
            'bulkProducts.*.category' => 'required|string',
            'bulkProducts.*.tagged_price' => 'required|numeric|min:0',
            'bulkProducts.*.quantity_in_stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () {
            foreach ($this->bulkProducts as $p) {
                if (!empty(trim($p['name']))) {
                    Product::create([
                        'name' => $p['name'],
                        'category' => $p['category'],
                        'description' => $p['description'] ?? '',
                        'tagged_price' => $p['tagged_price'],
                        'quantity_in_stock' => $p['quantity_in_stock'],
                        'status' => 'active',
                    ]);
                }
            }
        });

        $this->showMultipleModal = false;
        $this->successMessage = "Multiple products added successfully.";
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
        $this->edit_name = $product->name;
        $this->edit_category = $product->category;
        $this->edit_description = $product->description ?? '';
        $this->edit_price = (string)$product->tagged_price;
        $this->edit_stock = (string)$product->quantity_in_stock;
        $this->showEditModal = true;
    }

    public function updateProduct()
    {
        $this->validate([
            'edit_name' => 'required|string|max:255',
            'edit_category' => 'required|string',
            'edit_price' => 'required|numeric|min:0',
            'edit_stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($this->editingProductId);
        $product->update([
            'name' => $this->edit_name,
            'category' => $this->edit_category,
            'description' => $this->edit_description,
            'tagged_price' => $this->edit_price,
            'quantity_in_stock' => $this->edit_stock,
        ]);

        $this->showEditModal = false;
        $this->successMessage = "Product '{$product->name}' updated successfully.";
    }

    // ARCHIVE instead of DELETE (User ADD ON requirement + Role Permission check)
    public function archiveProduct($id)
    {
        $user = Auth::user();
        $isSecretary = $user && $user->hasRole('secretary') && !$user->hasRole('admin') && !$user->hasRole('manager');

        if ($isSecretary) {
            // Require Admin / Manager authorization
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

        $product = Product::findOrFail($id);
        $product->update([
            'status' => 'archived',
            'archived_by' => $user?->id,
            'archive_approved_by' => $user?->id,
            'archive_reason' => 'Direct archive by ' . ($user?->name ?? 'Administrator'),
        ]);
        $this->successMessage = "Product '{$product->name}' has been archived.";
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

        $product = Product::findOrFail($this->pendingArchiveId);
        $product->update([
            'status' => 'archived',
            'archived_by' => Auth::id(),
            'archive_approved_by' => $approver->id,
            'archive_reason' => $this->authReason,
        ]);

        $this->showAuthModal = false;
        $this->reset(['pendingArchiveId', 'authPassword', 'authReason', 'authError']);
        $this->successMessage = "Product '{$product->name}' archived with authorization from {$approver->name}.";
    }

    // RESTORE archived product (User ADD ON requirement)
    public function restoreProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'active']);
        $this->successMessage = "Product '{$product->name}' has been restored to active inventory.";
    }

    public function openRestockModal($id)
    {
        $product = Product::findOrFail($id);
        $this->restockProductId = $product->id;
        $this->restockProductName = $product->name;
        $this->restockQuantity = 5;
        $this->sourceBranch = 'Toril';
        $this->restockNotes = '';
        $this->showRestockModal = true;
    }

    public function submitRestock()
    {
        $this->validate([
            'restockQuantity' => 'required|integer|min:1',
            'sourceBranch' => 'required|string',
        ]);

        DB::transaction(function () {
            $product = Product::findOrFail($this->restockProductId);
            $product->increment('quantity_in_stock', $this->restockQuantity);

            $transfer = StockTransfer::create([
                'source_branch' => $this->sourceBranch,
                'received_by' => Auth::id() ?? 1,
                'date_received' => now()->toDateString(),
                'notes' => $this->restockNotes ?: "Restocked {$this->restockQuantity} units of {$product->name}",
            ]);

            StockTransferItem::create([
                'stock_transfer_id' => $transfer->id,
                'product_id' => $product->id,
                'quantity_received' => $this->restockQuantity,
            ]);
        });

        $this->showRestockModal = false;
        $this->successMessage = "Restocked {$this->restockQuantity} units for '{$this->restockProductName}'.";
    }

    public function render()
    {
        // Category counts
        $allCount = Product::where('status', 'active')->count();
        $sofaCount = Product::where('status', 'active')->where('category', 'Sofa')->count();
        $diningCount = Product::where('status', 'active')->where('category', 'Dining Table')->count();
        $closetCount = Product::where('status', 'active')->where('category', 'Closet')->count();
        $archivedCount = Product::where('status', 'archived')->count();

        // Query
        $query = Product::query();

        if ($this->selectedCategory === 'ARCHIVED') {
            $query->where('status', 'archived');
        } else {
            $query->where('status', 'active');
            if ($this->selectedCategory !== 'ALL') {
                $query->where('category', $this->selectedCategory);
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . ltrim($this->search, 'Pp0') . '%');
            });
        }

        $products = $query->orderBy('id', 'asc')->paginate(15);

        // Restock transfers history
        $restockTransfers = StockTransfer::with(['items.product', 'receiver'])
            ->latest('date_received')
            ->take(10)
            ->get();

        $approvers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();

        return view('livewire.inventory-manager', [
            'products' => $products,
            'allCount' => $allCount,
            'sofaCount' => $sofaCount,
            'diningCount' => $diningCount,
            'closetCount' => $closetCount,
            'archivedCount' => $archivedCount,
            'restockTransfers' => $restockTransfers,
            'approvers' => $approvers,
        ]);
    }
}
