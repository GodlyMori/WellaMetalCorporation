<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AddInventory extends Component
{
    public string $product_search = '';

    public array $entryItems = [
        ['product_id' => null, 'name' => '', 'category' => 'Sala Set', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '1', 'low_stock_threshold' => '5', 'is_existing' => false],
    ];

    public string $successMessage = '';

    public string $errorMessage = '';

    public bool $hasDuplicateOrIncomplete = false;

    public function mount(): void
    {
        if (empty($this->entryItems)) {
            $this->resetEntryForm();
        }
    }

    public function resetEntryForm(): void
    {
        $this->entryItems = [
            ['product_id' => null, 'name' => '', 'category' => 'Sala Set', 'description' => '', 'tagged_price' => '', 'quantity_in_stock' => '1', 'low_stock_threshold' => '5', 'is_existing' => false],
        ];
        $this->product_search = '';
        $this->resetErrorBag();
        $this->hasDuplicateOrIncomplete = false;
    }

    public function addEntryRow(): void
    {
        $this->entryItems[] = [
            'product_id' => null,
            'name' => '',
            'category' => 'Sala Set',
            'description' => '',
            'tagged_price' => '',
            'quantity_in_stock' => '1',
            'low_stock_threshold' => '5',
            'is_existing' => false,
        ];
    }

    public function removeEntryRow(int $index): void
    {
        if (isset($this->entryItems[$index])) {
            unset($this->entryItems[$index]);
            $this->entryItems = array_values($this->entryItems);
        }

        if (empty($this->entryItems)) {
            $this->resetEntryForm();
        }
    }

    public function selectExistingProduct(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        // Find the first unfilled new-item row to populate, or append a row
        $targetIndex = null;
        foreach ($this->entryItems as $idx => $row) {
            if (empty($row['product_id']) && empty($row['name']) && empty($row['is_existing'])) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === null) {
            $targetIndex = count($this->entryItems);
            $this->addEntryRow();
        }

        $this->entryItems[$targetIndex] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'category' => $product->category,
            'description' => $product->description ?? '',
            'tagged_price' => (string) $product->tagged_price,
            'quantity_in_stock' => '1',
            'low_stock_threshold' => (string) $product->low_stock_threshold,
            'is_existing' => true,
        ];

        $this->product_search = '';
    }

    public function saveInventoryEntries(InventoryService $service): void
    {
        $this->successMessage = '';
        $this->errorMessage = '';

        try {
            $result = $service->saveBatch($this->entryItems, Auth::user(), 'entryItems');

            $countNew = $result['newCount'] ?? 0;
            $countRestocked = $result['restockedCount'] ?? 0;
            $totalUpdated = $countNew + $countRestocked;

            $parts = [];
            if ($countNew > 0) {
                $parts[] = "{$countNew} new product(s) added";
            }
            if ($countRestocked > 0) {
                $parts[] = "{$countRestocked} existing product(s) restocked";
            }

            $summaryText = ! empty($parts) ? implode(' and ', $parts) : "{$totalUpdated} item(s) processed";
            $this->successMessage = "{$summaryText} successfully!";

            $this->resetEntryForm();
        } catch (ValidationException $e) {
            $this->setErrorBag($e->validator->getMessageBag());
            $this->errorMessage = 'Please review the highlighted row errors before saving.';
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to save inventory: '.$e->getMessage();
        }
    }

    public function getDuplicateErrors(): array
    {
        return app(InventoryService::class)->findDuplicateErrors($this->entryItems);
    }

    public function render()
    {
        $duplicateErrors = $this->getDuplicateErrors();
        $this->hasDuplicateOrIncomplete = app(InventoryService::class)->hasDuplicateOrIncompleteRows($this->entryItems);

        $searchedExistingProducts = [];
        if (! empty(trim($this->product_search))) {
            $term = '%'.trim($this->product_search).'%';
            $searchedExistingProducts = Product::where('status', 'active')
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('description', 'like', $term);
                })
                ->take(6)
                ->get();
        }

        $loadedCategories = Category::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        if (empty($loadedCategories)) {
            $dbCategories = Product::where('status', 'active')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->toArray();
            $existingCategories = array_values(array_unique(array_merge(Product::CATEGORIES, $dbCategories)));
        } else {
            $existingCategories = $loadedCategories;
        }

        return view('livewire.add-inventory', [
            'searchedExistingProducts' => $searchedExistingProducts,
            'existingCategories' => $existingCategories,
            'duplicateErrors' => $duplicateErrors,
        ]);
    }
}
