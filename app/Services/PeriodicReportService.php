<?php

namespace App\Services;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PeriodicReportService
{
    /**
     * Compile periodic summary aggregates (Daily, Weekly, Monthly)
     * fulfilling Objective 3 by eliminating multi-day manual tallying.
     *
     * @return array{
     *     periodicity: string,
     *     start_date: string,
     *     end_date: string,
     *     buckets: array<int, array<string, mixed>>,
     *     totals: array<string, mixed>
     * }
     */
    public function getPeriodicSummary(
        string $periodicity = 'daily',
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $search = null
    ): array {
        $startDate = $startDate ?: now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: now()->endOfMonth()->toDateString();

        $query = Sale::with(['items.product', 'creator'])
            ->where('is_archived', false)
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        if (! empty($search)) {
            $s = '%'.trim($search).'%';
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', $s)
                    ->orWhere('customer_phone', 'like', $s)
                    ->orWhere('sale_number', 'like', $s)
                    ->orWhere('product_name', 'like', $s);
            });
        }

        $sales = $query->orderBy('sale_date', 'asc')->get();

        // Group into buckets
        $grouped = $sales->groupBy(function (Sale $sale) use ($periodicity) {
            $date = $sale->sale_date;
            if ($periodicity === 'weekly') {
                return $date->format('o-\WW'); // e.g. 2026-W41
            }
            if ($periodicity === 'monthly') {
                return $date->format('Y-m'); // e.g. 2026-10
            }

            return $date->format('Y-m-d'); // Daily
        });

        $buckets = [];
        foreach ($grouped as $bucketKey => $bucketSales) {
            /** @var Collection<int, Sale> $bucketSales */
            $completedSales = $bucketSales->where('status', 'completed');
            $layawaySales = $bucketSales->where('status', 'layaway');
            $cancelledSales = $bucketSales->where('status', 'cancelled');

            $completedRevenue = (float) $completedSales->sum('amount');
            $layawayDeposits = (float) $layawaySales->sum('amount_paid');
            $realizedCash = $completedRevenue + $layawayDeposits;
            $grossSalesVolume = (float) $completedSales->sum('amount') + (float) $layawaySales->sum('amount');
            $totalDiscounts = (float) $bucketSales->sum('discount_amount');
            $outstandingBalance = (float) $layawaySales->sum('remaining_balance');

            $activeOrdersCount = $completedSales->count() + $layawaySales->count();
            $averageTicket = $activeOrdersCount > 0 ? ($realizedCash / $activeOrdersCount) : 0.0;

            $buckets[] = [
                'bucket_key' => $bucketKey,
                'label' => $this->formatBucketLabel($bucketKey, $periodicity),
                'total_transactions' => $bucketSales->count(),
                'completed_count' => $completedSales->count(),
                'completed_revenue' => $completedRevenue,
                'layaway_count' => $layawaySales->count(),
                'layaway_deposits' => $layawayDeposits,
                'cancelled_count' => $cancelledSales->count(),
                'gross_sales_volume' => $grossSalesVolume,
                'realized_cash' => $realizedCash,
                'total_discounts' => $totalDiscounts,
                'outstanding_balance' => $outstandingBalance,
                'average_ticket' => $averageTicket,
            ];
        }

        // Global aggregate totals
        $allCompleted = $sales->where('status', 'completed');
        $allLayaways = $sales->where('status', 'layaway');
        $totalCompletedRevenue = (float) $allCompleted->sum('amount');
        $totalLayawayDeposits = (float) $allLayaways->sum('amount_paid');
        $totalRealizedCash = $totalCompletedRevenue + $totalLayawayDeposits;
        $totalGrossVolume = $totalCompletedRevenue + (float) $allLayaways->sum('amount');
        $totalDiscountsAll = (float) $sales->sum('discount_amount');
        $totalOutstandingReceivables = (float) $allLayaways->sum('remaining_balance');

        $activeOrdersTotal = $allCompleted->count() + $allLayaways->count();
        $overallAverageTicket = $activeOrdersTotal > 0 ? ($totalRealizedCash / $activeOrdersTotal) : 0.0;

        return [
            'periodicity' => $periodicity,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'buckets' => $buckets,
            'totals' => [
                'total_transactions' => $sales->count(),
                'completed_count' => $allCompleted->count(),
                'completed_revenue' => $totalCompletedRevenue,
                'layaway_count' => $allLayaways->count(),
                'layaway_deposits' => $totalLayawayDeposits,
                'realized_cash' => $totalRealizedCash,
                'gross_sales_volume' => $totalGrossVolume,
                'total_discounts' => $totalDiscountsAll,
                'outstanding_receivables' => $totalOutstandingReceivables,
                'average_ticket' => $overallAverageTicket,
            ],
        ];
    }

    /**
     * Compute Stock Turnover & Inventory Movements for the period.
     *
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     total_units_sold: int,
     *     total_restocked: int,
     *     overall_turnover_rate: float
     * }
     */
    public function getInventoryMovements(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?: now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: now()->endOfMonth()->toDateString();

        // 1. Units sold via sales in period
        $saleItems = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->where('is_archived', false)
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })->get();

        $unitsSoldByProduct = $saleItems->groupBy('product_id')->map(function ($items) {
            return [
                'quantity' => $items->sum('quantity'),
                'subtotal' => (float) $items->sum('subtotal'),
            ];
        });

        // 2. Restocks via positive inventory adjustments in period
        $adjustments = InventoryAdjustment::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $restockedByProduct = $adjustments->where('quantity_change', '>', 0)
            ->groupBy('product_id')
            ->map(fn ($list) => $list->sum('quantity_change'));

        // 3. Stock requests received in period
        $requests = StockRequest::where('status', 'received')
            ->whereDate('received_at', '>=', $startDate)
            ->whereDate('received_at', '<=', $endDate)
            ->get();

        $requestsByProduct = $requests->groupBy('product_id')->map(fn ($list) => $list->sum('quantity'));

        // 4. Products catalog compilation
        $products = Product::where('status', 'active')->orderBy('name', 'asc')->get();

        $items = [];
        $totalUnitsSold = 0;
        $totalRestocked = 0;
        $totalStockOnHand = $products->sum('quantity_in_stock');

        foreach ($products as $prod) {
            $soldData = $unitsSoldByProduct->get($prod->id, ['quantity' => 0, 'subtotal' => 0.0]);
            $soldQty = (int) $soldData['quantity'];
            $soldRevenue = (float) $soldData['subtotal'];

            $adjRestock = (int) $restockedByProduct->get($prod->id, 0);
            $reqRestock = (int) $requestsByProduct->get($prod->id, 0);
            $totalAdded = $adjRestock + $reqRestock;

            $totalUnitsSold += $soldQty;
            $totalRestocked += $totalAdded;

            // Velocity classification
            $velocity = 'Stagnant';
            if ($soldQty >= 10) {
                $velocity = 'High Velocity';
            } elseif ($soldQty >= 4) {
                $velocity = 'Moderate';
            } elseif ($soldQty > 0) {
                $velocity = 'Slow-moving';
            }

            $items[] = [
                'product_id' => $prod->id,
                'formatted_id' => $prod->formatted_id,
                'name' => $prod->name,
                'category' => $prod->category,
                'tagged_price' => (float) $prod->tagged_price,
                'current_stock' => (int) $prod->quantity_in_stock,
                'units_sold' => $soldQty,
                'revenue_generated' => $soldRevenue,
                'units_added' => $totalAdded,
                'velocity' => $velocity,
            ];
        }

        // Sort items: highest units sold first, then lowest stock
        usort($items, function ($a, $b) {
            if ($b['units_sold'] === $a['units_sold']) {
                return $a['current_stock'] <=> $b['current_stock'];
            }

            return $b['units_sold'] <=> $a['units_sold'];
        });

        $overallTurnoverRate = ($totalStockOnHand + $totalUnitsSold) > 0
            ? round(($totalUnitsSold / ($totalStockOnHand + $totalUnitsSold)) * 100, 1)
            : 0.0;

        return [
            'items' => $items,
            'total_units_sold' => $totalUnitsSold,
            'total_restocked' => $totalRestocked,
            'overall_turnover_rate' => $overallTurnoverRate,
        ];
    }

    /**
     * Retrieve individual traceable transactions matching periodic selection,
     * allowing the GM and CEO to cross-reference summary figures with receipts.
     */
    public function getTraceableSales(
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $search = null,
        ?string $status = null,
        int $limit = 100
    ): Collection {
        $startDate = $startDate ?: now()->startOfMonth()->toDateString();
        $endDate = $endDate ?: now()->endOfMonth()->toDateString();

        $query = Sale::with(['items.product', 'creator', 'customer'])
            ->where('is_archived', false)
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $s = '%'.trim($search).'%';
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', $s)
                    ->orWhere('customer_phone', 'like', $s)
                    ->orWhere('sale_number', 'like', $s)
                    ->orWhere('product_name', 'like', $s);
            });
        }

        return $query->orderBy('sale_date', 'desc')->take($limit)->get();
    }

    /**
     * Format periodic bucket keys into clear, executive-grade labels.
     */
    protected function formatBucketLabel(string $key, string $periodicity): string
    {
        if ($periodicity === 'monthly') {
            return Carbon::createFromFormat('Y-m', $key)->format('F Y');
        }

        if ($periodicity === 'weekly') {
            // e.g. 2026-W41
            if (preg_match('/^(\d{4})-W(\d{2})$/', $key, $m)) {
                $year = (int) $m[1];
                $week = (int) $m[2];
                $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
                $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

                return "Week {$week} ({$startOfWeek->format('M d')} - {$endOfWeek->format('M d, Y')})";
            }

            return $key;
        }

        // Daily
        return Carbon::parse($key)->format('D, M j, Y');
    }
}
