<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;

class BusinessIntelligenceService
{
    /**
     * Resolve date boundary from preset.
     */
    public function resolveDateRange(string $preset): array
    {
        $now = Carbon::now();

        return match ($preset) {
            'last_30_days' => [
                'start' => $now->copy()->subDays(30)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Last 30 Days',
            ],
            'this_quarter' => [
                'start' => $now->copy()->firstOfQuarter()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'This Quarter',
            ],
            'last_year' => [
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
                'label' => 'Last Year ('.($now->year - 1).')',
            ],
            'all_time' => [
                'start' => Carbon::parse('2020-01-01')->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'All-Time Records',
            ],
            default => [ // 'this_year'
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Year-to-Date ('.$now->year.')',
            ],
        };
    }

    /**
     * Get complete Executive Business Intelligence metrics.
     */
    public function getExecutiveMetrics(string $datePreset = 'this_year'): array
    {
        $range = $this->resolveDateRange($datePreset);
        $start = $range['start'];
        $end = $range['end'];

        $salesQuery = Sale::where('is_archived', false);
        if ($datePreset !== 'all_time') {
            $salesQuery->whereDate('sale_date', '>=', $start->toDateString())
                ->whereDate('sale_date', '<=', $end->toDateString());
        }

        // 1. Commercial Top-Level KPIs
        $validSales = (clone $salesQuery)->where('status', '!=', 'cancelled');
        $completedSales = (clone $salesQuery)->where('status', 'completed');
        $layawaySales = (clone $salesQuery)->where('status', 'layaway');

        $totalInvoicedAmount = (float) (clone $validSales)->sum('amount');
        $realizedCashCollected = (float) (clone $validSales)->sum('amount_paid');
        $outstandingLayawayReceivables = (float) (clone $layawaySales)->sum('remaining_balance');
        $completedCount = (clone $completedSales)->count();
        $totalOrdersCount = (clone $validSales)->count();
        $averageOrderValue = $totalOrdersCount > 0 ? ($totalInvoicedAmount / $totalOrdersCount) : 0.0;

        // 2. Caravan Pricing Negotiation & Concession Analytics
        $negotiationAnalytics = $this->calculateNegotiationAnalytics($start, $end, $datePreset);

        // 3. ABC Inventory Classification & Dead Stock Working Capital
        $inventoryAbc = $this->calculateInventoryAbcAnalysis();

        // 4. Layaway Conversion & Portfolio Health
        $layawayConversion = $this->calculateLayawayConversionMetrics();

        // 5. Customer Concentration & Lifetime Value (LTV)
        $customerIntelligence = $this->calculateCustomerIntelligence($start, $end);

        // 6. 12-Month Trajectory for ApexCharts
        $trajectory = $this->calculateRevenueTrajectory();

        return [
            'date_range' => $range,
            'kpis' => [
                'total_invoiced_amount' => $totalInvoicedAmount,
                'realized_cash_collected' => $realizedCashCollected,
                'outstanding_layaway_receivables' => $outstandingLayawayReceivables,
                'completed_count' => $completedCount,
                'total_orders_count' => $totalOrdersCount,
                'average_order_value' => $averageOrderValue,
                'gross_gmv' => $negotiationAnalytics['gross_srp_total'],
                'total_concession_amount' => $negotiationAnalytics['total_concessions'],
                'concession_erosion_rate' => $negotiationAnalytics['concession_rate'],
                'dead_stock_capital' => $inventoryAbc['dead_stock_capital'],
                'dead_stock_count' => $inventoryAbc['dead_stock_count'],
                'layaway_settlement_rate' => $layawayConversion['settlement_rate'],
                'repeat_customer_rate' => $customerIntelligence['repeat_customer_rate'],
            ],
            'negotiations' => $negotiationAnalytics,
            'inventory_abc' => $inventoryAbc,
            'layaways' => $layawayConversion,
            'customers' => $customerIntelligence,
            'trajectory' => $trajectory,
        ];
    }

    /**
     * Compute Caravan price negotiation and discount erosion.
     */
    protected function calculateNegotiationAnalytics(Carbon $start, Carbon $end, string $datePreset = 'this_year'): array
    {
        $saleItems = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($q) use ($start, $end, $datePreset) {
                $q->where('is_archived', false)
                    ->where('status', '!=', 'cancelled');

                if ($datePreset !== 'all_time') {
                    $q->whereDate('sale_date', '>=', $start->toDateString())
                        ->whereDate('sale_date', '<=', $end->toDateString());
                }
            })
            ->get();

        $grossSrpTotal = 0.0;
        $realizedItemTotal = 0.0;
        $totalConcessions = 0.0;
        $negotiatedItemsCount = 0;
        $totalItemsCount = 0;
        $categoryConcessions = [];

        foreach ($saleItems as $item) {
            $qty = (int) $item->quantity;
            $unitPrice = (float) $item->unit_price;
            $srp = (float) ($item->original_price ?? $unitPrice);
            $cat = $item->product?->category ?? 'General Metalcraft';

            $itemGrossSrp = $srp * $qty;
            $itemRealized = $unitPrice * $qty;
            $concession = max(0.0, $srp - $unitPrice) * $qty;

            $grossSrpTotal += $itemGrossSrp;
            $realizedItemTotal += $itemRealized;
            $totalConcessions += $concession;
            $totalItemsCount += $qty;

            if ($concession > 0.001) {
                $negotiatedItemsCount += $qty;
            }

            if (! isset($categoryConcessions[$cat])) {
                $categoryConcessions[$cat] = [
                    'category' => $cat,
                    'gross_srp' => 0.0,
                    'realized' => 0.0,
                    'concessions' => 0.0,
                    'items_sold' => 0,
                ];
            }

            $categoryConcessions[$cat]['gross_srp'] += $itemGrossSrp;
            $categoryConcessions[$cat]['realized'] += $itemRealized;
            $categoryConcessions[$cat]['concessions'] += $concession;
            $categoryConcessions[$cat]['items_sold'] += $qty;
        }

        // Calculate erosion percentage per category
        $categoryTolerance = [];
        foreach ($categoryConcessions as $cat => $data) {
            $rate = $data['gross_srp'] > 0 ? ($data['concessions'] / $data['gross_srp']) * 100 : 0.0;
            $categoryTolerance[] = [
                'category' => $cat,
                'gross_srp' => $data['gross_srp'],
                'realized' => $data['realized'],
                'concessions' => $data['concessions'],
                'items_sold' => $data['items_sold'],
                'concession_rate' => round($rate, 1),
            ];
        }

        // Sort categories by highest concession amount
        usort($categoryTolerance, fn ($a, $b) => $b['concessions'] <=> $a['concessions']);

        $concessionRate = $grossSrpTotal > 0 ? ($totalConcessions / $grossSrpTotal) * 100 : 0.0;
        $negotiatedRatio = $totalItemsCount > 0 ? ($negotiatedItemsCount / $totalItemsCount) * 100 : 0.0;

        return [
            'gross_srp_total' => $grossSrpTotal,
            'realized_total' => $realizedItemTotal,
            'total_concessions' => $totalConcessions,
            'concession_rate' => round($concessionRate, 2),
            'negotiated_items_count' => $negotiatedItemsCount,
            'total_items_count' => $totalItemsCount,
            'negotiated_ratio' => round($negotiatedRatio, 1),
            'categories' => $categoryTolerance,
        ];
    }

    /**
     * ABC Analysis of Inventory & Dead Stock Capital Detection.
     */
    protected function calculateInventoryAbcAnalysis(): array
    {
        // 1. Get product total historical sales revenue
        $productSales = SaleItem::whereHas('sale', function ($q) {
            $q->where('is_archived', false)->where('status', '!=', 'cancelled');
        })
            ->selectRaw('product_id, SUM(subtotal) as total_revenue, SUM(quantity) as units_sold')
            ->groupBy('product_id')
            ->pluck('total_revenue', 'product_id')
            ->toArray();

        $activeProducts = Product::where('status', 'active')->get();
        $totalHistoricalRevenue = array_sum($productSales);

        $classifiedProducts = [];
        $deadStockProducts = [];
        $deadStockCapital = 0.0;

        // Determine dead stock: active products with zero sales in last 60 days or zero all-time sales
        $recentSoldProductIds = SaleItem::whereHas('sale', function ($q) {
            $q->where('is_archived', false)
                ->where('status', '!=', 'cancelled')
                ->where('sale_date', '>=', now()->subDays(60)->toDateString());
        })->distinct()->pluck('product_id')->toArray();

        foreach ($activeProducts as $p) {
            $revenue = (float) ($productSales[$p->id] ?? 0.0);
            $stock = (int) $p->quantity_in_stock;
            $unitPrice = (float) $p->tagged_price;
            $valuation = $stock * $unitPrice;

            $isDead = ($stock > 0) && ! in_array($p->id, $recentSoldProductIds);
            if ($isDead) {
                $deadStockProducts[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => $p->category ?? 'Furniture',
                    'stock' => $stock,
                    'unit_price' => $unitPrice,
                    'tied_capital' => $valuation,
                    'all_time_revenue' => $revenue,
                ];
                $deadStockCapital += $valuation;
            }

            $classifiedProducts[] = [
                'id' => $p->id,
                'name' => $p->name,
                'category' => $p->category ?? 'Furniture',
                'stock' => $stock,
                'unit_price' => $unitPrice,
                'revenue' => $revenue,
                'valuation' => $valuation,
            ];
        }

        // Sort descending by revenue for Pareto ABC distribution
        usort($classifiedProducts, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        $cumulative = 0.0;
        $classA = [];
        $classB = [];
        $classC = [];

        foreach ($classifiedProducts as $item) {
            $cumulative += $item['revenue'];
            $pct = $totalHistoricalRevenue > 0 ? ($cumulative / $totalHistoricalRevenue) * 100 : 100;

            if ($pct <= 80 || empty($classA)) {
                $classA[] = $item;
            } elseif ($pct <= 95 || empty($classB)) {
                $classB[] = $item;
            } else {
                $classC[] = $item;
            }
        }

        usort($deadStockProducts, fn ($a, $b) => $b['tied_capital'] <=> $a['tied_capital']);

        return [
            'total_catalog_products' => $activeProducts->count(),
            'class_a_count' => count($classA),
            'class_b_count' => count($classB),
            'class_c_count' => count($classC),
            'class_a_revenue' => array_sum(array_column($classA, 'revenue')),
            'class_b_revenue' => array_sum(array_column($classB, 'revenue')),
            'class_c_revenue' => array_sum(array_column($classC, 'revenue')),
            'dead_stock_count' => count($deadStockProducts),
            'dead_stock_capital' => $deadStockCapital,
            'top_class_a' => array_slice($classA, 0, 5),
            'dead_stock_items' => array_slice($deadStockProducts, 0, 6),
        ];
    }

    /**
     * Compute Layaway conversion, settlement velocity, and default rate.
     */
    protected function calculateLayawayConversionMetrics(): array
    {
        $allLayawaySales = Sale::where('is_archived', false)
            ->where(function ($q) {
                $q->where('payment_type', 'layaway')
                    ->orWhere('downpayment_amount', '>', 0);
            })->get();

        $totalHistoricalLayaways = $allLayawaySales->count();
        $settledLayaways = $allLayawaySales->where('status', 'completed');
        $activeLayaways = $allLayawaySales->where('status', 'layaway');
        $cancelledLayaways = $allLayawaySales->where('status', 'cancelled');

        $settledCount = $settledLayaways->count();
        $activeCount = $activeLayaways->count();
        $cancelledCount = $cancelledLayaways->count();

        // Calculate settled conversion rate
        $settlementRate = $totalHistoricalLayaways > 0
            ? ($settledCount / $totalHistoricalLayaways) * 100
            : 0.0;

        $cancellationRate = $totalHistoricalLayaways > 0
            ? ($cancelledCount / $totalHistoricalLayaways) * 100
            : 0.0;

        $totalCollectedFromLayaways = $allLayawaySales->sum('amount_paid');
        $activeReceivablesBalance = $activeLayaways->sum('remaining_balance');

        return [
            'total_historical' => $totalHistoricalLayaways,
            'settled_count' => $settledCount,
            'active_count' => $activeCount,
            'cancelled_count' => $cancelledCount,
            'settlement_rate' => round($settlementRate, 1),
            'cancellation_rate' => round($cancellationRate, 1),
            'total_layaway_revenue' => (float) $totalCollectedFromLayaways,
            'active_receivables' => (float) $activeReceivablesBalance,
        ];
    }

    /**
     * Customer segmentation, Repeat Buyer Rate, and Top 10 VIP LTV Ranking.
     */
    protected function calculateCustomerIntelligence(Carbon $start, Carbon $end): array
    {
        $sales = Sale::where('is_archived', false)
            ->where('status', '!=', 'cancelled')
            ->get();

        $customerMap = [];
        foreach ($sales as $s) {
            $identifier = $s->customer_id ? ('id_'.$s->customer_id) : ('phone_'.($s->customer_phone ?: $s->customer_name));
            $name = $s->customer_name ?: 'Valued Customer';
            $phone = $s->customer_phone ?: '—';

            if (! isset($customerMap[$identifier])) {
                $customerMap[$identifier] = [
                    'identifier' => $identifier,
                    'customer_id' => $s->customer_id,
                    'name' => $name,
                    'phone' => $phone,
                    'orders_count' => 0,
                    'total_spend' => 0.0,
                    'cash_paid' => 0.0,
                    'first_order' => $s->sale_date,
                    'last_order' => $s->sale_date,
                ];
            }

            $customerMap[$identifier]['orders_count']++;
            $customerMap[$identifier]['total_spend'] += (float) $s->amount;
            $customerMap[$identifier]['cash_paid'] += (float) $s->amount_paid;

            if ($s->sale_date < $customerMap[$identifier]['first_order']) {
                $customerMap[$identifier]['first_order'] = $s->sale_date;
            }
            if ($s->sale_date > $customerMap[$identifier]['last_order']) {
                $customerMap[$identifier]['last_order'] = $s->sale_date;
            }
        }

        $totalCustomers = count($customerMap);
        $repeatCustomers = 0;
        foreach ($customerMap as $c) {
            if ($c['orders_count'] >= 2) {
                $repeatCustomers++;
            }
        }

        $repeatRate = $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0.0;

        // Rank by Lifetime Value (paid cash)
        usort($customerMap, fn ($a, $b) => $b['cash_paid'] <=> $a['cash_paid']);
        $topVipCustomers = array_slice($customerMap, 0, 10);

        return [
            'total_unique_customers' => $totalCustomers,
            'repeat_customers_count' => $repeatCustomers,
            'repeat_customer_rate' => round($repeatRate, 1),
            'top_vip_customers' => $topVipCustomers,
        ];
    }

    /**
     * 12-Month revenue vs cash collected trajectory for ApexCharts.
     */
    protected function calculateRevenueTrajectory(): array
    {
        $labels = [];
        $invoicedData = [];
        $cashData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M y');

            $invoiced = Sale::where('is_archived', false)
                ->where('status', '!=', 'cancelled')
                ->whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->sum('amount');

            $cash = Sale::where('is_archived', false)
                ->where('status', '!=', 'cancelled')
                ->whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->sum('amount_paid');

            $invoicedData[] = round((float) $invoiced, 2);
            $cashData[] = round((float) $cash, 2);
        }

        return [
            'labels' => $labels,
            'invoiced_series' => $invoicedData,
            'cash_series' => $cashData,
        ];
    }
}
