<div class="space-y-6">

    <!-- HEADER / ACTION ROW -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-sm font-semibold text-slate-900 dark:text-white">Command Center</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Live showroom floor, inventory yard, and daily transaction status</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('sales') }}" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Record sale</span>
            </a>
            <a href="{{ route('inventory') }}" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Inventory yard</span>
            </a>
        </div>
    </div>

    <!-- RESPONSIVE KPI STAT STRIP -->
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] mb-6">
        <!-- Gross Revenue -->
        <div class="p-4 sm:p-5 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Gross settled revenue</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white tabular-nums mt-0.5">₱{{ number_format($totalCompletedRevenue, 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ $completedCount }} settled · Month: ₱{{ number_format($currentMonthRevenue, 2) }}</div>
        </div>
        <!-- Cash Collected -->
        <div class="p-4 sm:p-5 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Realized cash collected</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white tabular-nums mt-0.5">₱{{ number_format($realizedCashCollected, 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">This month: ₱{{ number_format($currentMonthCashCollected, 2) }}</div>
        </div>
        <!-- Layaway Balance -->
        <div class="p-4 sm:p-5 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Layaway receivables</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white tabular-nums mt-0.5">₱{{ number_format($layawayBalance, 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ $layawayCount }} open accounts</div>
        </div>
        <!-- Stock Units -->
        <div class="p-4 sm:p-5 min-w-0">
            <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Physical stock on yard</div>
            <div class="text-xl font-bold text-slate-900 dark:text-white tabular-nums mt-0.5">{{ number_format($stockUnits) }} <span class="text-sm font-medium text-slate-400">units</span></div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ $totalProducts }} SKUs · {{ $categoryCount }} categories</div>
        </div>
    </div>

    <!-- MAIN WORKSTATION: OPERATIONAL & PERFORMANCE DOMAINS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN (7 COLS): URGENT TRIAGE & COMMERCIAL FLOW -->
        <div class="lg:col-span-7 space-y-6">

            <!-- 1. IMMEDIATE OPERATIONAL ACTION & STOCKOUT TRIAGE (HIGH PRIORITY FIRST) -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200 dark:border-[#1a2858]">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Immediate action required
                        </h2>
                    </div>
                    @if($totalImmediateActions > 0)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                            {{ $totalImmediateActions }} urgent alert{{ $totalImmediateActions > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                @if($totalProducts === 0)
                    <!-- Empty Inventory State -->
                    <div class="text-slate-600 dark:text-slate-400 text-xs flex items-center justify-between py-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <div>
                                <div class="font-semibold text-slate-800 dark:text-slate-200">Catalog is empty</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">No products registered in the system yet.</div>
                            </div>
                        </div>
                        <a href="{{ route('inventory') }}" class="text-xs font-semibold text-[#142259] dark:text-blue-400 hover:underline cursor-pointer whitespace-nowrap">
                            Add products &rarr;
                        </a>
                    </div>
                @elseif($totalImmediateActions > 0)
                    <div class="space-y-1">
                        <!-- Out of Stock List -->
                        @if($outOfStockItems->isNotEmpty())
                            <div class="pb-2">
                                <div class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 flex items-center justify-between py-1">
                                    <span>Critical stockouts (0 units)</span>
                                    <span>{{ $totalOutOfStockCount }} SKU(s)</span>
                                </div>
                                @foreach($outOfStockItems as $item)
                                    <div class="border-b border-slate-100 dark:border-[#1a2858] py-2 flex items-center justify-between gap-2 text-xs">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 dark:text-white truncate" title="{{ $item->name }}">{{ $item->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $item->category }} · ₱{{ number_format($item->tagged_price, 2) }}</div>
                                        </div>
                                        <div class="flex items-center gap-2.5 flex-shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900 whitespace-nowrap">
                                                0 units
                                            </span>
                                            <a href="{{ route('inventory') }}" class="text-xs font-semibold text-[#142259] dark:text-slate-300 hover:underline cursor-pointer whitespace-nowrap">
                                                Restock
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Low Stock List -->
                        @if($lowStockItems->isNotEmpty())
                            <div class="pt-2">
                                <div class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 flex items-center justify-between py-1">
                                    <span>Low stock warning (at/below threshold)</span>
                                    <span>{{ $totalLowStockCount }} SKU(s)</span>
                                </div>
                                @foreach($lowStockItems as $item)
                                    <div class="border-b border-slate-100 dark:border-[#1a2858] py-2 flex items-center justify-between gap-2 text-xs">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 dark:text-white truncate" title="{{ $item->name }}">{{ $item->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $item->category }} · ₱{{ number_format($item->tagged_price, 2) }}</div>
                                        </div>
                                        <div class="flex items-center gap-2.5 flex-shrink-0">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900 whitespace-nowrap">
                                                {{ $item->quantity_in_stock }} left <span class="text-[10px] opacity-75 ml-1">(&le;{{ $item->low_stock_threshold ?? 5 }})</span>
                                            </span>
                                            <a href="{{ route('inventory') }}" class="text-xs font-semibold text-[#142259] dark:text-slate-300 hover:underline cursor-pointer whitespace-nowrap">
                                                Add stock
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Healthy State Confirmation (products exist and stock >= 5) -->
                    <div class="text-emerald-700 dark:text-emerald-400 text-xs flex items-center gap-2 py-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-semibold">Inventory levels stable</div>
                            <div class="text-[11px] text-emerald-600/80 dark:text-emerald-400/80 mt-0.5">All active catalog products have sufficient yard stock. No critical restock orders pending.</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 2. REVENUE TRAJECTORY (COMPACT OPERATIONAL PRESENTATION) -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5">
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200 dark:border-[#1a2858]">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Revenue trajectory
                    </h2>
                    <div class="text-right">
                        <span class="text-[11px] text-slate-400 mr-1.5">This month:</span>
                        <span class="text-xs font-bold text-slate-900 dark:text-white tabular-nums font-mono">₱{{ number_format($currentMonthRevenue, 2) }}</span>
                    </div>
                </div>

                <!-- Compact ApexCharts Container (h-48) -->
                <div class="w-full h-48" wire:ignore>
                    <div id="dashboardSalesChart" class="w-full h-full"></div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (5 COLS): MERCHANDISE VELOCITY & YARD METRICS -->
        <div class="lg:col-span-5 space-y-6">

            <!-- 1. TOP SELLING PRODUCTS: DATA-ORIENTED MINI-TABLE -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5">
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200 dark:border-[#1a2858]">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Top revenue products
                    </h2>
                    <span class="text-[11px] text-slate-400">Ranked by settled sales</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-1.5 px-2 w-8">#</th>
                                <th class="py-1.5 px-2">Product</th>
                                <th class="py-1.5 px-2 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858]">
                            @forelse($topProducts as $tp)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2 px-2 text-slate-400 font-mono text-[11px]">{{ $tp['rank'] }}</td>
                                    <td class="py-2 px-2 font-medium text-slate-900 dark:text-white truncate max-w-[170px]" title="{{ $tp['name'] }}">
                                        {{ $tp['name'] }}
                                    </td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold text-slate-900 dark:text-white tabular-nums">
                                        ₱{{ number_format($tp['amount'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-xs text-slate-400">
                                        No settled product sales recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. INVENTORY VOLUME BY CATEGORY -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5">
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200 dark:border-[#1a2858]">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Stock volume by category
                    </h2>
                    <span class="text-[11px] font-mono text-slate-400 tabular-nums">
                        {{ number_format($stockUnits) }} total pcs
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($categories as $cat)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="text-slate-700 dark:text-slate-300 font-medium w-28 truncate">{{ $cat['name'] }}</span>
                            <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-none overflow-hidden">
                                <div class="h-full bg-[#142259] dark:bg-slate-400"
                                     style="width: {{ min(100, ($cat['count'] / max(1, $stockUnits)) * 100) }}%;"></div>
                            </div>
                            <span class="font-mono text-slate-900 dark:text-white w-14 text-right tabular-nums">
                                {{ number_format($cat['count']) }} <span class="text-[10px] text-slate-400">pcs</span>
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            No product categories registered yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initSalesChart();

        window.addEventListener('theme-changed', function () {
            if (window.salesChartInstance) {
                window.salesChartInstance.destroy();
                initSalesChart();
            }
        });
    });

    function initSalesChart() {
        var chartContainer = document.getElementById('dashboardSalesChart');
        if (!chartContainer || typeof ApexCharts === 'undefined') return;

        var isDark = document.documentElement.classList.contains('dark');
        var labels = {!! json_encode($salesChartLabels) !!};
        var data = {!! json_encode($salesChartData) !!};

        var options = {
            series: [{
                name: 'Gross Revenue',
                data: data
            }],
            chart: {
                type: 'area',
                height: 190,
                toolbar: { show: false },
                zoom: { enabled: false },
                background: 'transparent',
                fontFamily: 'inherit'
            },
            colors: [isDark ? '#93c5fd' : '#142259'],
            fill: {
                type: 'solid',
                opacity: 0
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            grid: {
                borderColor: isDark ? '#1a2858' : '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            xaxis: {
                categories: labels,
                labels: {
                    style: {
                        colors: isDark ? '#94a3b8' : '#64748b',
                        fontSize: '11px',
                        fontFamily: 'inherit'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: isDark ? '#94a3b8' : '#64748b',
                        fontSize: '11px',
                        fontFamily: 'inherit'
                    },
                    formatter: function (value) {
                        return '₱' + Number(value).toLocaleString();
                    }
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function (value) {
                        return '₱' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            }
        };

        window.salesChartInstance = new ApexCharts(chartContainer, options);
        window.salesChartInstance.render();
    }
</script>
@endpush
