<?php

namespace App\Livewire;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;
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

    // Top Section: Sliding Add Inventory Form state & rows
    public bool $isFormOpen = true; // Open by default
    public string $product_search = ''; // Search existing products to prefill/restock

    public array $entryItems = [
        ['name' => '', 'category' => 'Sala Set', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '1', 'low_stock_threshold' => '5'],
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

    // Restock Modal state & fields
    public bool $showRestockModal = false;
    public ?int $restockProductId = null;
    public string $restockProductName = '';
    public int $restockQuantity = 5;
    public string $sourceBranch = 'Toril';
    public string $restockNotes = '';

    // Toast notification message
    public string $successMessage = '';

    public function mount()
    {
        if (empty($this->entryItems)) {
            $this->resetEntryForm();
        }
    }

    public function setCategory($cat)
    {
        $this->selectedCategory = $cat;
        $this->resetPage();
    }

    public function setStockFilter($filter)
    {
        $this->stockFilter = $filter;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->isFormOpen = !$this->isFormOpen;
    }

    // Quick prefill from existing catalog product
    public function selectExistingProduct(int $productId, ?int $rowIndex = null)
    {
        $prod = Product::find($productId);
        if (!$prod) {
            return;
        }

        if ($rowIndex !== null && isset($this->entryItems[$rowIndex])) {
            $this->entryItems[$rowIndex]['name'] = $prod->name;
            $this->entryItems[$rowIndex]['category'] = $prod->category;
            $this->entryItems[$rowIndex]['description'] = $prod->description ?? '';
            $this->entryItems[$rowIndex]['tagged_price'] = (string)$prod->tagged_price;
            $this->entryItems[$rowIndex]['quantity_in_stock'] = '1';
            $this->entryItems[$rowIndex]['low_stock_threshold'] = (string)($prod->low_stock_threshold ?? 5);
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
                $this->entryItems[$targetIndex]['name'] = $prod->name;
                $this->entryItems[$targetIndex]['category'] = $prod->category;
                $this->entryItems[$targetIndex]['description'] = $prod->description ?? '';
                $this->entryItems[$targetIndex]['tagged_price'] = (string)$prod->tagged_price;
                $this->entryItems[$targetIndex]['quantity_in_stock'] = '1';
                $this->entryItems[$targetIndex]['low_stock_threshold'] = (string)($prod->low_stock_threshold ?? 5);
            } else {
                $this->entryItems[] = [
                    'name' => $prod->name,
                    'category' => $prod->category,
                    'description' => $prod->description ?? '',
                    'tagged_price' => (string)$prod->tagged_price,
                    'quantity_in_stock' => '1',
                    'low_stock_threshold' => (string)($prod->low_stock_threshold ?? 5),
                ];
            }
        }

        $this->product_search = '';
    }

    public function addEntryRow()
    {
        $this->entryItems[] = [
            'name' => '',
            'category' => 'Sala Set',
            'description' => '',
            'tagged_price' => '',
            'quantity_in_stock' => '1',
            'low_stock_threshold' => '5',
        ];
    }

    public function removeEntryRow(int $index)
    {
        if (count($this->entryItems) > 1) {
            unset($this->entryItems[$index]);
            $this->entryItems = array_values($this->entryItems);
        } else {
            $this->resetEntryForm();
        }
    }

    public function resetEntryForm()
    {
        $this->entryItems = [
            ['name' => '', 'category' => 'Sala Set', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '1', 'low_stock_threshold' => '5'],
        ];
        $this->product_search = '';
        $this->resetValidation();
    }

    public function saveInventoryEntries()
    {
        $this->validate([
            'entryItems.*.name' => 'required|string|max:255',
            'entryItems.*.category' => 'required|string',
            'entryItems.*.description' => 'nullable|string|max:1000',
            'entryItems.*.tagged_price' => ['required', 'numeric', 'min:0'],
            'entryItems.*.quantity_in_stock' => ['required', 'integer', 'min:1'],
            'entryItems.*.low_stock_threshold' => ['required', 'integer', 'min:0'],
        ], [
            'entryItems.*.name.required' => 'Product name is required for all item rows.',
            'entryItems.*.tagged_price.required' => 'Price is required for all item rows.',
            'entryItems.*.tagged_price.numeric' => 'Price must be a valid number.',
            'entryItems.*.tagged_price.min' => 'Product price cannot be negative.',
            'entryItems.*.quantity_in_stock.required' => 'Quantity is required.',
            'entryItems.*.quantity_in_stock.integer' => 'Quantity must be a whole number.',
            'entryItems.*.quantity_in_stock.min' => 'Quantity must be at least 1 unit.',
            'entryItems.*.low_stock_threshold.required' => 'Low stock threshold is required.',
            'entryItems.*.low_stock_threshold.integer' => 'Threshold must be a whole number.',
            'entryItems.*.low_stock_threshold.min' => 'Threshold cannot be negative.',
        ]);

        $user = Auth::user();
        $newCount = 0;
        $restockedCount = 0;
        $updatedSummaries = [];

        DB::transaction(function () use ($user, &$newCount, &$restockedCount, &$updatedSummaries) {
            foreach ($this->entryItems as $item) {
                $trimmedName = trim($item['name'] ?? '');
                if (empty($trimmedName)) {
                    continue;
                }

                $existingProduct = Product::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($trimmedName)])
                    ->lockForUpdate()
                    ->first();

                if ($existingProduct) {
                    $oldStock = (int)$existingProduct->quantity_in_stock;
                    $addedStock = (int)$item['quantity_in_stock'];
                    $newStock = $oldStock + $addedStock;

                    $updateData = [
                        'quantity_in_stock' => $newStock,
                        'status' => 'active',
                    ];

                    if (isset($item['tagged_price']) && is_numeric($item['tagged_price'])) {
                        $updateData['tagged_price'] = (float)$item['tagged_price'];
                    }

                    if (isset($item['low_stock_threshold']) && is_numeric($item['low_stock_threshold'])) {
                        $updateData['low_stock_threshold'] = (int)$item['low_stock_threshold'];
                    }

                    if (!empty($item['category'])) {
                        $updateData['category'] = $item['category'];
                    }

                    if (!empty(trim($item['description'] ?? ''))) {
                        $updateData['description'] = trim($item['description']);
                    }

                    $existingProduct->update($updateData);

                    InventoryAdjustment::create([
                        'product_id' => $existingProduct->id,
                        'user_id' => $user?->id ?? 1,
                        'old_quantity' => $oldStock,
                        'new_quantity' => $newStock,
                        'quantity_change' => $addedStock,
                        'reason' => 'Existing Product Stock Addition',
                        'notes' => "Added {$addedStock} units to existing catalog item '{$existingProduct->name}' via Inventory Entry (Previous: {$oldStock}, New Total: {$newStock})",
                    ]);

                    $restockedCount++;
                    $updatedSummaries[] = "{$existingProduct->name} (+{$addedStock} units)";
                } else {
                    $newProduct = Product::create([
                        'name' => $trimmedName,
                        'category' => $item['category'],
                        'description' => $item['description'] ?? '',
                        'tagged_price' => (float)$item['tagged_price'],
                        'quantity_in_stock' => (int)$item['quantity_in_stock'],
                        'low_stock_threshold' => (int)($item['low_stock_threshold'] ?? 5),
                        'status' => 'active',
                    ]);

                    InventoryAdjustment::create([
                        'product_id' => $newProduct->id,
                        'user_id' => $user?->id ?? 1,
                        'old_quantity' => 0,
                        'new_quantity' => (int)$item['quantity_in_stock'],
                        'quantity_change' => (int)$item['quantity_in_stock'],
                        'reason' => 'Initial Stock Creation',
                        'notes' => "Initial catalog entry of '{$newProduct->name}' with {$item['quantity_in_stock']} unit(s)",
                    ]);

                    $newCount++;
                }
            }
        });

        $this->resetEntryForm();

        if ($newCount > 0 && $restockedCount > 0) {
            $this->successMessage = "Inventory updated: {$newCount} new product(s) added, and {$restockedCount} existing item(s) restocked (" . implode(', ', $updatedSummaries) . ").";
        } elseif ($restockedCount > 0) {
            $this->successMessage = "Stock added to {$restockedCount} existing product(s): " . implode(', ', $updatedSummaries) . ".";
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
        $this->edit_price = (string)$product->tagged_price;
        $this->edit_stock = (string)$product->quantity_in_stock;
        $this->edit_low_stock_threshold = (string)($product->low_stock_threshold ?? 5);
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
            'edit_stock' => ['required', 'integer', 'min:0'],
            'edit_low_stock_threshold' => ['required', 'integer', 'min:0'],
            'edit_stock_reason' => 'nullable|string|max:255',
        ], [
            'edit_name.required' => 'Product name is required.',
            'edit_price.required' => 'Price is required.',
            'edit_price.min' => 'Price cannot be negative.',
            'edit_stock.required' => 'Quantity is required.',
            'edit_stock.min' => 'Quantity cannot be negative.',
            'edit_low_stock_threshold.required' => 'Low stock threshold is required.',
            'edit_low_stock_threshold.min' => 'Threshold cannot be negative.',
        ]);

        $product = Product::findOrFail($this->editingProductId);
        $oldStock = (int)$product->quantity_in_stock;
        $newStock = (int)$this->edit_stock;

        $user = Auth::user();

        DB::transaction(function () use ($product, $oldStock, $newStock, $user) {
            $product->update([
                'name' => trim($this->edit_name),
                'category' => $this->edit_category,
                'description' => $this->edit_description ?? '',
                'tagged_price' => (float)$this->edit_price,
                'quantity_in_stock' => $newStock,
                'low_stock_threshold' => (int)$this->edit_low_stock_threshold,
            ]);

            if ($oldStock !== $newStock) {
                InventoryAdjustment::create([
                    'product_id' => $product->id,
                    'user_id' => $user?->id ?? 1,
                    'old_quantity' => $oldStock,
                    'new_quantity' => $newStock,
                    'quantity_change' => $newStock - $oldStock,
                    'reason' => 'Stock Count Correction',
                    'notes' => $this->edit_stock_reason ?: 'Manual stock level adjustment via Inventory Manager',
                ]);
            }
        });

        $this->showEditModal = false;
        $this->successMessage = "Product '{$product->name}' updated successfully.";
    }

    public function openRestockModal(int $id)
    {
        $product = Product::findOrFail($id);
        $this->restockProductId = $product->id;
        $this->restockProductName = $product->name;
        $this->restockQuantity = 5;
        $this->sourceBranch = 'Toril';
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
                'quantity_received' => (int)$this->restockQuantity,
            ]);

            $oldStock = (int)$product->quantity_in_stock;
            $newStock = $oldStock + (int)$this->restockQuantity;
            $product->update([
                'quantity_in_stock' => $newStock,
                'status' => 'active',
            ]);

            InventoryAdjustment::create([
                'product_id' => $product->id,
                'user_id' => $user?->id ?? 1,
                'old_quantity' => $oldStock,
                'new_quantity' => $newStock,
                'quantity_change' => (int)$this->restockQuantity,
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
        // 1. Dynamic categories: standard furniture categories + any custom database categories
        $dbCategories = Product::whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
        $existingCategories = array_values(array_unique(array_merge(Product::CATEGORIES, $dbCategories)));

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
            $ps = '%' . trim($this->product_search) . '%';
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

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('description', 'like', $s)
                  ->orWhere('id', 'like', '%' . ltrim(trim($this->search), 'Pp0#') . '%');
            });
        }

        $products = $query->orderBy('id', 'asc')->paginate(15);

        return view('livewire.inventory-manager', [
            'products' => $products,
            'existingCategories' => $existingCategories,
            'categoryCounts' => $categoryCounts,
            'allCount' => $allCount,
            'archivedCount' => $archivedCount,
            'searchedExistingProducts' => $searchedExistingProducts,
        ]);
    }
}
