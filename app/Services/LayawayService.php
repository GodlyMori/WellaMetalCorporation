<?php

namespace App\Services;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LayawayService
{
    /**
     * Process all expired layaway orders.
     * Restores deducted physical inventory back to products with audit records.
     * Marks sale status as cancelled while preserving payment history.
     */
    public function processExpiredLayaways(?int $processedByUserId = null): int
    {
        $expiredSales = Sale::where('status', 'layaway')
            ->whereNotNull('layaway_expires_at')
            ->whereDate('layaway_expires_at', '<', now()->toDateString())
            ->where('is_archived', false)
            ->get();

        $processedCount = 0;

        foreach ($expiredSales as $sale) {
            DB::transaction(function () use ($sale, $processedByUserId, &$processedCount) {
                $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->first();
                if (!$lockedSale || $lockedSale->status !== 'layaway') {
                    return;
                }

                // Restore reserved inventory for all sale items
                $items = $lockedSale->items;
                foreach ($items as $item) {
                    $lockedProduct = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if ($lockedProduct) {
                        $oldQty = $lockedProduct->quantity_in_stock;
                        $newQty = $oldQty + $item->quantity;
                        $lockedProduct->increment('quantity_in_stock', $item->quantity);

                        InventoryAdjustment::create([
                            'product_id' => $lockedProduct->id,
                            'user_id' => $processedByUserId,
                            'old_quantity' => $oldQty,
                            'new_quantity' => $newQty,
                            'quantity_change' => $item->quantity,
                            'reason' => 'Lay-Away Expiration Stock Restock',
                            'notes' => "Restored {$item->quantity} unit(s) due to expiration of lay-away order {$lockedSale->sale_number} (Expired: {$lockedSale->layaway_expires_at?->format('Y-m-d')})",
                        ]);
                    }
                }

                $expiryDateStr = $lockedSale->layaway_expires_at ? $lockedSale->layaway_expires_at->format('Y-m-d') : 'unknown date';
                $paidAmountStr = number_format((float)$lockedSale->amount_paid, 2);

                $auditNote = "\n[" . now()->format('Y-m-d H:i') . "] Lay-away expired on {$expiryDateStr}. Order cancelled and stock restored to inventory. Partial payments received (₱{$paidAmountStr}) preserved pending management disposition.";

                $lockedSale->update([
                    'status' => 'cancelled',
                    'notes' => ($lockedSale->notes ?? '') . $auditNote,
                ]);

                $processedCount++;
            });
        }

        return $processedCount;
    }
}
