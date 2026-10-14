<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedCategory = 'ALL';

    public string $stockFilter = 'all'; // all, in_stock, low_stock, out_of_stock

    // Top Section: Add Inventory Modal state & rows
    public bool $isFormOpen = false;

    public bool $showAddModal = false;

    public string $product_search = ''; // Search existing products to prefill/restock

    public array $entryItems = [
        ['product_id' => null, 'name' => '', 'category' => 'Sala Set', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '1', 'low_stock_threshold' => '5', 'is_existing' => false],
    ];

    // Edit Modal state & fields
    public bool $showEditModal = false;

    public ?int $editingProductId = null;

    public string $edit_name = '';

    public string $edit_category = '';

    public string $edit_description = '';

    public string $edit_price = '';

    public string $edit_stock = '';

    public string $edit_low_stock_threshold = '5';

    public string $edit_stock_reason = '';

    // Stock Request Modal (Secretary submits requisition)
    public bool $showRequestModal = false;

    public ?int $requestProductId = null;

    public string $requestProductName = '';

    public int $requestCurrentStock = 0;

    public int $requestQuantity = 5;

    public string $requestReason = 'Stock Replenishment';

    public string $requestNotes = '';

    // Stock Requests Hub Modal (GM/Admin approval + Delivery reception)
    public bool $showRequestsHubModal = false;

    public string $requestsTab = 'pending'; // 'pending', 'awaiting_delivery', 'history'

    public ?int $rejectingRequestId = null;

    public string $rejectionReasonInput = '';

    public string $receiveNotesInput = '';

    // Direct Restock Modal state & fields (Administrative fallback)
    public bool $showRestockModal = false;

    public ?int $restockProductId = null;

    public string $restockProductName = '';

    public int $restockQuantity = 5;

    public string $sourceBranch = 'Main Yard Production';

    public string $restockNotes = '';

    // Toast notification message
    public string $successMessage = '';

    public function mount()
    {
        if (request()->has('open_requests') || request()->has('open_hub')) {
            $this->showRequestsHubModal = true;
            $this->requestsTab = 'pending';
        }

        if (empty($this->entryItems)) {
            $this->resetEntryForm();
        }
    }

    public function setCategory($cat)
    {
        $this->selectedCategory = $cat;
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function setStockFilter($filter)
    {
        $this->stockFilter = $filter;
        $this->resetPage();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleForm(): void
    {
        $this->showAddModal = ! $this->showAddModal;
        $this->isFormOpen = $this->showAddModal;
    }

    public function openAddModal(): void
    {
        $this->resetValidation();
        $this->showAddModal = true;
    }

    public function closeAddModal(): void
    {
        $this->showAddModal = false;
        $this->resetValidation();
    }

    // Quick prefill from existing catalog product
    public function selectExistingProduct(int $productId, ?int $rowIndex = null)
    {
        $prod = Product::find($productId);
        if (! $prod) {
            return;
        }

        if ($rowIndex !== null && isset($this->entryItems[$rowIndex])) {
            $this->entryItems[$rowIndex]['is_existing'] = true;
            $this->entryItems[$rowIndex]['product_id'] = $prod->id;
            $this->entryItems[$rowIndex]['name'] = $prod->name;
            $this->entryItems[$rowIndex]['category'] = $prod->category;
            $this->entryItems[$rowIndex]['description'] = $prod->description ?? '';
            $this->entryItems[$rowIndex]['tagged_price'] = (string) $prod->tagged_price;
            $this->entryItems[$rowIndex]['quantity_in_stock'] = '1';
            $this->entryItems[$rowIndex]['low_stock_threshold'] = (string) ($prod->low_stock_threshold ?? 5);
        } else {
            // Find first empty row or append new row
            $targetIndex = null;
            foreach ($this->entryItems as $i => $item) {
                if (empty(trim($item['name'] ?? ''))) {
                    $targetIndex = $i;
                    break;
                }
            }

            if ($targetIndex !== null) {
                $this->entryItems[$targetIndex]['is_existing'] = true;
                $this->entryItems[$targetIndex]['product_id'] = $prod->id;
                $this->entryItems[$targetIndex]['name'] = $prod->name;
                $this->entryItems[$targetIndex]['category'] = $prod->category;
                $this->entryItems[$targetIndex]['description'] = $prod->description ?? '';
                $this->entryItems[$targetIndex]['tagged_price'] = (string) $prod->tagged_price;
                $this->entryItems[$targetIndex]['quantity_in_stock'] = '1';
                $this->entryItems[$targetIndex]['low_stock_threshold'] = (string) ($prod->low_stock_threshold ?? 5);
            } else {
                $this->entryItems[] = [
                    'is_existing' => true,
                    'product_id' => $prod->id,
                    'name' => $prod->name,
                    'category' => $prod->category,
                    'description' => $prod->description ?? '',
                    'tagged_price' => (string) $prod->tagged_price,
                    'quantity_in_stock' => '1',
                    'low_stock_threshold' => (string) ($prod->low_stock_threshold ?? 5),
                ];
            }
        }

        $this->product_search = '';
        $this->resetValidation();
    }

    public function addEntryRow(): void
    {
        $this->entryItems[] = [
            'product_id' => null,
            'is_existing' => false,
            'name' => '',
            'category' => 'Sala Set',
            'description' => '',
            'tagged_price' => '',
            'quantity_in_stock' => '1',
            'low_stock_threshold' => '5',
        ];
    }

    public function removeEntryRow(int $index): void
    {
        if (count($this->entryItems) > 1) {
            unset($this->entryItems[$index]);
            $this->entryItems = array_values($this->entryItems);
        } else {
            $this->resetEntryForm();
        }
        $this->resetValidation();
    }

    public function resetEntryForm(): void
    {
        $this->entryItems = [
            [
                'product_id' => null,
                'is_existing' => false,
                'name' => '',
                'category' => 'Sala Set',
                'description' => '',
                'tagged_price' => '',
                'quantity_in_stock' => '1',
                'low_stock_threshold' => '5',
            ],
        ];
        $this->product_search = '';
        $this->resetValidation();
    }

    /**
     * Real-time duplicate error messages keyed by row index.
     *
     * @return array<int, string>
     */
    public function getDuplicateErrors(): array
    {
        return app(InventoryService::class)->findDuplicateErrors($this->entryItems);
    }

    /**
     * Check if duplicate or incomplete rows exist to control save state.
     */
    public function getHasDuplicateOrIncompleteProperty(): bool
    {
        return app(InventoryService::class)->hasDuplicateOrIncompleteRows($this->entryItems);
    }

    public function saveInventoryEntries(InventoryService $inventoryService): void
    {
        $result = $inventoryService->saveBatch($this->entryItems, Auth::user());

        $this->resetEntryForm();
        $this->showAddModal = false;

        $newCount = $result['newCount'];
        $restockedCount = $result['restockedCount'];
        $updatedSummaries = $result['updatedSummaries'];

        if ($newCount > 0 && $restockedCount > 0) {
            $this->successMessage = "Inventory updated: {$newCount} new product(s) added, and {$restockedCount} existing item(s) restocked (".implode(', ', $updatedSummaries).').';
        } elseif ($restockedCount > 0) {
            $this->successMessage = "Stock added to {$restockedCount} existing product(s): ".implode(', ', $updatedSummaries).'.';
        } else {
            $this->successMessage = "{$newCount} new product(s) saved to inventory successfully.";
        }
    }

    public function editProduct(int $id)
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
        $this->edit_name = $product->name;
        $this->edit_category = $product->category;
        $this->edit_description = $product->description ?? '';
        $this->edit_price = (string) $product->tagged_price;
        $this->edit_stock = (string) $product->quantity_in_stock; // Display only
        $this->edit_low_stock_threshold = (string) ($product->low_stock_threshold ?? 5);
        $this->edit_stock_reason = '';
        $this->showEditModal = true;
    }

    public function updateProduct()
    {
        $this->validate([
            'edit_name' => 'required|string|max:255',
            'edit_category' => 'required|string',
            'edit_description' => 'nullable|string|max:1000',
            'edit_price' => ['required', 'numeric', 'min:0'],
            'edit_low_stock_threshold' => ['required', 'integer', 'min:0'],
        ], [
            'edit_name.required' => 'Product name is required.',
            'edit_price.required' => 'Price is required.',
            'edit_price.min' => 'Price cannot be negative.',
            'edit_low_stock_threshold.required' => 'Low stock threshold is required.',
            'edit_low_stock_threshold.min' => 'Threshold cannot be negative.',
        ]);

        $product = Product::findOrFail($this->editingProductId);
        $catId = Category::where('name', $this->edit_category)->value('id');

        // Quantity in stock is strictly NOT editable in Edit Product details (use Add Inventory or Stock Request Delivery)
        $product->update([
            'name' => trim($this->edit_name),
            'category' => $this->edit_category,
            'category_id' => $catId,
            'description' => $this->edit_description ?? '',
            'tagged_price' => (float) $this->edit_price,
            'low_stock_threshold' => (int) $this->edit_low_stock_threshold,
        ]);

        $this->showEditModal = false;
        $this->successMessage = "Product '{$product->name}' details updated successfully.";
    }

    // ==============================================================
    // STOCK REQUEST WORKFLOW (3-STAGE PROCUREMENT)
    // ==============================================================
    public function openRequestModal(int $id)
    {
        $product = Product::findOrFail($id);
        $this->requestProductId = $product->id;
        $this->requestProductName = $product->name;
        $this->requestCurrentStock = (int) $product->quantity_in_stock;
        $this->requestQuantity = 5;
        $this->requestReason = 'Stock Replenishment';
        $this->requestNotes = '';
        $this->showRequestModal = true;
    }

    public function submitStockRequest()
    {
        $this->validate([
            'requestQuantity' => 'required|integer|min:1',
            'requestReason' => 'required|string|max:255',
            'requestNotes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($this->requestProductId);
        $nextId = (StockRequest::max('id') ?? 0) + 1;
        $reqNumber = 'REQ-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        StockRequest::create([
            'request_number' => $reqNumber,
            'product_id' => $product->id,
            'requested_by' => Auth::id() ?? 1,
            'quantity_requested' => (int) $this->requestQuantity,
            'reason' => $this->requestReason,
            'notes' => $this->requestNotes ?: null,
            'status' => 'pending',
        ]);

        $this->showRequestModal = false;
        $this->successMessage = "Stock requisition {$reqNumber} for '{$product->name}' ({$this->requestQuantity} units) submitted to General Manager for approval.";
    }

    public function openRequestsHubModal(?string $tab = null)
    {
        if ($tab) {
            $this->requestsTab = $tab;
        }
        $this->rejectingRequestId = null;
        $this->rejectionReasonInput = '';
        $this->receiveNotesInput = '';
        $this->showRequestsHubModal = true;
    }

    public function approveStockRequest(int $requestId)
    {
        $user = Auth::user();
        $isAuthorized = $user && ($user->hasRole('admin') || $user->hasRole('manager'));

        if (! $isAuthorized) {
            session()->flash('request_error', 'Only the General Manager or Administrator can approve stock requisitions.');

            return;
        }

        $request = StockRequest::with('product')->findOrFail($requestId);

        if ($request->status !== 'pending') {
            session()->flash('request_error', 'This request is no longer in pending status.');

            return;
        }

        $request->update([
            'status' => 'awaiting_delivery',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->successMessage = "Requisition {$request->request_number} approved! Status updated to 'Awaiting Delivery'. Physical inventory will update upon delivery reception.";
    }

    public function openRejectForm(int $requestId)
    {
        $user = Auth::user();
        if (! $user || (! $user->hasRole('admin') && ! $user->hasRole('manager'))) {
            session()->flash('request_error', 'Only the General Manager or Administrator can reject stock requisitions.');

            return;
        }
        $this->rejectingRequestId = $requestId;
        $this->rejectionReasonInput = '';
    }

    public function cancelRejectForm()
    {
        $this->rejectingRequestId = null;
        $this->rejectionReasonInput = '';
    }

    public function rejectStockRequest(int $requestId)
    {
        $user = Auth::user();
        $isAuthorized = $user && ($user->hasRole('admin') || $user->hasRole('manager'));

        if (! $isAuthorized) {
            session()->flash('request_error', 'Only the General Manager or Administrator can reject stock requisitions.');

            return;
        }

        $this->validate([
            'rejectionReasonInput' => 'required|string|max:500',
        ], [
            'rejectionReasonInput.required' => 'Please provide a reason for rejecting this requisition.',
        ]);

        $request = StockRequest::findOrFail($requestId);
        $request->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $this->rejectionReasonInput,
        ]);

        $this->rejectingRequestId = null;
        $this->rejectionReasonInput = '';
        $this->successMessage = "Requisition {$request->request_number} was marked as rejected.";
    }

    public function receiveStockDelivery(int $requestId)
    {
        $user = Auth::user();
        $request = StockRequest::with('product')->findOrFail($requestId);

        if ($request->status !== 'awaiting_delivery') {
            session()->flash('request_error', 'Only requests in "Awaiting Delivery" status can be received.');

            return;
        }

        DB::transaction(function () use ($request, $user) {
            $product = Product::where('id', $request->product_id)->lockForUpdate()->firstOrFail();
            $oldStock = (int) $product->quantity_in_stock;
            $newStock = $oldStock + (int) $request->quantity_requested;

            $product->update([
                'quantity_in_stock' => $newStock,
                'status' => 'active',
            ]);

            $request->update([
                'status' => 'received',
                'received_by' => $user?->id ?? 1,
                'received_at' => now(),
                'received_notes' => $this->receiveNotesInput ?: 'Supplier delivery arrived at yard and confirmed',
            ]);

            InventoryAdjustment::create([
                'product_id' => $product->id,
                'user_id' => $user?->id ?? 1,
                'old_quantity' => $oldStock,
                'new_quantity' => $newStock,
                'quantity_change' => (int) $request->quantity_requested,
                'reason' => 'Stock Request Delivery Receipt',
                'notes' => "Requisition {$request->request_number}: Received {$request->quantity_requested} units of {$product->name} (Prev: {$oldStock}, New: {$newStock})",
            ]);
        });

        $this->receiveNotesInput = '';
        $this->successMessage = "Delivery received! Added {$request->quantity_requested} units of '{$request->product->name}' to stock.";
    }

    // Direct Restock Modal (Admin fallback)
    public function openRestockModal(int $id)
    {
        $product = Product::findOrFail($id);
        $this->restockProductId = $product->id;
        $this->restockProductName = $product->name;
        $this->restockQuantity = 5;
        $this->sourceBranch = 'Main Yard Production';
        $this->restockNotes = '';
        $this->showRestockModal = true;
    }

    public function saveRestock()
    {
        $this->validate([
            'restockQuantity' => 'required|integer|min:1',
            'sourceBranch' => 'required|string|max:100',
            'restockNotes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($this->restockProductId);
        $user = Auth::user();

        DB::transaction(function () use ($product, $user) {
            $transfer = StockTransfer::create([
                'source_branch' => $this->sourceBranch,
                'received_by' => $user?->id ?? 1,
                'date_received' => now(),
                'notes' => $this->restockNotes,
            ]);

            StockTransferItem::create([
                'stock_transfer_id' => $transfer->id,
                'product_id' => $product->id,
                'quantity_received' => (int) $this->restockQuantity,
            ]);

            $oldStock = (int) $product->quantity_in_stock;
            $newStock = $oldStock + (int) $this->restockQuantity;
            $product->update([
                'quantity_in_stock' => $newStock,
                'status' => 'active',
            ]);

            InventoryAdjustment::create([
                'product_id' => $product->id,
                'user_id' => $user?->id ?? 1,
                'old_quantity' => $oldStock,
                'new_quantity' => $newStock,
                'quantity_change' => (int) $this->restockQuantity,
                'reason' => 'Branch Restock Inflow',
                'notes' => "Received {$this->restockQuantity} units from {$this->sourceBranch} branch",
            ]);
        });

        $this->showRestockModal = false;
        $this->successMessage = "Restocked {$this->restockQuantity} units for '{$this->restockProductName}'.";
    }

    public function archiveProduct(int $id)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'status' => 'archived',
            'archived_by' => Auth::id(),
            'archive_approved_by' => Auth::id(),
            'archive_reason' => 'Direct archive from Inventory Manager',
        ]);
        $this->successMessage = "Product '{$product->name}' has been archived.";
    }

    public function restoreProduct(int $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'active']);
        $this->successMessage = "Product '{$product->name}' restored to active catalog.";
    }

    public function render()
    {
        // 1. Dynamic categories: active categories from Category management
        $activeCategoryNames = Category::whereNull('deleted_at')->where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        $existingCategories = ! empty($activeCategoryNames) ? $activeCategoryNames : Product::CATEGORIES;

        // 2. Category counts
        $allCount = Product::where('status', 'active')->count();
        $archivedCount = Product::where('status', 'archived')->count();

        $categoryCounts = [];
        foreach ($existingCategories as $cat) {
            $categoryCounts[$cat] = Product::where('status', 'active')->where('category', $cat)->count();
        }

        // 3. Search existing products for pre-fill / restock in Add Inventory section
        $searchedExistingProducts = [];
        if (strlen(trim($this->product_search)) >= 1) {
            $ps = '%'.trim($this->product_search).'%';
            $searchedExistingProducts = Product::where('status', 'active')
                ->where(function ($q) use ($ps) {
                    $q->where('name', 'like', $ps)
                        ->orWhere('description', 'like', $ps)
                        ->orWhere('category', 'like', $ps);
                })
                ->take(8)
                ->get();
        }

        // 4. Query for main product table
        $query = Product::query();

        if ($this->selectedCategory === 'ARCHIVED') {
            $query->where('status', 'archived');
        } else {
            $query->where('status', 'active');
            if ($this->selectedCategory !== 'ALL') {
                $query->where('category', $this->selectedCategory);
            }
        }

        if ($this->stockFilter === 'low_stock') {
            $query->where('quantity_in_stock', '>', 0)->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold');
        } elseif ($this->stockFilter === 'out_of_stock') {
            $query->where('quantity_in_stock', '<=', 0);
        } elseif ($this->stockFilter === 'in_stock') {
            $query->whereColumn('quantity_in_stock', '>', 'low_stock_threshold');
        }

        if (! empty($this->search)) {
            $s = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                    ->orWhere('description', 'like', $s)
                    ->orWhere('id', 'like', '%'.ltrim(trim($this->search), 'Pp0#').'%');
            });
        }

        $products = $query->orderBy('id', 'asc')->paginate(15);

        // 5. Stock requests data & counters
        $pendingRequestsCount = StockRequest::where('status', 'pending')->count();
        $awaitingDeliveryCount = StockRequest::where('status', 'awaiting_delivery')->count();

        $hubRequests = [];
        if ($this->showRequestsHubModal) {
            $hubQuery = StockRequest::with(['product', 'requester', 'approver', 'receiver']);
            if ($this->requestsTab === 'pending') {
                $hubQuery->where('status', 'pending');
            } elseif ($this->requestsTab === 'awaiting_delivery') {
                $hubQuery->where('status', 'awaiting_delivery');
            } else {
                $hubQuery->whereIn('status', ['received', 'rejected']);
            }
            $hubRequests = $hubQuery->orderBy('id', 'desc')->take(50)->get();
        }

        return view('livewire.inventory-manager', [
            'products' => $products,
            'existingCategories' => $existingCategories,
            'categoryCounts' => $categoryCounts,
            'allCount' => $allCount,
            'archivedCount' => $archivedCount,
            'searchedExistingProducts' => $searchedExistingProducts,
            'pendingRequestsCount' => $pendingRequestsCount,
            'awaitingDeliveryCount' => $awaitingDeliveryCount,
            'hubRequests' => $hubRequests,
            'duplicateErrors' => $this->getDuplicateErrors(),
            'hasDuplicateOrIncomplete' => $this->hasDuplicateOrIncomplete,
        ]);
    }
}
