<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminAnalyticsBi extends Component
{
    // Filter properties
    public string $timeframe = '30d'; // '7d', '30d', 'this_month', 'last_month', 'quarter', 'ytd', 'all', 'custom'
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    public string $categoryFilter = 'ALL';
    public string $paymentFilter = 'ALL';
    public string $activeTab = 'overview'; // 'overview', 'layaways', 'products', 'staff'

    public function mount()
    {
        // Enforce strict Admin-Only authorization
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Analytics and Business Intelligence is strictly reserved for System Administrators.');
        }

        $this->customStartDate = now()->subDays(30)->toDateString();
        $this->customEndDate = now()->toDateString();
    }

    public function setTimeframe(string $timeframe)
    {
        $this->timeframe = $timeframe;
    }

    public function setCategoryFilter(string $category)
    {
        $this->categoryFilter = $category;
    }

    public function setPaymentFilter(string $payment)
    {
        $this->paymentFilter = $payment;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Resolves the start and end dates based on the active timeframe.
     */
    protected function getDateRange(): array
    {
        $now = now();
        return match ($this->timeframe) {
            '7d' => [$now->copy()->subDays(7)->startOfDay(), $now->copy()->endOfDay()],
            '30d' => [$now->copy()->subDays(30)->startOfDay(), $now->copy()->endOfDay()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'quarter' => [$now->copy()->subDays(90)->startOfDay(), $now->copy()->endOfDay()],
            'ytd' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            'all' => [Carbon::parse('2020-01-01')->startOfDay(), $now->copy()->endOfDay()],
            'custom' => [
                $this->customStartDate ? Carbon::parse($this->customStartDate)->startOfDay() : $now->copy()->subDays(30)->startOfDay(),
                $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : $now->copy()->endOfDay(),
            ],
            default => [$now->copy()->subDays(30)->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * Preceding period range for comparative growth analysis.
     */
    protected function getPrecedingDateRange(Carbon $start, Carbon $end): array
    {
        $days = max(1, $start->diffInDays($end));
        $prevEnd = $start->copy()->subSecond();
        $prevStart = $prevEnd->copy()->subDays($days)->startOfDay();
        return [$prevStart, $prevEnd];
    }

    /**
     * Base query for sales within date range and applied filters.
     */
    protected function getFilteredSalesQuery($startDate, $endDate)
    {
        $query = Sale::query()
            ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'cancelled');

        if ($this->paymentFilter !== 'ALL') {
            $query->where('payment_type', $this->paymentFilter);
        }

        if ($this->categoryFilter !== 'ALL') {
            $query->where(function ($q) {
                $q->whereHas('items.product', function ($itemQ) {
                    $itemQ->where('category', $this->categoryFilter);
                })->orWhereHas('product', function ($prodQ) {
                    $prodQ->where('category', $this->categoryFilter);
                });
            });
        }

        return $query;
    }

    public function render()
    {
        [$startDate, $endDate] = $this->getDateRange();
        [$prevStart, $prevEnd] = $this->getPrecedingDateRange($startDate, $endDate);

        // Current period sales
        $currentSales = $this->getFilteredSalesQuery($startDate, $endDate)->get();
        // Previous period sales (for comparative trend)
        $prevSales = $this->getFilteredSalesQuery($prevStart, $prevEnd)->get();

        // ----------------------------------------------------
        // 1. EXECUTIVE KPI SCORECARDS
        // ----------------------------------------------------
        $grossRevenue = (float)$currentSales->sum('amount');
        $realizedCash = (float)$currentSales->sum('amount_paid');
        $prevGrossRevenue = (float)$prevSales->sum('amount');
        $prevRealizedCash = (float)$prevSales->sum('amount_paid');

        $revenueGrowth = $prevGrossRevenue > 0
            ? round((($grossRevenue - $prevGrossRevenue) / $prevGrossRevenue) * 100, 1)
            : ($grossRevenue > 0 ? 100.0 : 0.0);

        $cashGrowth = $prevRealizedCash > 0
            ? round((($realizedCash - $prevRealizedCash) / $prevRealizedCash) * 100, 1)
            : ($realizedCash > 0 ? 100.0 : 0.0);

        $realizationRate = $grossRevenue > 0
            ? round(($realizedCash / $grossRevenue) * 100, 1)
            : 0.0;

        // Outstanding Receivables across ALL active layaways
        $activeLayawaysAll = Sale::where('status', 'layaway')
            ->where('remaining_balance', '>', 0)
            ->get();
        $totalOutstandingReceivables = (float)$activeLayawaysAll->sum('remaining_balance');
        $activeLayawaysCount = $activeLayawaysAll->count();

        // Orders breakdown
        $totalOrdersCount = $currentSales->count();
        $completedCount = $currentSales->where('status', 'completed')->count();
        $layawayCount = $currentSales->where('status', 'layaway')->count();
        $averageOrderValue = $totalOrdersCount > 0 ? round($grossRevenue / $totalOrdersCount, 2) : 0.0;

        // Inventory valuation
        $activeProducts = Product::where('status', '!=', 'archived')->get();
        $inventoryValuation = (float)$activeProducts->sum(fn($p) => (float)$p->tagged_price * $p->quantity_in_stock);
        $totalStockUnits = (int)$activeProducts->sum('quantity_in_stock');
        $lowStockProductsCount = $activeProducts->where('status', 'active')->where('quantity_in_stock', '<=', 4)->count();
        $outOfStockCount = $activeProducts->where('status', 'active')->where('quantity_in_stock', '<=', 0)->count();

        // Discounts conceded
        $totalDiscountsGiven = (float)$currentSales->sum('discount_amount');

        // ----------------------------------------------------
        // 2. CHART 1: REVENUE & CASH FLOW TRAJECTORY
        // ----------------------------------------------------
        $trajectoryLabels = [];
        $trajectoryRevenue = [];
        $trajectoryCash = [];

        $diffDays = $startDate->diffInDays($endDate);

        if ($diffDays <= 35) {
            // Daily granularity
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            $salesByDate = $currentSales->groupBy(fn($s) => $s->sale_date->format('Y-m-d'));

            foreach ($period as $dt) {
                $dateKey = $dt->format('Y-m-d');
                $trajectoryLabels[] = $dt->format('M d');
                $daySales = $salesByDate->get($dateKey, collect());
                $trajectoryRevenue[] = (float)$daySales->sum('amount');
                $trajectoryCash[] = (float)$daySales->sum('amount_paid');
            }
        } else {
            // Weekly granularity
            $period = CarbonPeriod::create($startDate, '1 week', $endDate);
            foreach ($period as $dt) {
                $weekEnd = $dt->copy()->endOfWeek();
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }
                $trajectoryLabels[] = $dt->format('M d') . ' - ' . $weekEnd->format('M d');

                $weekSales = $currentSales->filter(function ($s) use ($dt, $weekEnd) {
                    return $s->sale_date->gte($dt->startOfDay()) && $s->sale_date->lte($weekEnd->endOfDay());
                });

                $trajectoryRevenue[] = (float)$weekSales->sum('amount');
                $trajectoryCash[] = (float)$weekSales->sum('amount_paid');
            }
        }

        // ----------------------------------------------------
        // 3. CHART 2: CATEGORY REVENUE DISTRIBUTION & MARKET SHARE
        // ----------------------------------------------------
        $categoryRevenueMap = [
            'Sofa' => 0.0,
            'Dining Table' => 0.0,
            'Closet' => 0.0,
            'Custom Metal' => 0.0,
        ];

        foreach ($currentSales as $sale) {
            $categoryFound = false;
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $cat = $item->product->category;
                    $categoryRevenueMap[$cat] = ($categoryRevenueMap[$cat] ?? 0.0) + (float)$item->subtotal;
                    $categoryFound = true;
                }
            }
            if (!$categoryFound && $sale->product) {
                $cat = $sale->product->category;
                $categoryRevenueMap[$cat] = ($categoryRevenueMap[$cat] ?? 0.0) + (float)$sale->amount;
            } elseif (!$categoryFound) {
                $categoryRevenueMap['Custom Metal'] += (float)$sale->amount;
            }
        }

        // Filter out zero categories if other categories have revenue
        $categoryLabels = [];
        $categorySeries = [];
        foreach ($categoryRevenueMap as $catName => $catRev) {
            if ($catRev > 0 || count($categoryLabels) < 3) {
                $categoryLabels[] = $catName;
                $categorySeries[] = round($catRev, 2);
            }
        }

        // ----------------------------------------------------
        // 4. CHART 3: TOP GROSSING & BEST-SELLING PRODUCTS
        // ----------------------------------------------------
        $productStats = [];
        foreach ($currentSales as $sale) {
            foreach ($sale->items as $item) {
                $pId = $item->product_id;
                $pName = $item->product?->name ?? $sale->product_name;
                $pCat = $item->product?->category ?? 'General';
                $pPrice = (float)($item->product?->tagged_price ?? $item->unit_price);
                $pStock = $item->product?->quantity_in_stock ?? 0;

                if (!isset($productStats[$pId])) {
                    $productStats[$pId] = [
                        'id' => $pId,
                        'name' => $pName,
                        'category' => $pCat,
                        'tagged_price' => $pPrice,
                        'stock' => $pStock,
                        'units_sold' => 0,
                        'gross_revenue' => 0.0,
                    ];
                }

                $productStats[$pId]['units_sold'] += (int)$item->quantity;
                $productStats[$pId]['gross_revenue'] += (float)$item->subtotal;
            }

            if ($sale->items->isEmpty() && $sale->product_id) {
                $pId = $sale->product_id;
                $pName = $sale->product_name;
                $pCat = $sale->product?->category ?? 'General';
                $pPrice = (float)($sale->product?->tagged_price ?? $sale->amount);
                $pStock = $sale->product?->quantity_in_stock ?? 0;

                if (!isset($productStats[$pId])) {
                    $productStats[$pId] = [
                        'id' => $pId,
                        'name' => $pName,
                        'category' => $pCat,
                        'tagged_price' => $pPrice,
                        'stock' => $pStock,
                        'units_sold' => 0,
                        'gross_revenue' => 0.0,
                    ];
                }

                $productStats[$pId]['units_sold'] += 1;
                $productStats[$pId]['gross_revenue'] += (float)$sale->amount;
            }
        }

        usort($productStats, fn($a, $b) => $b['gross_revenue'] <=> $a['gross_revenue']);
        $topProducts = array_slice($productStats, 0, 7);

        $topProductNames = array_column($topProducts, 'name');
        $topProductRevenue = array_column($topProducts, 'gross_revenue');
        $topProductUnits = array_column($topProducts, 'units_sold');

        // ----------------------------------------------------
        // 5. CHART 4: LAYAWAY AGING & CREDIT RISK HORIZON
        // ----------------------------------------------------
        $layawayAging = [
            'Healthy (>45d)' => ['paid' => 0.0, 'balance' => 0.0, 'count' => 0],
            'Upcoming Due (15-45d)' => ['paid' => 0.0, 'balance' => 0.0, 'count' => 0],
            'Critical Due (<15d)' => ['paid' => 0.0, 'balance' => 0.0, 'count' => 0],
            'Overdue / Default' => ['paid' => 0.0, 'balance' => 0.0, 'count' => 0],
        ];

        $today = now()->startOfDay();
        $detailedLayaways = [];

        foreach ($activeLayawaysAll as $layaway) {
            $daysLeft = $layaway->layaway_expires_at ? $today->diffInDays($layaway->layaway_expires_at, false) : 90;
            $paid = (float)$layaway->amount_paid;
            $bal = (float)$layaway->remaining_balance;

            if ($daysLeft < 0) {
                $bucket = 'Overdue / Default';
                $badge = 'critical';
            } elseif ($daysLeft <= 15) {
                $bucket = 'Critical Due (<15d)';
                $badge = 'warning';
            } elseif ($daysLeft <= 45) {
                $bucket = 'Upcoming Due (15-45d)';
                $badge = 'notice';
            } else {
                $bucket = 'Healthy (>45d)';
                $badge = 'healthy';
            }

            $layawayAging[$bucket]['paid'] += $paid;
            $layawayAging[$bucket]['balance'] += $bal;
            $layawayAging[$bucket]['count'] += 1;

            $detailedLayaways[] = [
                'sale' => $layaway,
                'days_left' => $daysLeft,
                'badge' => $badge,
                'paid_pct' => $layaway->amount > 0 ? round(($paid / (float)$layaway->amount) * 100, 1) : 0,
            ];
        }

        // Sort detailed layaways by urgency (fewest days left first)
        usort($detailedLayaways, fn($a, $b) => $a['days_left'] <=> $b['days_left']);

        $agingCategories = array_keys($layawayAging);
        $agingPaidSeries = array_column(array_values($layawayAging), 'paid');
        $agingBalanceSeries = array_column(array_values($layawayAging), 'balance');

        // ----------------------------------------------------
        // 6. CHART 5: PAYMENT METHOD RATIO & REALIZATION
        // ----------------------------------------------------
        $fullPaymentAmount = (float)$currentSales->where('payment_type', 'full')->sum('amount');
        $layawayDownpayments = (float)$currentSales->where('payment_type', 'layaway')->sum('amount_paid');
        $paymentMethodSeries = [round($fullPaymentAmount, 2), round($layawayDownpayments, 2)];
        $paymentMethodLabels = ['Full Cash / Settlement', 'Lay-Away Initial Downpayment'];

        // ----------------------------------------------------
        // 7. CHART 6: INVENTORY HEALTH & RISK DISTRIBUTION
        // ----------------------------------------------------
        $healthyStockCount = $activeProducts->where('status', 'active')->where('quantity_in_stock', '>=', 5)->count();
        $inventoryHealthSeries = [$healthyStockCount, $lowStockProductsCount, $outOfStockCount];
        $inventoryHealthLabels = ['Healthy Stock (>=5)', 'Low Stock Alert (1-4)', 'Depleted / Out of Stock (0)'];

        // ----------------------------------------------------
        // 8. STAFF / TEAM SALES PERFORMANCE
        // ----------------------------------------------------
        $staffPerformance = [];
        $users = User::all()->keyBy('id');

        $salesByCreator = $currentSales->groupBy('created_by');
        foreach ($salesByCreator as $creatorId => $salesList) {
            $user = $users->get($creatorId);
            $userName = $user ? $user->name : 'Secretary Desk';
            $userEmail = $user ? $user->email : 'staff@wellametal.test';
            $userRole = $user ? ($user->roles->first()?->name ?? 'Staff') : 'Staff';

            $staffVolume = (float)$salesList->sum('amount');
            $staffCash = (float)$salesList->sum('amount_paid');

            $staffPerformance[] = [
                'user_id' => $creatorId,
                'name' => $userName,
                'email' => $userEmail,
                'role' => strtoupper($userRole),
                'deals_count' => $salesList->count(),
                'gross_volume' => $staffVolume,
                'realized_cash' => $staffCash,
                'contribution_pct' => $grossRevenue > 0 ? round(($staffVolume / $grossRevenue) * 100, 1) : 0,
            ];
        }

        usort($staffPerformance, fn($a, $b) => $b['gross_volume'] <=> $a['gross_volume']);

        // ----------------------------------------------------
        // 9. SMART BUSINESS INTELLIGENCE (BI) STRATEGIC INSIGHTS
        // ----------------------------------------------------
        $biInsights = [];

        // Insight 1: Primary Revenue Engine
        if (!empty($categoryRevenueMap) && $grossRevenue > 0) {
            arsort($categoryRevenueMap);
            $topCat = array_key_first($categoryRevenueMap);
            $topCatRev = $categoryRevenueMap[$topCat];
            $catShare = round(($topCatRev / $grossRevenue) * 100, 1);
            $biInsights[] = [
                'type' => 'driver',
                'title' => "Core Revenue Driver: {$topCat} ({$catShare}%)",
                'body' => "The {$topCat} product category generated ₱" . number_format($topCatRev, 2) . " representing {$catShare}% of gross billed sales this period. Recommended to maintain inventory buffer.",
                'badge' => 'High Velocity',
                'color' => 'blue',
            ];
        }

        // Insight 2: Cash Realization Velocity
        if ($grossRevenue > 0) {
            if ($realizationRate >= 80) {
                $biInsights[] = [
                    'type' => 'cashflow',
                    'title' => "Healthy Cash Realization Rate ({$realizationRate}%)",
                    'body' => "Strong upfront liquidity with ₱" . number_format($realizedCash, 2) . " immediately realized in company cash accounts. Working capital cycle is currently optimal.",
                    'badge' => 'Optimal Liquidity',
                    'color' => 'emerald',
                ];
            } else {
                $biInsights[] = [
                    'type' => 'cashflow',
                    'title' => "High Layaway Exposure ({$realizationRate}% Cash Realized)",
                    'body' => "₱" . number_format($totalOutstandingReceivables, 2) . " remains committed in pending layaways. Ensure timely customer reminders to accelerate settlement before 90-day expiration.",
                    'badge' => 'Credit Exposure',
                    'color' => 'amber',
                ];
            }
        }

        // Insight 3: Layaway Expiration Radar
        $expiringSoonCount = $layawayAging['Critical Due (<15d)']['count'] + $layawayAging['Overdue / Default']['count'];
        $expiringSoonBal = $layawayAging['Critical Due (<15d)']['balance'] + $layawayAging['Overdue / Default']['balance'];
        if ($expiringSoonCount > 0) {
            $biInsights[] = [
                'type' => 'risk',
                'title' => "{$expiringSoonCount} Layaway Account(s) Nearing 90-Day Expiry",
                'body' => "₱" . number_format($expiringSoonBal, 2) . " in outstanding balances are within 15 days of expiration or overdue. Recommend staff reach out for final payment or layaway forfeit review.",
                'badge' => 'Action Required',
                'color' => 'red',
            ];
        }

        // Insight 4: Inventory Reorder Urgency
        if ($lowStockProductsCount > 0) {
            $lowStockItems = $activeProducts->where('status', 'active')->where('quantity_in_stock', '<=', 4)->take(2);
            $names = $lowStockItems->pluck('name')->implode(', ');
            $biInsights[] = [
                'type' => 'inventory',
                'title' => "{$lowStockProductsCount} Products in Critical Stock Reorder Zone",
                'body' => "Items such as {$names} have 4 units or fewer in warehouse stock. Initiate fabrication replenishment orders with the production workshop.",
                'badge' => 'Supply Alert',
                'color' => 'purple',
            ];
        }

        // Insight 5: Seasonal Promo ROI
        if ($totalDiscountsGiven > 0) {
            $promoSalesCount = $currentSales->whereNotNull('promotion_id')->count();
            $biInsights[] = [
                'type' => 'marketing',
                'title' => "Promotional Discount ROI",
                'body' => "Promotions conceded ₱" . number_format($totalDiscountsGiven, 2) . " in price concessions across {$promoSalesCount} orders, successfully stimulating volume without undermining gross profitability.",
                'badge' => 'Promo Active',
                'color' => 'indigo',
            ];
        }

        // Categories list for filter dropdown
        $allCategories = Product::select('category')->distinct()->pluck('category')->filter()->values();

        return view('livewire.admin-analytics-bi', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'diffDays' => $diffDays,
            'grossRevenue' => $grossRevenue,
            'realizedCash' => $realizedCash,
            'revenueGrowth' => $revenueGrowth,
            'cashGrowth' => $cashGrowth,
            'realizationRate' => $realizationRate,
            'totalOutstandingReceivables' => $totalOutstandingReceivables,
            'activeLayawaysCount' => $activeLayawaysCount,
            'totalOrdersCount' => $totalOrdersCount,
            'completedCount' => $completedCount,
            'layawayCount' => $layawayCount,
            'averageOrderValue' => $averageOrderValue,
            'inventoryValuation' => $inventoryValuation,
            'totalStockUnits' => $totalStockUnits,
            'lowStockProductsCount' => $lowStockProductsCount,
            'outOfStockCount' => $outOfStockCount,
            'totalDiscountsGiven' => $totalDiscountsGiven,

            // Chart data
            'trajectoryLabels' => $trajectoryLabels,
            'trajectoryRevenue' => $trajectoryRevenue,
            'trajectoryCash' => $trajectoryCash,

            'categoryLabels' => $categoryLabels,
            'categorySeries' => $categorySeries,

            'topProductNames' => $topProductNames,
            'topProductRevenue' => $topProductRevenue,
            'topProductUnits' => $topProductUnits,
            'topProducts' => $topProducts,

            'agingCategories' => $agingCategories,
            'agingPaidSeries' => $agingPaidSeries,
            'agingBalanceSeries' => $agingBalanceSeries,
            'detailedLayaways' => $detailedLayaways,

            'paymentMethodLabels' => $paymentMethodLabels,
            'paymentMethodSeries' => $paymentMethodSeries,

            'inventoryHealthLabels' => $inventoryHealthLabels,
            'inventoryHealthSeries' => $inventoryHealthSeries,

            'staffPerformance' => $staffPerformance,
            'biInsights' => $biInsights,
            'allCategories' => $allCategories,
        ]);
    }
}
