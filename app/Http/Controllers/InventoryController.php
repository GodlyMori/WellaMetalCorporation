<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Batch store inventory items.
     * Validates incoming array defensively and rejects with HTTP 422 JSON if duplicates or invalid rows exist.
     */
    public function storeBatch(Request $request): JsonResponse
    {
        $prefix = $request->has('items') ? 'items' : 'entryItems';
        $items = $request->input('items', $request->input('entryItems', []));

        $result = $this->inventoryService->saveBatch($items, Auth::user(), $prefix);

        return response()->json([
            'success' => true,
            'message' => 'Inventory batch saved successfully.',
            'data' => $result,
        ], 201);
    }

    /**
     * Update product catalog details.
     * Enforces enterprise data integrity by strictly updating only static catalog fields
     * and rejecting/excluding any modifications to stock.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'spec_notes' => 'nullable|string|max:1000',
            'tagged_price' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $updatedProduct = $this->inventoryService->updateCatalogProduct($product, $validated);

        return response()->json([
            'success' => true,
            'message' => "Product '{$updatedProduct->name}' updated successfully.",
            'data' => $updatedProduct,
        ]);
    }
}
