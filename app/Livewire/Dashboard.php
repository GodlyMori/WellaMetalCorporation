<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $activeProducts = Product::where('status', 'active');
        $totalProducts = (clone $activeProducts)->count();
        $categoryCount = (clone $activeProducts)->distinct('category')->count('category');
        $stockUnits = (clone $activeProducts)->sum('quantity_in_stock');

        // Revenue calculations (Settled / Completed sales)
        $completedSales = Sale::where('status', 'completed')
            ->where('is_archived', false);
        $totalCompletedRevenue = (clone $completedSales)->sum('amount');
        $completedCount = (clone $completedSales)->count();
        $avgOrder = $completedCount > 0 ? ($totalCompletedRevenue / $completedCount) : 0;

        $currentMonthSales = (clone $completedSales)
            ->whereYear('sale_date', now()->year)
            ->whereMonth('sale_date', now()->month);
        $currentMonthRevenue = (clone $currentMonthSales)->sum('amount');

        // Realized Cash Collected (All payments collected from completed + active layaways, excluding archived/cancelled)
        $validSales = Sale::where('is_archived', false)->where('status', '!=', 'cancelled');
        $realizedCashCollected = (clone $validSales)->sum('amount_paid');
        $currentMonthCashCollected = (clone $validSales)
            ->whereYear('sale_date', now()->year)
            ->whereMonth('sale_date', now()->month)
            ->sum('amount_paid');

        // Layaways & Receivables
        $layawaySales = Sale::where('is_archived', false)->where('status', 'layaway');
        $layawayCount = (clone $layawaySales)->count();
        $layawayBalance = (float) (clone $layawaySales)->sum('remaining_balance');

        // Top 5 Products - Queried via direct database aggregation for high performance
        $topItems = SaleItem::select('product_id', DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('sale', function ($q) {
                $q->where('is_archived', false)
                    ->where('status', '!=', 'cancelled');
            })
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->with('product:id,name')
            ->take(5)
            ->get();

        $maxAmount = $topItems->max('total_amount') ?? 0;
        $topProducts = [];
        $rankIdx = 1;
        foreach ($topItems as $item) {
            $amount = (float) $item->total_amount;
            $pct = $maxAmount > 0 ? (int) round(($amount / $maxAmount) * 100) : 0;
            $topProducts[] = [
                'rank' => '#'.$rankIdx,
                'name' => $item->product?->name ?? 'Product #'.$item->product_id,
                'amount' => $amount,
                'pct' => $pct,
            ];
            $rankIdx++;
        }

        // Dynamic Stock Distribution by Category from actual database products
        $categories = Product::where('status', 'active')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->selectRaw('category as name, SUM(quantity_in_stock) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'count' => (int) $item->count,
            ])
            ->toArray();

        // Immediate Action Required: Out of Stock (0 units) & Low Stock (at or below threshold)
        $outOfStockItems = Product::where('status', 'active')
            ->where('quantity_in_stock', '<=', 0)
            ->orderBy('name')
            ->take(6)
            ->get();

        $lowStockItems = Product::where('status', 'active')
            ->where('quantity_in_stock', '>', 0)
            ->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold')
            ->orderBy('quantity_in_stock', 'asc')
            ->take(6)
            ->get();

        $totalOutOfStockCount = Product::where('status', 'active')->where('quantity_in_stock', '<=', 0)->count();
        $totalLowStockCount = Product::where('status', 'active')->where('quantity_in_stock', '>', 0)->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold')->count();
        $totalImmediateActions = $totalOutOfStockCount + $totalLowStockCount;

        // 6-month completed sales revenue run for ApexCharts
        $salesChartLabels = [];
        $salesChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $salesChartLabels[] = $m->format('M Y');
            $monthSum = Sale::where('status', 'completed')
                ->where('is_archived', false)
                ->whereYear('sale_date', $m->year)
                ->whereMonth('sale_date', $m->month)
                ->sum('amount');
            $salesChartData[] = (float) $monthSum;
        }

        return view('livewire.dashboard', [
            'totalProducts' => $totalProducts,
            'categoryCount' => $categoryCount,
            'stockUnits' => $stockUnits,
            'totalCompletedRevenue' => $totalCompletedRevenue,
            'currentMonthRevenue' => $currentMonthRevenue,
            'realizedCashCollected' => $realizedCashCollected,
            'currentMonthCashCollected' => $currentMonthCashCollected,
            'avgOrder' => $avgOrder,
            'completedCount' => $completedCount,
            'layawayCount' => $layawayCount,
            'layawayBalance' => $layawayBalance,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'topProducts' => $topProducts,
            'categories' => $categories,
            'outOfStockItems' => $outOfStockItems,
            'lowStockItems' => $lowStockItems,
            'totalOutOfStockCount' => $totalOutOfStockCount,
            'totalLowStockCount' => $totalLowStockCount,
            'totalImmediateActions' => $totalImmediateActions,
        ]);
    }
}
