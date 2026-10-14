<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Normalize an entry item into a unique signature key.
     * Uses existing product ID if present, otherwise normalized name + category + specs.
     *
     * @param  array<string, mixed>  $item
     */
    public function generateItemSignature(array $item): ?string
    {
        if (! empty($item['product_id'])) {
            return 'id:'.(int) $item['product_id'];
        }

        $name = trim(strtolower((string) ($item['name'] ?? '')));
        if ($name === '') {
            return null;
        }

        $category = trim(strtolower((string) ($item['category'] ?? '')));
        $specs = trim(strtolower((string) ($item['description'] ?? '')));

        return $name.'|'.$category.'|'.$specs;
    }

    /**
     * Check incoming batch items for duplicate entries.
     * Returns an array of formatted error messages keyed by 0-based row index.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, string>
     */
    public function findDuplicateErrors(array $items): array
    {
        $seen = [];
        $errors = [];

        foreach ($items as $index => $item) {
            $sig = $this->generateItemSignature($item);
            if ($sig === null) {
                continue;
            }

            if (isset($seen[$sig])) {
                $firstRowIndex = $seen[$sig];
                $firstDisplayNumber = $firstRowIndex + 1;
                $errors[$index] = "Duplicate entry: This product is already entered in Item #{$firstDisplayNumber}. Adjust quantity there instead.";
            } else {
                $seen[$sig] = $index;
            }
        }

        return $errors;
    }

    /**
     * Determine whether the given item rows contain any incomplete or duplicate fields.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public function hasDuplicateOrIncompleteRows(array $items): bool
    {
        if (empty($items)) {
            return true;
        }

        if (! empty($this->findDuplicateErrors($items))) {
            return true;
        }

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $price = $item['tagged_price'] ?? '';
            $qty = $item['quantity_in_stock'] ?? '';

            if ($name === '') {
                return true;
            }
            if ($price === '' || ! is_numeric($price) || (float) $price < 0) {
                return true;
            }
            if ($qty === '' || ! is_numeric($qty) || (int) $qty < 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the entire batch payload defensively before persisting.
     * Throws ValidationException matching HTTP 422 JSON schema.
     *
     * @param  array<int, array<string, mixed>>  $items
     *
     * @throws ValidationException
     */
    public function validateBatch(array $items, string $keyPrefix = 'entryItems'): void
    {
        if (empty($items)) {
            throw ValidationException::withMessages([
                $keyPrefix => ['Inventory batch must contain at least one item.'],
            ]);
        }

        $errors = [];

        // 1. Check duplicate item collisions across rows
        $duplicateErrors = $this->findDuplicateErrors($items);
        foreach ($duplicateErrors as $index => $msg) {
            $errors["{$keyPrefix}.{$index}"] = [$msg];
            // Format strictly compatible with HTTP 422 {"errors": {"items.1": ["Duplicate product row in payload."]}}
            $errors["items.{$index}"] = ['Duplicate product row in payload.'];
        }

        // 2. Defensive field validations for each row
        foreach ($items as $index => $item) {
            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') {
                $errors["{$keyPrefix}.{$index}.name"] = ['Product name is required for all item rows.'];
            }

            $price = $item['tagged_price'] ?? null;
            if ($price === null || $price === '' || ! is_numeric($price) || (float) $price < 0) {
                $errors["{$keyPrefix}.{$index}.tagged_price"] = ['Price must be a valid non-negative number.'];
            }

            $qty = $item['quantity_in_stock'] ?? null;
            if ($qty === null || $qty === '' || ! is_numeric($qty) || (int) $qty < 1) {
                $errors["{$keyPrefix}.{$index}.quantity_in_stock"] = ['Quantity must be at least 1 unit.'];
            }

            $threshold = $item['low_stock_threshold'] ?? null;
            if ($threshold !== null && $threshold !== '' && (! is_numeric($threshold) || (int) $threshold < 0)) {
                $errors["{$keyPrefix}.{$index}.low_stock_threshold"] = ['Threshold cannot be negative.'];
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Persist batch entries with ACID transactional safety and lock-for-update.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{newCount: int, restockedCount: int, updatedSummaries: array<string>}
     *
     * @throws ValidationException|\Throwable
     */
    public function saveBatch(array $items, ?User $user = null, string $keyPrefix = 'entryItems'): array
    {
        // 1. Defensive validation before beginning transaction
        $this->validateBatch($items, $keyPrefix);

        $newCount = 0;
        $restockedCount = 0;
        $updatedSummaries = [];

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $trimmedName = trim((string) ($item['name'] ?? ''));
                if ($trimmedName === '') {
                    continue;
                }

                $productId = ! empty($item['product_id']) ? (int) $item['product_id'] : null;

                $existingProduct = null;
                if ($productId) {
                    $existingProduct = Product::where('id', $productId)->lockForUpdate()->first();
                }

                if (! $existingProduct) {
                    $existingProduct = Product::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($trimmedName)])
                        ->lockForUpdate()
                        ->first();
                }

                if ($existingProduct) {
                    $oldStock = (int) $existingProduct->quantity_in_stock;
                    $addedStock = (int) $item['quantity_in_stock'];
                    $newStock = $oldStock + $addedStock;

                    $existingProduct->update([
                        'quantity_in_stock' => $newStock,
                        'status' => 'active',
                    ]);

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
                    $catId = Category::where('name', $item['category'])->value('id');
                    $newProduct = Product::create([
                        'name' => $trimmedName,
                        'category' => $item['category'],
                        'category_id' => $catId,
                        'description' => $item['description'] ?? '',
                        'tagged_price' => (float) $item['tagged_price'],
                        'quantity_in_stock' => (int) $item['quantity_in_stock'],
                        'low_stock_threshold' => (int) ($item['low_stock_threshold'] ?? 5),
                        'status' => 'active',
                    ]);

                    InventoryAdjustment::create([
                        'product_id' => $newProduct->id,
                        'user_id' => $user?->id ?? 1,
                        'old_quantity' => 0,
                        'new_quantity' => (int) $item['quantity_in_stock'],
                        'quantity_change' => (int) $item['quantity_in_stock'],
                        'reason' => 'Initial Stock Creation',
                        'notes' => "Initial catalog entry of '{$newProduct->name}' with {$item['quantity_in_stock']} unit(s)",
                    ]);

                    $newCount++;
                }
            }

            DB::commit();

            return [
                'newCount' => $newCount,
                'restockedCount' => $restockedCount,
                'updatedSummaries' => $updatedSummaries,
            ];
        } catch (ValidationException $ve) {
            DB::rollBack();
            throw $ve;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Inventory batch transaction failed: '.$e->getMessage(), [
                'exception' => $e,
                'items' => $items,
            ]);

            throw ValidationException::withMessages([
                'general' => 'Database collision or unexpected error during batch insertion. Transaction was safely rolled back: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Update static catalog fields for a product, strictly excluding stock quantity
     * to enforce enterprise-grade data integrity and audit compliance.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function updateCatalogProduct(Product $product, array $attributes): Product
    {
        // Explicitly strip any attempt to modify stock quantity or category once created
        unset(
            $attributes['quantity_in_stock'],
            $attributes['stock'],
            $attributes['edit_stock'],
            $attributes['category_id'],
            $attributes['category'],
            $attributes['edit_category']
        );

        $allowedAttributes = [
            'name' => trim((string) ($attributes['name'] ?? $product->name)),
            'description' => trim((string) ($attributes['description'] ?? $attributes['spec_notes'] ?? $product->description ?? '')),
            'tagged_price' => (float) ($attributes['tagged_price'] ?? $attributes['unit_price'] ?? $product->tagged_price),
            'low_stock_threshold' => (int) ($attributes['low_stock_threshold'] ?? $product->low_stock_threshold ?? 5),
        ];

        $product->update($allowedAttributes);

        return $product;
    }
}
