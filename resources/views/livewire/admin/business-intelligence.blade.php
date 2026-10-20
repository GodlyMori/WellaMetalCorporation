<div class="space-y-6">

    <!-- EXECUTIVE HEADER & DATE PRESET CONTROLS -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-[#1a2858]">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#142259] text-white uppercase tracking-wider">
                    Executive BI
                </span>
                <span class="text-[11px] text-slate-400 font-mono">Confidential · System Admin Only</span>
            </div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1">
                Business Intelligence & Decision Analytics
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Strategic Caravan price negotiation impact, working capital tied in yard, ABC inventory classification, and cash collection conversions
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Date Presets -->
            <div class="inline-flex items-center bg-slate-100 dark:bg-[#0f1b40] p-1 rounded border border-slate-200 dark:border-[#1a2858] text-xs">
                <button type="button"
                        wire:click="setDatePreset('this_year')"
                        class="px-2.5 py-1 rounded font-semibold cursor-pointer transition-colors {{ $datePreset === 'this_year' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    YTD
                </button>
                <button type="button"
                        wire:click="setDatePreset('this_quarter')"
                        class="px-2.5 py-1 rounded font-semibold cursor-pointer transition-colors {{ $datePreset === 'this_quarter' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    Quarter
                </button>
                <button type="button"
                        wire:click="setDatePreset('last_30_days')"
                        class="px-2.5 py-1 rounded font-semibold cursor-pointer transition-colors {{ $datePreset === 'last_30_days' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    Last 30D
                </button>
                <button type="button"
                        wire:click="setDatePreset('last_year')"
                        class="px-2.5 py-1 rounded font-semibold cursor-pointer transition-colors {{ $datePreset === 'last_year' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    Last Year
                </button>
                <button type="button"
                        wire:click="setDatePreset('all_time')"
                        class="px-2.5 py-1 rounded font-semibold cursor-pointer transition-colors {{ $datePreset === 'all_time' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    All-Time
                </button>
            </div>

            <button type="button"
                    onclick="window.print()"
                    class="bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-[#1a2858] text-slate-700 dark:text-slate-300 text-xs font-semibold px-3 py-1.5 rounded transition-colors cursor-pointer inline-flex items-center gap-1.5 shadow-xs">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Brief</span>
            </button>
        </div>
    </div>

    <!-- STRATEGIC EXECUTIVE KPI OVERVIEW (4 KEY CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: REALIZED CASH & GMV -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4.5 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs mb-1">
                <span class="font-medium">Realized Cash Inflow</span>
                <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-semibold">Liquid Cash</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white tabular-nums">
                ₱{{ number_format($metrics['kpis']['realized_cash_collected'], 2) }}
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <span>Invoiced GMV: <strong class="text-slate-800 dark:text-slate-200">₱{{ number_format($metrics['kpis']['total_invoiced_amount'], 2) }}</strong></span>
                <span class="text-[10px]">{{ $metrics['kpis']['completed_count'] }} settled</span>
            </div>
        </div>

        <!-- CARD 2: CARAVAN NEGOTIATION CONCESSION -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4.5 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs mb-1">
                <span class="font-medium">Caravan Price Concessions</span>
                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                    {{ $metrics['kpis']['concession_erosion_rate'] }}% Erosion
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-mono text-amber-700 dark:text-amber-400 tabular-nums">
                -₱{{ number_format($metrics['kpis']['total_concession_amount'], 2) }}
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <span>Gross SRP: ₱{{ number_format($metrics['negotiations']['gross_srp_total'], 2) }}</span>
                <span>{{ $metrics['negotiations']['negotiated_ratio'] }}% items discounted</span>
            </div>
        </div>

        <!-- CARD 3: LAYAWAY CONVERSION -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4.5 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs mb-1">
                <span class="font-medium">Layaway Settlement Rate</span>
                <span class="text-[10px] font-mono text-blue-600 dark:text-blue-400 font-semibold">Conversion</span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-mono text-slate-900 dark:text-white tabular-nums">
                {{ $metrics['kpis']['layaway_settlement_rate'] }}%
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <span>Active Receivables: <strong class="text-slate-800 dark:text-slate-200">₱{{ number_format($metrics['kpis']['outstanding_layaway_receivables'], 2) }}</strong></span>
                <span>{{ $metrics['layaways']['cancelled_count'] }} cancelled</span>
            </div>
        </div>

        <!-- CARD 4: DEAD STOCK WORKING CAPITAL -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4.5 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs mb-1">
                <span class="font-medium">Dead Stock Working Capital</span>
                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                    {{ $metrics['kpis']['dead_stock_count'] }} Idle SKUs
                </span>
            </div>
            <div class="text-xl sm:text-2xl font-bold font-mono text-rose-600 dark:text-rose-400 tabular-nums">
                ₱{{ number_format($metrics['kpis']['dead_stock_capital'], 2) }}
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-[#1a2858]/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                <span>Class A Core: {{ $metrics['inventory_abc']['class_a_count'] }} SKUs (80% rev)</span>
                <span>>60d zero sales</span>
            </div>
        </div>

    </div>

    <!-- SUB-NAVIGATION TABS -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-[#1a2858] overflow-x-auto text-xs pb-1">
        <button type="button"
                wire:click="setActiveTab('overview')"
                class="px-3.5 py-2 font-semibold cursor-pointer transition-colors whitespace-nowrap {{ $activeTab === 'overview' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">
            Executive Visualizations & Trajectory
        </button>
        <button type="button"
                wire:click="setActiveTab('negotiations')"
                class="px-3.5 py-2 font-semibold cursor-pointer transition-colors whitespace-nowrap {{ $activeTab === 'negotiations' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">
            Caravan Price Negotiation Insights
        </button>
        <button type="button"
                wire:click="setActiveTab('inventory_abc')"
                class="px-3.5 py-2 font-semibold cursor-pointer transition-colors whitespace-nowrap {{ $activeTab === 'inventory_abc' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">
            ABC Inventory & Yard Capital Triage
        </button>
        <button type="button"
                wire:click="setActiveTab('customers')"
                class="px-3.5 py-2 font-semibold cursor-pointer transition-colors whitespace-nowrap {{ $activeTab === 'customers' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">
            Customer Concentration & Layaway Risk
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: EXECUTIVE VISUALIZATIONS & TRAJECTORY -->
    <!-- ========================================== -->
    @if($activeTab === 'overview')
        <div class="space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- 12-MONTH TRAJECTORY CHART (8 COLS) -->
                <div class="lg:col-span-8 bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-[#1a2858]">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span>12-Month Invoiced Volume vs. Realized Cash Collected</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Comparing total booked sales contract amounts against actual cash inflow</p>
                        </div>
                        <div class="flex items-center gap-3 text-[11px]">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#142259]"></span> Invoiced</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Cash Paid</span>
                        </div>
                    </div>
                    <div id="biTrajectoryChart" class="w-full h-64"></div>
                </div>

                <!-- CATEGORY CONCESSION TOLERANCE CHART (4 COLS) -->
                <div class="lg:col-span-4 bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-5 shadow-xs">
                    <div class="pb-3 mb-3 border-b border-slate-200 dark:border-[#1a2858]">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Negotiation Erosion by Category</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Percentage of SRP discounted across product categories</p>
                    </div>
                    <div id="biCategoryToleranceChart" class="w-full h-64"></div>
                </div>

            </div>

            <!-- EXECUTIVE ACTION SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Commercial Health Score
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                        Average Order Value stands at <strong class="text-slate-900 dark:text-white font-mono">₱{{ number_format($metrics['kpis']['average_order_value'], 2) }}</strong> across {{ $metrics['kpis']['total_orders_count'] }} recorded orders. Customer repeat purchase rate is currently <strong class="text-slate-900 dark:text-white">{{ $metrics['kpis']['repeat_customer_rate'] }}%</strong>.
                    </p>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Caravan Negotiation Tolerance
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                        A total of <strong class="text-amber-700 dark:text-amber-400 font-mono">₱{{ number_format($metrics['kpis']['total_concession_amount'], 2) }}</strong> was negotiated away across {{ $metrics['negotiations']['negotiated_items_count'] }} line items, resulting in a system-wide discount erosion of <strong class="text-slate-900 dark:text-white">{{ $metrics['kpis']['concession_erosion_rate'] }}%</strong>.
                    </p>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5 mb-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Capital Efficiency & Yard Triage
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                        <strong class="text-rose-600 dark:text-rose-400 font-mono">₱{{ number_format($metrics['kpis']['dead_stock_capital'], 2) }}</strong> of inventory valuation is tied up in {{ $metrics['kpis']['dead_stock_count'] }} stagnant SKUs with zero sales in the last 60 days. Prioritize bundling in caravans.
                    </p>
                </div>

            </div>

        </div>
    @endif

    <!-- ============================================== -->
    <!-- TAB 2: CARAVAN PRICE NEGOTIATION INSIGHTS -->
    <!-- ============================================== -->
    @if($activeTab === 'negotiations')
        <div class="space-y-5">
            
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] bg-slate-50/60 dark:bg-[#0f1b40]/60">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                        Category Negotiation Tolerance & Concession Breakdown
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Shows which product lines encounter the heaviest customer bargaining during Caravan events
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Items Sold</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Gross SRP Total</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Agreed Revenue</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Total Concessions</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Erosion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['negotiations']['categories'] as $cat)
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $cat['category'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono">
                                        {{ number_format($cat['items_sold']) }} pcs
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-600 dark:text-slate-300">
                                        ₱{{ number_format($cat['gross_srp'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                        ₱{{ number_format($cat['realized'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-amber-700 dark:text-amber-400">
                                        @if($cat['concessions'] > 0)
                                            -₱{{ number_format($cat['concessions'], 2) }}
                                        @else
                                            <span class="text-slate-400">₱0.00</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        @if($cat['concession_rate'] > 10)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                                {{ $cat['concession_rate'] }}% (High)
                                            </span>
                                        @elseif($cat['concession_rate'] > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                                {{ $cat['concession_rate'] }}%
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                0.0% (Full SRP)
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-xs text-slate-400">
                                        No sales item data recorded for this date period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    <!-- ============================================== -->
    <!-- TAB 3: ABC INVENTORY & YARD CAPITAL TRIAGE -->
    <!-- ============================================== -->
    @if($activeTab === 'inventory_abc')
        <div class="space-y-6">

            <!-- ABC PARETO DISTRIBUTION STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="bg-white dark:bg-[#0c163b] rounded border-l-4 border-l-blue-600 border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Class A (Core Revenue Drivers)</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300">Top 80% Rev</span>
                    </div>
                    <div class="text-lg font-bold font-mono text-slate-900 dark:text-white mt-1">
                        ₱{{ number_format($metrics['inventory_abc']['class_a_revenue'], 2) }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $metrics['inventory_abc']['class_a_count'] }} product SKUs responsible for 80% of historical revenue. Maintain strict zero-stockout policy.
                    </p>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border-l-4 border-l-amber-500 border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Class B (Stable Movers)</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">Next 15% Rev</span>
                    </div>
                    <div class="text-lg font-bold font-mono text-slate-900 dark:text-white mt-1">
                        ₱{{ number_format($metrics['inventory_abc']['class_b_revenue'], 2) }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $metrics['inventory_abc']['class_b_count'] }} product SKUs. Standard replenishment rhythm.
                    </p>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border-l-4 border-l-rose-500 border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">Class C & Stagnant Items</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300">Bottom 5% Rev</span>
                    </div>
                    <div class="text-lg font-bold font-mono text-slate-900 dark:text-white mt-1">
                        ₱{{ number_format($metrics['inventory_abc']['class_c_revenue'], 2) }}
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $metrics['inventory_abc']['class_c_count'] }} SKUs with minimal velocity. Avoid over-ordering.
                    </p>
                </div>

            </div>

            <!-- DEAD STOCK INVENTORY CAPITAL TABLE -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] bg-rose-50/30 dark:bg-rose-950/10 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-rose-900 dark:text-rose-300">
                                Stagnant & Dead Stock Working Capital Triage
                            </h3>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Active products in yard with unsold physical units that recorded zero sales over the last 60+ days
                        </p>
                    </div>
                    <span class="text-xs font-mono font-bold text-rose-600 dark:text-rose-400">
                        Total Trapped: ₱{{ number_format($metrics['inventory_abc']['dead_stock_capital'], 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product SKU</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Unsold Units in Yard</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Tagged SRP</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right font-bold text-rose-600 dark:text-rose-400">Tied Working Capital</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['inventory_abc']['dead_stock_items'] as $dead)
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $dead['name'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">
                                        {{ $dead['category'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-rose-600">
                                        {{ $dead['stock'] }} units
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-600 dark:text-slate-300">
                                        ₱{{ number_format($dead['unit_price'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                        ₱{{ number_format($dead['tied_capital'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-xs text-slate-400">
                                        Excellent inventory turnover: no stagnant products found with idle inventory >60 days!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    <!-- ============================================== -->
    <!-- TAB 4: CUSTOMER CONCENTRATION & LAYAWAY RISK -->
    <!-- ============================================== -->
    @if($activeTab === 'customers')
        <div class="space-y-6">

            <!-- CUSTOMER & LAYAWAY SUMMARY STRIP -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <span class="text-slate-500 block">Repeat Customer Rate</span>
                    <span class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-0.5 block">
                        {{ $metrics['customers']['repeat_customer_rate'] }}%
                    </span>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        {{ $metrics['customers']['repeat_customers_count'] }} of {{ $metrics['customers']['total_unique_customers'] }} unique customers have multiple purchases
                    </span>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <span class="text-slate-500 block">Layaway Settlement Conversion</span>
                    <span class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                        {{ $metrics['layaways']['settlement_rate'] }}%
                    </span>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        {{ $metrics['layaways']['settled_count'] }} contracts fully paid out of {{ $metrics['layaways']['total_historical'] }} historical layaways
                    </span>
                </div>

                <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 text-xs">
                    <span class="text-slate-500 block">Layaway Default / Cancellation</span>
                    <span class="text-xl font-bold font-mono text-rose-600 dark:text-rose-400 mt-0.5 block">
                        {{ $metrics['layaways']['cancellation_rate'] }}%
                    </span>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        {{ $metrics['layaways']['cancelled_count'] }} accounts cancelled or defaulted
                    </span>
                </div>
            </div>

            <!-- TOP 10 VIP CUSTOMERS BY LIFETIME VALUE -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] bg-slate-50/60 dark:bg-[#0f1b40]/60">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                        Top 10 High-LTV VIP Clients (Cumulative Cash Contribution)
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Identifies key clientele driving Wella Metal Corporation's long-term showroom & caravan revenues
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-4 w-12 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Rank</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer Name</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contact</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Orders</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Invoiced Spend</th>
                                <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right font-bold text-slate-900 dark:text-white">Realized Cash (LTV)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($metrics['customers']['top_vip_customers'] as $idx => $vip)
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-500">
                                        #{{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-900 dark:text-white">
                                        {{ $vip['name'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ $vip['phone'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono">
                                        {{ $vip['orders_count'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-600 dark:text-slate-300">
                                        ₱{{ number_format($vip['total_spend'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        ₱{{ number_format($vip['cash_paid'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-xs text-slate-400">
                                        No customer transaction records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initBiCharts();

        window.addEventListener('theme-changed', function () {
            destroyBiCharts();
            initBiCharts();
        });
    });

    document.addEventListener('livewire:navigated', function () {
        destroyBiCharts();
        initBiCharts();
    });

    function destroyBiCharts() {
        if (window.biTrajectoryInstance) {
            window.biTrajectoryInstance.destroy();
            window.biTrajectoryInstance = null;
        }
        if (window.biCategoryToleranceInstance) {
            window.biCategoryToleranceInstance.destroy();
            window.biCategoryToleranceInstance = null;
        }
    }

    function initBiCharts() {
        var trajContainer = document.getElementById('biTrajectoryChart');
        if (trajContainer && typeof ApexCharts !== 'undefined') {
            var isDark = document.documentElement.classList.contains('dark');
            var trajLabels = {!! json_encode($metrics['trajectory']['labels']) !!};
            var invoicedSeries = {!! json_encode($metrics['trajectory']['invoiced_series']) !!};
            var cashSeries = {!! json_encode($metrics['trajectory']['cash_series']) !!};

            var trajOptions = {
                series: [
                    { name: 'Booked Invoiced (₱)', data: invoicedSeries },
                    { name: 'Realized Cash (₱)', data: cashSeries }
                ],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    background: 'transparent',
                    fontFamily: 'inherit'
                },
                colors: ['#142259', '#10b981'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                grid: {
                    borderColor: isDark ? '#1a2858' : '#f1f5f9',
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: trajLabels,
                    labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } }
                },
                yaxis: {
                    labels: {
                        style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                        formatter: function (val) {
                            return '₱' + Number(val).toLocaleString();
                        }
                    }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return '₱' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
                        }
                    }
                }
            };

            window.biTrajectoryInstance = new ApexCharts(trajContainer, trajOptions);
            window.biTrajectoryInstance.render();
        }

        var catContainer = document.getElementById('biCategoryToleranceChart');
        if (catContainer && typeof ApexCharts !== 'undefined') {
            var catData = {!! json_encode(array_slice($metrics['negotiations']['categories'], 0, 6)) !!};
            var catNames = catData.map(function (c) { return c.category; });
            var catErosion = catData.map(function (c) { return c.concession_rate; });

            if (catNames.length === 0) {
                catNames = ['No Data'];
                catErosion = [0];
            }

            var catOptions = {
                series: [{ name: 'Discount Erosion %', data: catErosion }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'inherit'
                },
                colors: ['#d97706'],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        borderRadius: 3,
                        barHeight: '55%'
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return val + '%'; },
                    style: { fontSize: '10px', colors: ['#ffffff'] }
                },
                grid: {
                    borderColor: isDark ? '#1a2858' : '#f1f5f9',
                    strokeDashArray: 4
                },
                xaxis: {
                    labels: {
                        style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '10px' },
                        formatter: function (v) { return v + '%'; }
                    }
                },
                yaxis: {
                    labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } }
                },
                tooltip: {
                    theme: isDark ? 'dark' : 'light',
                    y: {
                        formatter: function (val) { return val + '% SRP discount rate'; }
                    }
                }
            };

            window.biCategoryToleranceInstance = new ApexCharts(catContainer, catOptions);
            window.biCategoryToleranceInstance.render();
        }
    }
</script>
@endpush
