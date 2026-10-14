<?php

namespace App\Http\Controllers;

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
}
