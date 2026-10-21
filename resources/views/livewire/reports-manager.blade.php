<div class="space-y-5">

    <!-- Success Message Alert -->
    @if($successMessage)
        <div class="rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-3.5 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- TOP HEADER: REPORT NAVIGATION TABS + EXPORT ACTIONS -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-6 pb-2 border-b border-slate-200 dark:border-[#1a2858]">
        <div class="inline-flex items-center gap-6 overflow-x-auto">
            <button type="button" wire:click="setTab('periodic')" class="text-xs font-semibold pb-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'periodic' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">Periodic summaries</button>
            <button type="button" wire:click="setTab('sales')" class="text-xs font-semibold pb-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'sales' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">Sales & orders</button>
            <button type="button" wire:click="setTab('inventory')" class="text-xs font-semibold pb-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'inventory' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">Inventory & valuation</button>
            <button type="button" wire:click="setTab('layaways')" class="text-xs font-semibold pb-2 cursor-pointer whitespace-nowrap {{ $activeTab === 'layaways' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400' }}">Layaway receivables</button>
        </div>
        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
            <!-- Master Comprehensive Export (All 3 Reports) -->
            <a href="{{ route('reports.export.all-pdf') }}"
               target="_blank"
               class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-3 py-1.5 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5"
               title="Download unified master audit PDF containing Sales & Orders, Inventory Valuation, and Layaway Receivables">
                <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Export All (Master Audit)</span>
            </a>

            @if($activeTab === 'periodic')
                <!-- Export Periodic Report PDF -->
                <a href="{{ route('reports.export.periodic-pdf', ['periodicity' => $periodicity, 'start_date' => $periodicStartDate, 'end_date' => $periodicEndDate, 'search' => $periodicSearch]) }}"
                   target="_blank"
                   class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-3 py-1.5 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5"
                   title="Download executive periodic business report PDF (Daily/Weekly/Monthly compilation)">
                    <svg class="w-3.5 h-3.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Export Periodic PDF</span>
                </a>
            @elseif($activeTab === 'sales')
                <button type="button" wire:click="processExpiredLayaways" title="Scan & restore cancelled/expired layaway reserves"
                   class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-3 py-1.5 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Check expired reserves</span>
                </button>

                <!-- Export Sales PDF -->
                <a href="{{ route('reports.export.sales-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $salesStatus, 'search' => $salesSearch]) }}"
                   target="_blank"
                   class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-3 py-1.5 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Export Sales PDF</span>
                </a>
            @elseif($activeTab === 'inventory')
                <!-- Export Inventory PDF -->
                <a href="{{ route('reports.export.inventory-pdf', ['category' => $inventoryCategory]) }}"
                   target="_blank"
                   class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-3 py-1.5 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Export Inventory PDF</span>
                </a>
            @else
                <!-- Export Layaway PDF -->
                <a href="{{ route('reports.export.layaways-pdf', ['status' => $layawayStatus]) }}"
                   target="_blank"
                   class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-3 py-1.5 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Export Layaways PDF</span>
                </a>
            @endif
        </div>
    </div>

    <!-- =================================================== -->
    <!-- TAB 0: PERIODIC EXECUTIVE BUSINESS SUMMARIES           -->
    <!-- =================================================== -->
    @if($activeTab === 'periodic')
        <div class="space-y-4">

            <!-- Periodic Summary KPI Strip -->
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b]">
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Realized Cash Revenue</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">
                        ₱{{ number_format($periodicSummary['totals']['realized_cash'], 2) }}
                    </div>
                    <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5">Completed + Layaway Deposits</div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Gross Sales Volume</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">
                        ₱{{ number_format($periodicSummary['totals']['gross_sales_volume'], 2) }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5">
                        {{ $periodicSummary['totals']['completed_count'] + $periodicSummary['totals']['layaway_count'] }} active orders &bull; ₱{{ number_format($periodicSummary['totals']['average_ticket'], 2) }} AOV
                    </div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Stock Turnover Volume</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">
                        {{ number_format($inventoryMovements['total_units_sold']) }} units
                    </div>
                    <div class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">
                        {{ $inventoryMovements['overall_turnover_rate'] }}% turnover rate ({{ $inventoryMovements['total_restocked'] }} added)
                    </div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Outstanding Receivables</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">
                        ₱{{ number_format($periodicSummary['totals']['outstanding_receivables'], 2) }}
                    </div>
                    <div class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5">
                        {{ $periodicSummary['totals']['layaway_count'] }} layaway portfolio accounts
                    </div>
                </div>
            </div>

            <!-- Sub-view Navigation Pills: Arranges the 3 periodic tables cleanly -->
            <div class="flex items-center justify-between flex-wrap gap-2 pt-1 pb-1">
                <div class="inline-flex p-1 rounded-lg bg-slate-100 dark:bg-[#0f1b40] border border-slate-200 dark:border-slate-800 text-xs">
                    <button type="button"
                            wire:click="setPeriodicSubView('ledger')"
                            class="px-3.5 py-1.5 rounded-md font-semibold transition-all flex items-center gap-1.5 cursor-pointer {{ $periodicSubView === 'ledger' ? 'bg-white dark:bg-[#142259] text-[#142259] dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Periodic Sales Ledger</span>
                    </button>
                    <button type="button"
                            wire:click="setPeriodicSubView('turnover')"
                            class="px-3.5 py-1.5 rounded-md font-semibold transition-all flex items-center gap-1.5 cursor-pointer {{ $periodicSubView === 'turnover' ? 'bg-white dark:bg-[#142259] text-[#142259] dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Stock Turnover & Velocity</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded font-mono {{ $periodicSubView === 'turnover' ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">{{ count($inventoryMovements['items']) }}</span>
                    </button>
                    <button type="button"
                            wire:click="setPeriodicSubView('traceability')"
                            class="px-3.5 py-1.5 rounded-md font-semibold transition-all flex items-center gap-1.5 cursor-pointer {{ $periodicSubView === 'traceability' ? 'bg-white dark:bg-[#142259] text-[#142259] dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Receipts & Traceability</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded font-mono {{ $periodicSubView === 'traceability' ? 'bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">{{ $traceableSales->count() }}</span>
                    </button>
                </div>

                <div class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                    @if($periodicSubView === 'ledger')
                        Aggregated {{ $periodicity }} revenue ledger
                    @elseif($periodicSubView === 'turnover')
                        Stock movement velocity across {{ count($inventoryMovements['items']) }} products
                    @else
                        Receipt cross-reference audit
                    @endif
                </div>
            </div>

            <!-- SECTION 1: PERIODIC AGGREGATED LEDGER TABLE -->
            @if($periodicSubView === 'ledger')
                <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                    <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] space-y-3">
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 text-xs">
                            
                            <!-- Search Box -->
                            <div class="relative w-full lg:w-72">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text"
                                       wire:model.live.debounce.300ms="periodicSearch"
                                       placeholder="Filter customer, order #, product..."
                                       class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            </div>

                            <!-- Cadence Switcher: Daily, Weekly, Monthly -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Cadence:
                                </span>
                                <button type="button" wire:click="setPeriodicity('daily')"
                                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $periodicity === 'daily' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    Daily Breakdown
                                </button>
                                <button type="button" wire:click="setPeriodicity('weekly')"
                                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $periodicity === 'weekly' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    Weekly Summary
                                </button>
                                <button type="button" wire:click="setPeriodicity('monthly')"
                                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $periodicity === 'monthly' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                    Monthly Summary
                                </button>
                            </div>

                            <!-- Date Presets + Custom Range -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0">Range:</span>
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" wire:click="$set('periodicDatePreset', 'this_month')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $periodicDatePreset === 'this_month' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Month</button>
                                    <button type="button" wire:click="$set('periodicDatePreset', 'this_week')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $periodicDatePreset === 'this_week' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Week</button>
                                    <button type="button" wire:click="$set('periodicDatePreset', 'today')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $periodicDatePreset === 'today' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Today</button>
                                    <button type="button" wire:click="$set('periodicDatePreset', 'last_month')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $periodicDatePreset === 'last_month' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Last Mo</button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <input type="date" wire:model.live="periodicStartDate" class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2 py-1 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259]">
                                    <span class="text-slate-400 text-xs">to</span>
                                    <input type="date" wire:model.live="periodicEndDate" class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2 py-1 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259]">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Periodic Aggregated Ledger Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Periodic Interval</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Orders</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Completed Sales</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Layaway Deposits</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Discounts & Adj</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right font-bold text-slate-700 dark:text-slate-200">Realized Cash</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Receivables Bal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodicSummary['buckets'] as $bucket)
                                    <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                        <td class="py-2.5 px-4 font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $bucket['label'] }}</span>
                                        </td>
                                        <td class="py-2.5 px-4 text-center font-mono">
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $bucket['completed_count'] + $bucket['layaway_count'] }}</span>
                                            <span class="text-[10px] text-slate-400">({{ $bucket['total_transactions'] }} tot)</span>
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                            ₱{{ number_format($bucket['completed_revenue'], 2) }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                            ₱{{ number_format($bucket['layaway_deposits'], 2) }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono text-slate-400">
                                            -₱{{ number_format($bucket['total_discounts'], 2) }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            ₱{{ number_format($bucket['realized_cash'], 2) }}
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono text-rose-600 dark:text-rose-400">
                                            ₱{{ number_format($bucket['outstanding_balance'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                            No sales transactions found for the selected periodic parameters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-[#0f1b40] font-bold border-t-2 border-slate-300 dark:border-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-900 dark:text-white">AGGREGATE TOTALS</td>
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-900 dark:text-white">
                                        {{ $periodicSummary['totals']['completed_count'] + $periodicSummary['totals']['layaway_count'] }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-900 dark:text-white">
                                        ₱{{ number_format($periodicSummary['totals']['completed_revenue'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-900 dark:text-white">
                                        ₱{{ number_format($periodicSummary['totals']['layaway_deposits'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-slate-500">
                                        -₱{{ number_format($periodicSummary['totals']['total_discounts'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                        ₱{{ number_format($periodicSummary['totals']['realized_cash'], 2) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-rose-600 dark:text-rose-400">
                                        ₱{{ number_format($periodicSummary['totals']['outstanding_receivables'], 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            <!-- SECTION 2: STOCK TURNOVER & INVENTORY MOVEMENTS -->
            @elseif($periodicSubView === 'turnover')
                <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                    <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Stock Turnover & Inventory Movements ({{ count($inventoryMovements['items']) }} SKUs)
                        </h3>
                        <span class="text-[11px] font-mono text-blue-600 dark:text-blue-400 font-semibold">
                            Overall Turnover Velocity: {{ $inventoryMovements['overall_turnover_rate'] }}%
                        </span>
                    </div>

                    <div class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">SKU</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product Name</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Price</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Stock On Hand</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center font-bold text-slate-800 dark:text-slate-200">Units Sold</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Restocked</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Velocity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryMovements['items'] as $it)
                                    <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                        <td class="py-2 px-4 font-mono font-medium text-slate-900 dark:text-white">{{ $it['formatted_id'] }}</td>
                                        <td class="py-2 px-4 font-semibold text-slate-900 dark:text-white">{{ $it['name'] }}</td>
                                        <td class="py-2 px-4 text-slate-500 dark:text-slate-400">{{ $it['category'] }}</td>
                                        <td class="py-2 px-4 text-right font-mono text-slate-700 dark:text-slate-300">₱{{ number_format($it['tagged_price'], 2) }}</td>
                                        <td class="py-2 px-4 text-center font-mono text-slate-800 dark:text-slate-200">{{ $it['current_stock'] }}</td>
                                        <td class="py-2 px-4 text-center font-mono font-bold text-slate-900 dark:text-white">{{ $it['units_sold'] }}</td>
                                        <td class="py-2 px-4 text-center font-mono text-emerald-600 dark:text-emerald-400">+{{ $it['units_added'] }}</td>
                                        <td class="py-2 px-4 text-center">
                                            @if($it['velocity'] === 'High Velocity')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">High</span>
                                            @elseif($it['velocity'] === 'Moderate')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900">Moderate</span>
                                            @elseif($it['velocity'] === 'Slow-moving')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">Slow</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Stagnant</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-6 text-center text-xs text-slate-400">No inventory products tracked.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECTION 3: UNDERLYING TRANSACTION TRACEABILITY LEDGER -->
            @elseif($periodicSubView === 'traceability')
                <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                    <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Underlying Transaction Traceability Ledger
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Direct cross-reference for General Manager and CEO to verify underlying receipts against periodic summary figures.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Receipt #</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Purchased Items</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Order Total</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Paid</th>
                                    <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($traceableSales as $sale)
                                    <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                        <td class="py-2.5 px-4 font-mono font-medium text-slate-900 dark:text-white">{{ $sale->sale_number }}</td>
                                        <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $sale->sale_date->format('M d, Y') }}</td>
                                        <td class="py-2.5 px-4">
                                            <div class="font-semibold text-slate-900 dark:text-white">{{ $sale->customer_name }}</div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $sale->customer_phone ?: 'No phone' }}</div>
                                        </td>
                                        <td class="py-2.5 px-4">
                                            @if($sale->items->isNotEmpty())
                                                @foreach($sale->items as $it)
                                                    <div class="text-xs text-slate-800 dark:text-slate-200">
                                                        <span class="font-medium">{{ $it->product?->name ?? 'Item' }}</span>
                                                        <span class="text-slate-400 text-[11px]">&times; {{ $it->quantity }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400">{{ $sale->product_name ?: 'Custom order' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">₱{{ number_format($sale->amount, 2) }}</td>
                                        <td class="py-2.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">₱{{ number_format($sale->amount_paid, 2) }}</td>
                                        <td class="py-2.5 px-4 text-center">
                                            @if($sale->status === 'completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">Completed</span>
                                            @elseif($sale->status === 'layaway')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">Lay-away</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-xs text-slate-400">No underlying receipts found for the current period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 1: SALES & REVENUE AUDIT REPORT        -->
    <!-- ========================================== -->
    @if($activeTab === 'sales')
        <div class="space-y-4">

            <!-- Summary KPI Strip (Responsive Grid) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] mb-4">
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Realized cash revenue</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">₱{{ number_format($totalSalesRevenue, 2) }}</div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Completed orders</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ $completedTransactionsCount }} <span class="text-xs text-slate-400 font-normal">/ {{ $totalTransactionsCount }}</span></div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Average ticket</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">₱{{ number_format($averageOrderValue, 2) }}</div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Active lay-aways</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ $counts['layaway'] }} <span class="text-xs text-slate-400 font-normal font-sans">(₱{{ number_format($totalLayawayBalance, 2) }} bal)</span></div>
                </div>
            </div>

            <!-- Integrated Sales Table Workbench -->
            <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                <!-- Integrated Filters Header Bar -->
                <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] space-y-3">
                    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 text-xs">
                        <!-- Search Box -->
                        <div class="relative w-full lg:w-72">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text"
                                   wire:model.live.debounce.300ms="salesSearch"
                                   placeholder="Search customer, phone, order #..."
                                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        </div>

                        <!-- Status Navigation Buttons -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Status:
                            </span>
                            <button type="button" wire:click="setSalesStatus('ALL')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'ALL' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                All ({{ $counts['all'] }})
                            </button>
                            <button type="button" wire:click="setSalesStatus('completed')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'completed' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Completed ({{ $counts['completed'] }})
                            </button>
                            <button type="button" wire:click="setSalesStatus('layaway')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'layaway' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Lay-away ({{ $counts['layaway'] }})
                            </button>
                            <button type="button" wire:click="setSalesStatus('pending')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'pending' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Pending ({{ $counts['pending'] }})
                            </button>
                            <button type="button" wire:click="setSalesStatus('cancelled')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'cancelled' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Cancelled ({{ $counts['cancelled'] }})
                            </button>
                            <button type="button" wire:click="setSalesStatus('ARCHIVED')" class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $salesStatus === 'ARCHIVED' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                Archived ({{ $counts['archived'] }})
                            </button>
                        </div>

                        <!-- Date Presets + Custom Range -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0">Period:</span>
                            <div class="inline-flex items-center gap-1">
                                <button type="button" wire:click="$set('datePreset', 'this_month')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $datePreset === 'this_month' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Month</button>
                                <button type="button" wire:click="$set('datePreset', 'this_week')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $datePreset === 'this_week' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Week</button>
                                <button type="button" wire:click="$set('datePreset', 'today')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $datePreset === 'today' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">Today</button>
                                <button type="button" wire:click="$set('datePreset', 'all')" class="px-2 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $datePreset === 'all' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 hover:bg-slate-100' }}">All</button>
                            </div>

                            <div class="flex items-center gap-1">
                                <input type="date" wire:model.live="startDate" class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2 py-1 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259]">
                                <span class="text-slate-400 text-xs">to</span>
                                <input type="date" wire:model.live="endDate" class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2 py-1 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sales Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Order #</th>
                                <th class="py-2.5 px-4 min-w-[160px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer</th>
                                <th class="py-2.5 px-4 min-w-[200px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Item Summary</th>
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Amount</th>
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Order Date</th>
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Payment Status</th>
                                <th class="py-2.5 px-4 w-44 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors {{ $sale->is_archived ? 'opacity-60 bg-slate-50/50 dark:bg-slate-900/30' : '' }}">
                                    <td class="py-2.5 px-4 font-mono font-medium text-slate-900 dark:text-white">{{ $sale->sale_number }}</td>
                                    <td class="py-2.5 px-4">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $sale->customer_name }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $sale->customer_phone ?: 'No phone recorded' }}</div>
                                    </td>
                                    <td class="py-2.5 px-4">
                                        @if($sale->items->isNotEmpty())
                                            @foreach($sale->items as $it)
                                                <div class="text-xs text-slate-800 dark:text-slate-200">
                                                    <span class="font-medium">{{ $it->product?->name ?? 'Item' }}</span>
                                                    <span class="text-slate-400 text-[11px]">&times; {{ $it->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-slate-400">{{ $sale->product_name ?: 'Custom order' }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono tabular-nums whitespace-nowrap">
                                        <div class="font-bold text-slate-900 dark:text-white">₱{{ number_format($sale->amount, 2) }}</div>
                                        @if($sale->discount_amount > 0)
                                            @php
                                                $isPromo = !empty($sale->promo_name);
                                                $adjustmentLabel = $isPromo ? 'Promo Discount' : 'Negotiated Amount';
                                            @endphp
                                            <div class="text-[10px] text-slate-400 font-sans mt-0.5" title="{{ $adjustmentLabel }}: -₱{{ number_format($sale->discount_amount, 2) }}">
                                                ({{ $adjustmentLabel }}: -₱{{ number_format($sale->discount_amount, 2) }})
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">
                                        <div>{{ $sale->sale_date->format('M d, Y') }}</div>
                                        @if($sale->payment_type === 'layaway' && $sale->layaway_expires_at)
                                            <div class="text-[10px] {{ $sale->layaway_expires_at->isPast() ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-amber-700 dark:text-amber-400' }}">
                                                Exp: {{ $sale->layaway_expires_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                        @if($sale->is_archived)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                Archived
                                            </span>
                                        @elseif($sale->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                Completed
                                            </span>
                                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">
                                                Paid in Full
                                            </div>
                                        @elseif($sale->status === 'layaway')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                                Lay-away
                                            </span>
                                            @if($sale->remaining_balance > 0)
                                                <div class="text-[10px] text-rose-600 dark:text-rose-400 font-mono font-semibold mt-0.5">
                                                    Bal: ₱{{ number_format($sale->remaining_balance, 2) }}
                                                </div>
                                                <div class="text-[9px] text-slate-400 font-mono">
                                                    ₱{{ number_format($sale->amount_paid, 2) }} paid
                                                </div>
                                            @else
                                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">
                                                    Settled
                                                </div>
                                            @endif
                                        @elseif($sale->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button"
                                                    wire:click="openOrderModal({{ $sale->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-[#0c163b] hover:bg-slate-50 dark:hover:bg-[#1a2858] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#1a2858] text-[11px] font-semibold rounded shadow-xs transition-colors cursor-pointer"
                                                    title="View order details and item pricing breakdown">
                                                <svg class="w-3 h-3 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>View</span>
                                            </button>

                                            @if($sale->status === 'layaway' && $sale->remaining_balance > 0 && !$sale->is_archived)
                                                <a href="{{ route('layaways') }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#142259] hover:bg-[#0e1840] text-white text-[11px] font-semibold rounded shadow-xs transition-colors cursor-pointer"
                                                   title="Manage installment in Lay-Aways module">
                                                    Manage
                                                </a>
                                            @endif

                                            @if($sale->is_archived)
                                                <button type="button"
                                                        wire:click="restoreSale({{ $sale->id }})"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white dark:bg-[#0c163b] hover:bg-slate-50 dark:hover:bg-[#1a2858] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#1a2858] text-[11px] font-semibold rounded shadow-xs transition-colors cursor-pointer"
                                                        title="Restore sale record">
                                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Restore
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="archiveSale({{ $sale->id }})"
                                                        wire:confirm="Archive this sales transaction record?"
                                                        class="inline-flex items-center gap-1 px-2 py-1 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 bg-white dark:bg-[#0c163b] hover:bg-rose-50 dark:hover:bg-rose-950/20 border border-slate-200 dark:border-[#1a2858] text-[11px] font-semibold rounded shadow-xs transition-colors cursor-pointer"
                                                        title="Archive order record">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Archive
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                        No sales transactions found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($sales->hasPages())
                        <div class="p-3.5 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50 dark:bg-[#0f1b40]">
                            {{ $sales->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: INVENTORY VALUATION & STOCK REPORT  -->
    <!-- ========================================== -->
    @if($activeTab === 'inventory')
        <div class="space-y-4">

            <!-- Summary KPI Strip -->
            <div class="grid grid-cols-2 lg:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] mb-4">
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total in-stock units</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ number_format($totalStockUnits) }} <span class="text-xs text-slate-400 font-normal">units</span></div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total catalog inventory valuation</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">₱{{ number_format($totalInventoryValuation, 2) }}</div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Critical / low stock SKUs</div>
                    <div class="text-lg font-bold {{ $totalLowStockItems > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }} font-mono tabular-nums mt-0.5">{{ $totalLowStockItems }} <span class="text-xs text-slate-400 font-normal">items</span></div>
                </div>
            </div>

            <!-- Integrated Inventory Table Workbench -->
            <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                <!-- Search & Filters -->
                <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 text-xs">
                    <div class="relative w-full md:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="inventorySearch"
                               placeholder="Search product name, description..."
                               class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    </div>

                    <!-- Category & Stock Filter Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Category:
                        </span>
                        <select wire:model.live="inventoryCategory" class="bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 font-medium">
                            <option value="ALL">All Categories</option>
                            @foreach($existingCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>

                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 ml-2 mr-1 shrink-0">Stock:</span>
                        <div class="inline-flex items-center rounded border border-slate-300 dark:border-slate-700 overflow-hidden">
                            <button type="button" wire:click="$set('stockFilter', 'all')" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors {{ $stockFilter === 'all' ? 'bg-[#142259] text-white' : 'bg-white dark:bg-[#0c163b] text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">All</button>
                            <button type="button" wire:click="$set('stockFilter', 'low_stock')" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors border-l border-slate-300 dark:border-slate-700 {{ $stockFilter === 'low_stock' ? 'bg-[#142259] text-white' : 'bg-white dark:bg-[#0c163b] text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Low stock</button>
                            <button type="button" wire:click="$set('stockFilter', 'out_of_stock')" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors border-l border-slate-300 dark:border-slate-700 {{ $stockFilter === 'out_of_stock' ? 'bg-[#142259] text-white' : 'bg-white dark:bg-[#0c163b] text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }}">Out of stock</button>
                        </div>
                    </div>
                </div>

                <!-- Detailed Inventory Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product Code</th>
                                <th class="py-2.5 px-3.5 min-w-[200px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product Name & Spec</th>
                                <th class="py-2.5 px-3.5 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Unit Price</th>
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Stock Level</th>
                                <th class="py-2.5 px-3.5 w-36 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Valuation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2.5 px-3.5 font-mono font-medium text-slate-600 dark:text-slate-400">#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-sm">{{ $product->description ?: 'No material specs recorded' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                            {{ $product->category }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3.5 text-right font-medium text-slate-900 dark:text-white font-mono tabular-nums">₱{{ number_format($product->tagged_price, 2) }}</td>
                                    <td class="py-2.5 px-3.5 text-center">
                                        @if($product->quantity_in_stock <= 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">0 units</span>
                                        @elseif($product->quantity_in_stock <= 4)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">{{ $product->quantity_in_stock }} units</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">{{ $product->quantity_in_stock }} units</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3.5 text-right font-bold text-slate-900 dark:text-white font-mono tabular-nums">
                                        ₱{{ number_format($product->tagged_price * $product->quantity_in_stock, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">No inventory products found matching filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($products->hasPages())
                        <div class="p-3 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50 dark:bg-[#0f1b40]">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: LAYAWAY PORTFOLIO & RECEIVABLES     -->
    <!-- ========================================== -->
    @if($activeTab === 'layaways')
        <div class="space-y-4">

            <!-- Summary KPI Strip -->
            <div class="grid grid-cols-2 lg:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] mb-4">
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Total Outstanding Receivables</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">₱{{ number_format($totalLayawayBalance, 2) }}</div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Active Installment Accounts</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white font-mono tabular-nums mt-0.5">{{ number_format($layawayActiveCount) }} <span class="text-xs text-slate-400 font-normal">open</span></div>
                </div>
                <div class="px-4 py-3 min-w-0">
                    <div class="text-[11px] font-medium text-rose-600 dark:text-rose-400">Overdue / Delinquent Accounts</div>
                    <div class="text-lg font-bold text-rose-600 dark:text-rose-400 font-mono tabular-nums mt-0.5">{{ number_format($layawayOverdueCount) }} <span class="text-xs text-slate-400 font-normal">require follow-up</span></div>
                </div>
            </div>

            <!-- Integrated Layaway Table Workbench -->
            <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
                <!-- Search & Filters -->
                <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 text-xs">
                    <div class="relative w-full md:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="layawaySearch"
                               placeholder="Search contract #, customer name, phone..."
                               class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    </div>

                    <!-- Status Filter Controls -->
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Status:
                        </span>
                        <button type="button" wire:click="setLayawayStatus('active')" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $layawayStatus === 'active' ? 'bg-[#142259] text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Active</button>
                        <button type="button" wire:click="setLayawayStatus('near_due')" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $layawayStatus === 'near_due' ? 'bg-amber-600 text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Near Due</button>
                        <button type="button" wire:click="setLayawayStatus('overdue')" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $layawayStatus === 'overdue' ? 'bg-rose-600 text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Overdue</button>
                        <button type="button" wire:click="setLayawayStatus('settled')" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $layawayStatus === 'settled' ? 'bg-emerald-700 text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">Settled</button>
                        <button type="button" wire:click="setLayawayStatus('all')" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer transition-colors {{ $layawayStatus === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-50 dark:bg-[#0f1b40] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">All</button>
                    </div>
                </div>

                <!-- Detailed Layaway Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                                <th class="py-2.5 px-3.5 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contract #</th>
                                <th class="py-2.5 px-3.5 min-w-[160px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer</th>
                                <th class="py-2.5 px-3.5 min-w-[180px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Reserved Items</th>
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Contract</th>
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Amount Paid</th>
                                <th class="py-2.5 px-3.5 w-28 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Balance</th>
                                <th class="py-2.5 px-3.5 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Expiry / Due</th>
                                <th class="py-2.5 px-3.5 w-24 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($layaways as $c)
                                @php
                                    $isOverdue = $c->status === 'layaway' && $c->layaway_expires_at && $c->layaway_expires_at->isPast();
                                    $isNearDue = $c->status === 'layaway' && $c->layaway_expires_at && !$isOverdue && $c->layaway_expires_at->diffInDays(now()) <= 14;
                                @endphp
                                <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                                    <td class="py-2.5 px-3.5 font-mono font-medium text-slate-900 dark:text-white">{{ $c->sale_number }}</td>
                                    <td class="py-2.5 px-3.5">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $c->customer_name }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $c->customer_phone ?: 'No phone' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        @if($c->items->isNotEmpty())
                                            @foreach($c->items as $it)
                                                <div class="text-xs text-slate-700 dark:text-slate-300 py-0.5">
                                                    <span class="font-medium">{{ $it->product?->name ?? 'Item' }}</span>
                                                    <span class="text-slate-400 text-[11px]">&times; {{ $it->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-slate-400">{{ $c->product_name ?: 'Layaway items' }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3.5 text-right font-medium text-slate-900 dark:text-white font-mono tabular-nums">₱{{ number_format($c->amount, 2) }}</td>
                                    <td class="py-2.5 px-3.5 text-right font-medium text-emerald-600 dark:text-emerald-400 font-mono tabular-nums">₱{{ number_format($c->amount_paid, 2) }}</td>
                                    <td class="py-2.5 px-3.5 text-right font-bold text-slate-900 dark:text-white font-mono tabular-nums">
                                        @if($c->status === 'completed')
                                            <span class="text-emerald-600">₱0.00</span>
                                        @else
                                            ₱{{ number_format($c->remaining_balance, 2) }}
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        @if($c->layaway_expires_at)
                                            <div class="font-medium text-slate-900 dark:text-white">{{ $c->layaway_expires_at->format('M d, Y') }}</div>
                                            @if($isOverdue)
                                                <span class="text-[10px] text-rose-600 font-bold">Overdue</span>
                                            @elseif($isNearDue)
                                                <span class="text-[10px] text-amber-600 font-semibold">&le; 14 days left</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3.5 text-center">
                                        @if($c->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                Settled
                                            </span>
                                        @elseif($c->status === 'cancelled')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                                Cancelled
                                            </span>
                                        @elseif($isOverdue)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                                Overdue
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">No layaway contracts found matching current filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($layaways->hasPages())
                        <div class="p-3 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50 dark:bg-[#0f1b40]">
                            {{ $layaways->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @endif

    <!-- ORDER DETAILS / PRICING BREAKDOWN MODAL -->
    @if(!empty($showOrderModal) && !empty($selectedOrderData))
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs animate-in fade-in duration-200">
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] shadow-2xl max-w-lg w-full overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between bg-slate-50 dark:bg-[#0f1b40]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded bg-[#142259] text-white flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white font-mono">
                                    {{ $selectedOrderData['order_number'] }}
                                </h3>
                                @if($selectedOrderData['is_archived'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        Archived
                                    </span>
                                @elseif($selectedOrderData['status'] === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                        Completed
                                    </span>
                                @elseif($selectedOrderData['status'] === 'layaway')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                        Lay-away
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                        {{ ucfirst($selectedOrderData['status']) }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Order & Negotiated Pricing Details</p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeOrderModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded hover:bg-slate-100 dark:hover:bg-[#1a2858] transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-200 dark:divide-[#1a2858]">
                    <!-- Customer & Transaction Context -->
                    <div class="p-4 sm:p-5 space-y-2 text-xs bg-white dark:bg-[#0c163b]">
                        <div class="grid grid-cols-2 gap-3 text-slate-600 dark:text-slate-300">
                            <div>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Customer</span>
                                <div class="font-semibold text-slate-900 dark:text-white text-xs mt-0.5">{{ $selectedOrderData['customer_name'] }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $selectedOrderData['customer_phone'] }}</div>
                                @if(!empty($selectedOrderData['customer_email']))
                                    <div class="text-[11px] text-slate-400 truncate">{{ $selectedOrderData['customer_email'] }}</div>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Order Date & Time</span>
                                <div class="text-slate-900 dark:text-white font-medium mt-0.5">{{ $selectedOrderData['sale_date'] }}</div>
                                <div class="text-[11px] text-slate-400 mt-1">Processed by: <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $selectedOrderData['cashier'] }}</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items Breakdown with SRP vs Agreed Price -->
                    <div class="p-4 sm:p-5 space-y-2.5 text-xs bg-slate-50/50 dark:bg-[#0f1b40]/40">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 pb-1.5 border-b border-slate-200 dark:border-slate-800 flex justify-between">
                            <span>Item Breakdown</span>
                            <span>Subtotal</span>
                        </div>
                        @forelse($selectedOrderData['items'] as $item)
                            <div class="flex justify-between items-start py-1 border-b border-dashed border-slate-100 dark:border-[#1a2858]/60 last:border-b-0">
                                <div class="flex-1 pr-3">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                                    @if(!empty($item['is_negotiated']) && ($item['srp'] ?? 0) > $item['unit_price'])
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex flex-wrap items-center gap-1.5">
                                            <span class="line-through text-slate-400">SRP ₱{{ number_format($item['srp'], 2) }}</span>
                                            <span class="text-amber-700 dark:text-amber-400 font-semibold">→ Agreed ₱{{ number_format($item['unit_price'], 2) }}</span>
                                            <span class="text-slate-500">({{ $item['quantity'] }} &times; ₱{{ number_format($item['unit_price'], 2) }})</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            {{ $item['quantity'] }} &times; ₱{{ number_format($item['unit_price'], 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="font-mono font-bold text-slate-900 dark:text-white text-right shrink-0">
                                    ₱{{ number_format($item['subtotal'], 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-slate-400 py-2 text-center">No individual items recorded.</div>
                        @endforelse
                    </div>

                    <!-- Financial Summary & Concessions -->
                    <div class="p-4 sm:p-5 space-y-2 text-xs bg-white dark:bg-[#0c163b]">
                        @if(!empty($selectedOrderData['has_negotiated_items']) && ($selectedOrderData['negotiated_concession_total'] ?? 0) > 0)
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Gross SRP Total:</span>
                                <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($selectedOrderData['gross_srp_total'] ?? $selectedOrderData['total_amount'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-amber-700 dark:text-amber-400 font-semibold">
                                <span>Price Concession (Negotiated):</span>
                                <span class="font-mono">-₱{{ number_format($selectedOrderData['negotiated_concession_total'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                <span>Agreed Subtotal:</span>
                                <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($selectedOrderData['total_amount'], 2) }}</span>
                            </div>
                        @else
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Subtotal:</span>
                                <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($selectedOrderData['total_amount'], 2) }}</span>
                            </div>
                        @endif

                        @if(!empty($selectedOrderData['promo_discount_amount']) && $selectedOrderData['promo_discount_amount'] > 0)
                            <div class="flex justify-between text-emerald-700 dark:text-emerald-400 font-medium">
                                <span>Promo Discount ({{ $selectedOrderData['promo_name'] ?? 'Applied' }}):</span>
                                <span class="font-mono">-₱{{ number_format($selectedOrderData['promo_discount_amount'], 2) }}</span>
                            </div>
                        @elseif(!empty($selectedOrderData['discount_amount']) && $selectedOrderData['discount_amount'] > 0 && empty($selectedOrderData['has_negotiated_items']))
                            <div class="flex justify-between text-amber-700 dark:text-amber-400 font-medium">
                                <span>Discount ({{ $selectedOrderData['promo_name'] ?? 'Applied' }}):</span>
                                <span class="font-mono">-₱{{ number_format($selectedOrderData['discount_amount'], 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm font-bold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
                            <span>Total Payable:</span>
                            <span class="font-mono text-base text-[#142259] dark:text-white">₱{{ number_format($selectedOrderData['net_amount'], 2) }}</span>
                        </div>

                        <!-- Settlement Breakdown -->
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1.5">
                            <div class="flex justify-between text-slate-600 dark:text-slate-400 text-[11px]">
                                <span>Settlement Method:</span>
                                <span class="font-semibold text-slate-900 dark:text-white">
                                    {{ $selectedOrderData['payment_type'] === 'layaway' ? 'Lay-Away Contract' : 'Full Settlement' }}
                                </span>
                            </div>

                            @if($selectedOrderData['payment_type'] === 'full')
                                <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                    <span>Amount Paid:</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-white">₱{{ number_format($selectedOrderData['cash_tendered'], 2) }}</span>
                                </div>
                                @if(($selectedOrderData['change_due'] ?? 0) > 0)
                                    <div class="flex justify-between text-slate-500 dark:text-slate-400 text-[11px]">
                                        <span>Change Due:</span>
                                        <span class="font-mono text-slate-700 dark:text-slate-300">₱{{ number_format($selectedOrderData['change_due'], 2) }}</span>
                                    </div>
                                @endif
                            @else
                                <div class="flex justify-between text-emerald-700 dark:text-emerald-400 font-medium">
                                    <span>Total Paid to Date:</span>
                                    <span class="font-mono">₱{{ number_format($selectedOrderData['amount_paid'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-rose-600 dark:text-rose-400 font-bold">
                                    <span>Remaining Balance:</span>
                                    <span class="font-mono">₱{{ number_format($selectedOrderData['remaining_balance'], 2) }}</span>
                                </div>
                                @if(!empty($selectedOrderData['expiry_date']))
                                    <div class="flex justify-between text-slate-500 text-[11px]">
                                        <span>Lay-Away Expiry:</span>
                                        <span class="font-mono text-amber-600 dark:text-amber-400 font-medium">{{ $selectedOrderData['expiry_date'] }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        @if(!empty($selectedOrderData['notes']))
                            <div class="pt-2 border-t border-slate-200 dark:border-slate-800">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Notes</span>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-0.5">{{ $selectedOrderData['notes'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-5 py-3.5 border-t border-slate-200 dark:border-[#1a2858] flex items-center justify-between gap-3 bg-slate-50 dark:bg-[#0f1b40]">
                    <a href="{{ route('sales.receipt', $selectedOrderData['order_number']) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#142259] dark:text-blue-400 hover:underline">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Open Printable Receipt</span>
                    </a>
                    <button type="button"
                            wire:click="closeOrderModal"
                            class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
