<div class="space-y-7" x-data="adminAnalyticsBi()">
    
    <!-- ============================================================== -->
    <!-- 1. TOP HEADER & EXECUTIVE FILTER CONTROLS                      -->
    <!-- ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-100 flex flex-col gap-6">
        
        <!-- Header Title & Badges -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase bg-[#142259] text-amber-300 shadow-xs border border-amber-400/30">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/></svg>
                        <span>ADMIN EXECUTIVE SUITE</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>LIVE COMPUTED BI</span>
                    </span>
                </div>
                <h2 class="text-[#142259] text-2xl lg:text-3xl font-black tracking-tight leading-none uppercase">
                    BUSINESS INTELLIGENCE & ANALYTICS
                </h2>
                <p class="text-slate-500 text-xs font-medium mt-1">
                    Multi-dimensional performance analytics, cash flow realization, layaway credit aging, and inventory health matrix.
                </p>
            </div>

            <!-- Action Controls (Print Report & Fast Recalculate) -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Print Report Button -->
                <button type="button" onclick="window.print()"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>PRINT BI DOSSIER</span>
                </button>

                <!-- Refresh Livewire Signal -->
                <button type="button" wire:click="$refresh"
                        class="bg-[#142259] hover:bg-blue-900 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>SYNC DATA</span>
                </button>
            </div>
        </div>

        <!-- Filter Slices: Timeframe Pills & Dropdowns -->
        <div class="flex flex-col 2xl:flex-row items-stretch 2xl:items-center justify-between gap-4">
            
            <!-- Timeframe Preset Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none flex-nowrap">
                @php
                    $timeframeOptions = [
                        '7d' => 'Last 7 Days',
                        '30d' => 'Last 30 Days',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'quarter' => 'Quarter (90d)',
                        'ytd' => 'YTD 2026',
                        'all' => 'All Time',
                        'custom' => 'Custom',
                    ];
                @endphp

                @foreach($timeframeOptions as $key => $label)
                    <button type="button" wire:click="setTimeframe('{{ $key }}')"
                            class="px-3.5 py-2 rounded-xl text-xs font-extrabold tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $timeframe === $key ? 'bg-[#1558bf] text-white shadow-sm font-black' : 'bg-slate-50 hover:bg-slate-100 text-slate-600' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Dropdown Filters (Category & Payment Type) -->
            <div class="flex items-center gap-3 flex-wrap 2xl:flex-nowrap">
                <!-- Custom Date Inputs (when 'custom' is active) -->
                @if($timeframe === 'custom')
                    <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200">
                        <input type="date" wire:model.live="customStartDate" class="bg-white text-xs border border-slate-200 rounded-lg px-2.5 py-1 text-slate-700 font-semibold focus:outline-none">
                        <span class="text-xs text-slate-400 font-bold">to</span>
                        <input type="date" wire:model.live="customEndDate" class="bg-white text-xs border border-slate-200 rounded-lg px-2.5 py-1 text-slate-700 font-semibold focus:outline-none">
                    </div>
                @endif

                <!-- Category Slice -->
                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <span class="text-[11px] font-black uppercase text-slate-400">Category:</span>
                    <select wire:model.live="categoryFilter" class="bg-transparent text-xs font-bold text-slate-800 border-none focus:outline-none cursor-pointer">
                        <option value="ALL">All Categories</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Type Slice -->
                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <span class="text-[11px] font-black uppercase text-slate-400">Payment:</span>
                    <select wire:model.live="paymentFilter" class="bg-transparent text-xs font-bold text-slate-800 border-none focus:outline-none cursor-pointer">
                        <option value="ALL">All Methods</option>
                        <option value="full">Full Payment (Cash)</option>
                        <option value="layaway">Lay-Away Plan</option>
                    </select>
                </div>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 2. EXECUTIVE KPI SCORECARDS (6 STRATEGIC PILLARS)              -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        
        <!-- KPI 1: Gross Sales Revenue -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-[#1558bf] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">GROSS SALES</span>
                    @if($revenueGrowth >= 0)
                        <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">+{{ $revenueGrowth }}%</span>
                    @else
                        <span class="text-[10px] font-extrabold text-red-700 bg-red-50 px-2 py-0.5 rounded-full">{{ $revenueGrowth }}%</span>
                    @endif
                </div>
                <h3 class="text-[#142259] text-2xl font-black tracking-tight leading-tight">₱{{ number_format($grossRevenue, 2) }}</h3>
            </div>
            <p class="text-slate-400 text-[11px] font-semibold mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span>{{ $totalOrdersCount }} orders</span>
                <span>vs prev period</span>
            </p>
        </div>

        <!-- KPI 2: Net Realized Cash Flow -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-emerald-500 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">REALIZED CASH</span>
                    <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $realizationRate }}% Coll.</span>
                </div>
                <h3 class="text-emerald-600 text-2xl font-black tracking-tight leading-tight">₱{{ number_format($realizedCash, 2) }}</h3>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100">
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, $realizationRate) }}%;"></div>
                </div>
                <p class="text-slate-400 text-[10px] font-semibold mt-1">Cash in bank realization</p>
            </div>
        </div>

        <!-- KPI 3: Layaway Receivables Portfolio -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-amber-500 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">LAYAWAY RECEIVABLES</span>
                    <span class="text-[10px] font-extrabold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full">{{ $activeLayawaysCount }} Active</span>
                </div>
                <h3 class="text-amber-600 text-2xl font-black tracking-tight leading-tight">₱{{ number_format($totalOutstandingReceivables, 2) }}</h3>
            </div>
            <p class="text-slate-400 text-[11px] font-semibold mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span>Credit exposure</span>
                <span class="text-amber-600 font-bold">90d Horizon</span>
            </p>
        </div>

        <!-- KPI 4: Average Ticket (AOV) -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-indigo-600 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">AVG ORDER VALUE</span>
                    <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">AOV</span>
                </div>
                <h3 class="text-[#142259] text-2xl font-black tracking-tight leading-tight">₱{{ number_format($averageOrderValue, 2) }}</h3>
            </div>
            <p class="text-slate-400 text-[11px] font-semibold mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span>Per transaction</span>
                <span>Retail avg</span>
            </p>
        </div>

        <!-- KPI 5: Warehouse Asset Valuation -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-purple-600 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">INVENTORY ASSET</span>
                    <span class="text-[10px] font-extrabold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full">{{ $totalStockUnits }} Units</span>
                </div>
                <h3 class="text-purple-700 text-2xl font-black tracking-tight leading-tight">₱{{ number_format($inventoryValuation, 2) }}</h3>
            </div>
            <p class="text-slate-400 text-[11px] font-semibold mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span>Warehouse Stock</span>
                <span class="{{ $lowStockProductsCount > 0 ? 'text-red-500 font-bold' : 'text-emerald-500 font-bold' }}">{{ $lowStockProductsCount }} low stock</span>
            </p>
        </div>

        <!-- KPI 6: Promo Discounts Granted -->
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 border-t-4 border-t-rose-500 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">PROMO CONCESSIONS</span>
                    <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">DISCOUNT</span>
                </div>
                <h3 class="text-rose-600 text-2xl font-black tracking-tight leading-tight">₱{{ number_format($totalDiscountsGiven, 2) }}</h3>
            </div>
            <p class="text-slate-400 text-[11px] font-semibold mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                <span>Campaign sales</span>
                <span class="text-rose-600 font-bold">Incentives</span>
            </p>
        </div>

    </div>

    <!-- ============================================================== -->
    <!-- 3. STRATEGIC BI EXECUTIVE INSIGHTS DECK                         -->
    <!-- ============================================================== -->
    @if(!empty($biInsights))
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-[#142259] flex items-center justify-center font-black">
                        💡
                    </div>
                    <div>
                        <h4 class="text-[#142259] text-sm font-black uppercase tracking-wider">AI Executive Briefing & Algorithmic Signals</h4>
                        <p class="text-slate-400 text-xs">Automated performance diagnosis and operational risk indicators</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-3 py-1 rounded-full">
                    {{ count($biInsights) }} Active Signals
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-2">
                @foreach($biInsights as $ins)
                    @php
                        $colorClass = match($ins['color']) {
                            'blue' => 'border-l-blue-500 bg-blue-50/30 text-blue-900',
                            'emerald' => 'border-l-emerald-500 bg-emerald-50/30 text-emerald-900',
                            'amber' => 'border-l-amber-500 bg-amber-50/30 text-amber-900',
                            'red' => 'border-l-red-500 bg-red-50/30 text-red-900',
                            'purple' => 'border-l-purple-500 bg-purple-50/30 text-purple-900',
                            default => 'border-l-indigo-500 bg-indigo-50/30 text-indigo-900',
                        };
                        $badgeClass = match($ins['color']) {
                            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'red' => 'bg-red-100 text-red-800 border-red-200',
                            'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
                            default => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        };
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-100 border-l-4 {{ $colorClass }} flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded border {{ $badgeClass }}">
                                    {{ $ins['badge'] }}
                                </span>
                            </div>
                            <h5 class="text-xs font-black text-slate-900 leading-snug">
                                {{ $ins['title'] }}
                            </h5>
                            <p class="text-slate-600 text-[11px] font-medium leading-relaxed mt-1.5">
                                {{ $ins['body'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- 4. SUB-NAVIGATION TABS                                          -->
    <!-- ============================================================== -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none border-b border-slate-200/80">
        <button type="button" wire:click="setTab('overview')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'overview' ? 'bg-[#142259] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>EXECUTIVE CHARTS & OVERVIEW</span>
        </button>

        <button type="button" wire:click="setTab('layaways')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'layaways' ? 'bg-[#142259] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>LAY-AWAY PORTFOLIO & CREDIT RISK ({{ $activeLayawaysCount }})</span>
        </button>

        <button type="button" wire:click="setTab('products')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'products' ? 'bg-[#142259] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>PRODUCT PROFITABILITY & VELOCITY</span>
        </button>

        <button type="button" wire:click="setTab('staff')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'staff' ? 'bg-[#142259] text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 font-bold' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>SALES TEAM & SECRETARY PERFORMANCE</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: EXECUTIVE CHARTS & OVERVIEW                             -->
    <!-- ============================================================== -->
    @if($activeTab === 'overview')
        <div class="space-y-6">
            
            <!-- ROW 1: REVENUE TRAJECTORY (2/3) + CATEGORY DONUT (1/3) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Chart 1: Revenue & Cash Flow Trend (Spline Area) -->
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#1558bf]"></span>
                                <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">REVENUE & CASH REALIZATION TRAJECTORY</h4>
                            </div>
                            <p class="text-slate-400 text-xs mt-0.5">Dual-axis tracking of Billed Gross Revenue vs Realized Cash Inflows</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs font-bold">
                            <span class="flex items-center gap-1.5 text-[#1558bf]">
                                <span class="w-3 h-3 rounded bg-[#1558bf]"></span> Gross Sales
                            </span>
                            <span class="flex items-center gap-1.5 text-emerald-600">
                                <span class="w-3 h-3 rounded bg-emerald-500"></span> Realized Cash
                            </span>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div id="chart-revenue-trajectory" class="w-full min-h-[340px]"></div>
                </div>

                <!-- Chart 2: Category Revenue Breakdown (Donut) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span>
                            <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">CATEGORY MARKET SHARE</h4>
                        </div>
                        <p class="text-slate-400 text-xs mt-0.5">Revenue proportion across furniture fabrication types</p>
                    </div>

                    <!-- Donut Container -->
                    <div id="chart-category-donut" class="w-full min-h-[300px] flex items-center justify-center"></div>

                    <!-- Category Breakdown Table Mini -->
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                        @foreach($categoryLabels as $idx => $cLabel)
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-700">{{ $cLabel }}</span>
                                <span class="text-[#142259] font-black">₱{{ number_format($categorySeries[$idx] ?? 0, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- ROW 2: TOP PRODUCTS (1/2) + LAYAWAY HORIZON (1/2) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart 3: Top Selling & High Grossing Products (Bar) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">TOP GROSSING PRODUCT LINES</h4>
                            </div>
                            <p class="text-slate-400 text-xs mt-0.5">Top revenue contributors sorted by gross volume</p>
                        </div>
                        <span class="text-xs font-extrabold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full">Top 7</span>
                    </div>

                    <div id="chart-top-products" class="w-full min-h-[320px]"></div>
                </div>

                <!-- Chart 4: Layaway Health & Credit Aging Horizon (Stacked Bar) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">LAYAWAY MATURITY & CREDIT AGING</h4>
                            </div>
                            <p class="text-slate-400 text-xs mt-0.5">3-Month Expiry timeline analysis of downpayments vs balance</p>
                        </div>
                        <span class="text-xs font-extrabold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full">90-Day Policy</span>
                    </div>

                    <div id="chart-layaway-aging" class="w-full min-h-[320px]"></div>
                </div>

            </div>

            <!-- ROW 3: PAYMENT METHOD (1/2) + INVENTORY HEALTH MATRIX (1/2) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart 5: Payment Method Composition (Donut) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">PAYMENT CHANNEL BREAKDOWN</h4>
                        </div>
                        <p class="text-slate-400 text-xs mt-0.5">Direct Settlement vs Structured Lay-Away Inflows</p>
                    </div>

                    <div id="chart-payment-methods" class="w-full min-h-[260px] flex items-center justify-center"></div>

                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-3 text-center">
                        <div class="bg-slate-50 p-2.5 rounded-2xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">FULL CASH SALES</p>
                            <p class="text-xs font-black text-emerald-600 mt-0.5">₱{{ number_format($paymentMethodSeries[0] ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-2xl">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">LAYAWAY DOWNPAYMENTS</p>
                            <p class="text-xs font-black text-amber-600 mt-0.5">₱{{ number_format($paymentMethodSeries[1] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chart 6: Warehouse Stock Health (Bar) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                    <div class="pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <h4 class="text-[#142259] text-base font-black tracking-wide uppercase">WAREHOUSE REPLENISHMENT STATUS</h4>
                        </div>
                        <p class="text-slate-400 text-xs mt-0.5">Active SKU distribution across buffer thresholds</p>
                    </div>

                    <div id="chart-inventory-health" class="w-full min-h-[260px]"></div>

                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-emerald-50/60 p-2 rounded-xl border border-emerald-100">
                            <span class="font-extrabold text-emerald-700">{{ $inventoryHealthSeries[0] ?? 0 }} SKUs</span>
                            <p class="text-[10px] text-emerald-600 mt-0.5 font-bold">Healthy Stock</p>
                        </div>
                        <div class="bg-amber-50/60 p-2 rounded-xl border border-amber-100">
                            <span class="font-extrabold text-amber-700">{{ $inventoryHealthSeries[1] ?? 0 }} SKUs</span>
                            <p class="text-[10px] text-amber-600 mt-0.5 font-bold">Low Stock (≤4)</p>
                        </div>
                        <div class="bg-red-50/60 p-2 rounded-xl border border-red-100">
                            <span class="font-extrabold text-red-700">{{ $inventoryHealthSeries[2] ?? 0 }} SKUs</span>
                            <p class="text-[10px] text-red-600 mt-0.5 font-bold">Depleted (0)</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: LAYAWAY PORTFOLIO & CREDIT RISK HORIZON                 -->
    <!-- ============================================================== -->
    @if($activeTab === 'layaways')
        <div class="space-y-6">
            
            <!-- Summary Banner -->
            <div class="bg-gradient-to-r from-[#142259] to-[#1558bf] rounded-3xl p-7 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="text-amber-300 text-xs font-black tracking-widest uppercase bg-white/10 px-3 py-1 rounded-full border border-white/20">
                        RECEIVABLES RADAR
                    </span>
                    <h3 class="text-2xl font-black mt-3">₱{{ number_format($totalOutstandingReceivables, 2) }} In Pending Customer Balances</h3>
                    <p class="text-blue-100 text-xs font-normal mt-1 max-w-xl">
                        Customers with lay-away plans are held to a strict 3-month (90-day) settlement window from payment date. Monitor risk horizons to ensure timely collection.
                    </p>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/15 text-center">
                        <span class="text-2xl font-black text-amber-300 leading-none">{{ $activeLayawaysCount }}</span>
                        <p class="text-[10px] font-extrabold uppercase text-white/80 mt-1">Active Plans</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/15 text-center">
                        <span class="text-2xl font-black text-emerald-300 leading-none">₱{{ number_format(\App\Models\Sale::where('status', 'layaway')->sum('amount_paid'), 2) }}</span>
                        <p class="text-[10px] font-extrabold uppercase text-white/80 mt-1">Paid In Advance</p>
                    </div>
                </div>
            </div>

            <!-- Detailed Layaway Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-[#142259] font-black text-base uppercase tracking-wider">Active Customer Layaway Portfolio</h4>
                        <p class="text-slate-400 text-xs mt-0.5">Chronologically ordered by nearest 3-month expiration date</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                                <th class="py-4 px-6">ORDER #</th>
                                <th class="py-4 px-6">CUSTOMER & CONTACT</th>
                                <th class="py-4 px-6">PRODUCT SPECIFICATION</th>
                                <th class="py-4 px-6">TOTAL PRICE</th>
                                <th class="py-4 px-6">PAID AMOUNT</th>
                                <th class="py-4 px-6">BALANCE DUE</th>
                                <th class="py-4 px-6">EXPIRY DATE</th>
                                <th class="py-4 px-6 text-right">RISK STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px]">
                            @forelse($detailedLayaways as $item)
                                @php $s = $item['sale']; @endphp
                                <tr class="hover:bg-blue-50/20 transition">
                                    <td class="py-4 px-6 font-bold text-blue-600 whitespace-nowrap">{{ $s->sale_number }}</td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800">{{ $s->customer_name }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $s->customer_phone }} • {{ $s->customer_address }}</div>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-700">{{ $s->product_name }}</td>
                                    <td class="py-4 px-6 font-black text-slate-900 whitespace-nowrap">₱{{ number_format($s->amount, 2) }}</td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="font-extrabold text-emerald-600">₱{{ number_format($s->amount_paid, 2) }}</span>
                                        <div class="text-[10px] text-slate-400 font-semibold">{{ $item['paid_pct'] }}% settled</div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="font-black text-amber-600 text-sm">₱{{ number_format($s->remaining_balance, 2) }}</span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="font-bold text-slate-700">
                                            {{ $s->layaway_expires_at ? $s->layaway_expires_at->format('M d, Y') : '—' }}
                                        </div>
                                        @if($item['days_left'] < 0)
                                            <span class="text-[10px] font-black text-red-600">Overdue by {{ abs($item['days_left']) }} days</span>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-400">{{ $item['days_left'] }} days remaining</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        @if($item['badge'] === 'critical')
                                            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-800 text-xs font-black px-3 py-1 rounded-full animate-pulse border border-red-200">
                                                <span>⚠️ OVERDUE</span>
                                            </span>
                                        @elseif($item['badge'] === 'warning')
                                            <span class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 text-xs font-extrabold px-3 py-1 rounded-full border border-rose-200">
                                                <span>🚨 EXPIRING SOON</span>
                                            </span>
                                        @elseif($item['badge'] === 'notice')
                                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 text-xs font-extrabold px-3 py-1 rounded-full border border-amber-200">
                                                <span>⏳ UPCOMING DUE</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">
                                                <span>✅ ON-TRACK</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 font-medium">
                                        No active lay-away credit accounts in the system.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: PRODUCT PROFITABILITY & VELOCITY                        -->
    <!-- ============================================================== -->
    @if($activeTab === 'products')
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-[#142259] font-black text-base uppercase tracking-wider">Product Sales Velocity & Replenishment Matrix</h4>
                        <p class="text-slate-400 text-xs mt-0.5">Ranked by revenue contribution across the selected timeframe</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                                <th class="py-4 px-6">PRODUCT LINE</th>
                                <th class="py-4 px-6">CATEGORY</th>
                                <th class="py-4 px-6">TAGGED UNIT PRICE</th>
                                <th class="py-4 px-6">UNITS SOLD</th>
                                <th class="py-4 px-6">GROSS REVENUE</th>
                                <th class="py-4 px-6">REMAINING STOCK</th>
                                <th class="py-4 px-6 text-right">VELOCITY STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px]">
                            @forelse($topProducts as $prod)
                                <tr class="hover:bg-blue-50/20 transition">
                                    <td class="py-4 px-6 font-bold text-slate-800">{{ $prod['name'] }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                            {{ $prod['category'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-700 whitespace-nowrap">₱{{ number_format($prod['tagged_price'], 2) }}</td>
                                    <td class="py-4 px-6 font-black text-[#142259]">{{ $prod['units_sold'] }} units</td>
                                    <td class="py-4 px-6 font-black text-emerald-600 whitespace-nowrap">₱{{ number_format($prod['gross_revenue'], 2) }}</td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold {{ $prod['stock'] <= 4 ? 'text-red-600' : 'text-slate-800' }}">{{ $prod['stock'] }} in warehouse</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        @if($prod['stock'] <= 0)
                                            <span class="bg-red-50 text-red-700 text-xs font-black px-3 py-1 rounded-full border border-red-200">OUT OF STOCK</span>
                                        @elseif($prod['stock'] <= 4)
                                            <span class="bg-amber-50 text-amber-700 text-xs font-black px-3 py-1 rounded-full border border-amber-200">LOW STOCK (REORDER)</span>
                                        @else
                                            <span class="bg-emerald-50 text-emerald-700 text-xs font-black px-3 py-1 rounded-full border border-emerald-200">HEALTHY RUNWAY</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                        No product sales records found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 4: SALES TEAM & SECRETARY PERFORMANCE                      -->
    <!-- ============================================================== -->
    @if($activeTab === 'staff')
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-[#142259] font-black text-base uppercase tracking-wider">Internal Sales Team Leaderboard</h4>
                        <p class="text-slate-400 text-xs mt-0.5">Audit log of sales recorded per team member</p>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                                <th class="py-4 px-6">STAFF MEMBER</th>
                                <th class="py-4 px-6">DESIGNATION</th>
                                <th class="py-4 px-6">DEALS RECORDED</th>
                                <th class="py-4 px-6">GROSS REVENUE BILLED</th>
                                <th class="py-4 px-6">REALIZED CASH COLLECTED</th>
                                <th class="py-4 px-6 text-right">VOLUME CONTRIBUTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px]">
                            @forelse($staffPerformance as $member)
                                <tr class="hover:bg-blue-50/20 transition">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-800">{{ $member['name'] }}</div>
                                        <div class="text-xs text-slate-400">{{ $member['email'] }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $member['role'] === 'ADMIN' ? 'bg-blue-100 text-blue-800' : ($member['role'] === 'MANAGER' ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $member['role'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-black text-[#142259]">{{ $member['deals_count'] }} orders</td>
                                    <td class="py-4 px-6 font-black text-slate-900 whitespace-nowrap">₱{{ number_format($member['gross_volume'], 2) }}</td>
                                    <td class="py-4 px-6 font-black text-emerald-600 whitespace-nowrap">₱{{ number_format($member['realized_cash'], 2) }}</td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="font-black text-[#142259]">{{ $member['contribution_pct'] }}%</span>
                                            <div class="w-16 bg-slate-100 h-2 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#1558bf] rounded-full" style="width: {{ min(100, $member['contribution_pct']) }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                        No sales performance data available.
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
function adminAnalyticsBi() {
    return {
        charts: {},
        init() {
            this.$nextTick(() => {
                this.renderAllCharts();
            });

            // Listen to Livewire DOM updates to refresh charts reactively
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(() => {
                    this.$nextTick(() => {
                        this.renderAllCharts();
                    });
                });
            });
        },
        destroyChart(key) {
            if (this.charts[key]) {
                try {
                    this.charts[key].destroy();
                } catch(e) {}
                delete this.charts[key];
            }
        },
        renderAllCharts() {
            if (typeof ApexCharts === 'undefined') return;

            // Chart 1: Revenue & Cash Trajectory
            const trajEl = document.querySelector("#chart-revenue-trajectory");
            if (trajEl) {
                this.destroyChart('trajectory');
                const trajOptions = {
                    series: [
                        { name: 'Gross Sales (₱)', data: @json($trajectoryRevenue) },
                        { name: 'Realized Cash (₱)', data: @json($trajectoryCash) }
                    ],
                    chart: {
                        type: 'area',
                        height: 340,
                        toolbar: { show: true, tools: { download: true, selection: false, zoom: false, pan: false } },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#1558bf', '#10b981'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [20, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: [3, 3] },
                    xaxis: {
                        categories: @json($trajectoryLabels),
                        labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#64748b' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => '₱' + Number(val).toLocaleString(),
                            style: { fontSize: '11px', fontWeight: 600, colors: '#64748b' }
                        }
                    },
                    tooltip: {
                        y: { formatter: (val) => '₱' + Number(val).toLocaleString() }
                    },
                    legend: { show: false }
                };
                this.charts['trajectory'] = new ApexCharts(trajEl, trajOptions);
                this.charts['trajectory'].render();
            }

            // Chart 2: Category Donut
            const catEl = document.querySelector("#chart-category-donut");
            if (catEl) {
                this.destroyChart('category');
                const catSeries = @json($categorySeries).map(v => Number(v));
                const catOptions = {
                    series: catSeries.length > 0 ? catSeries : [1],
                    labels: catSeries.length > 0 ? @json($categoryLabels) : ['No Data'],
                    chart: {
                        type: 'donut',
                        height: 300,
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#1558bf', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899'],
                    legend: { position: 'bottom', fontSize: '12px', fontWeight: 600 },
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val.toFixed(1) + '%'
                    },
                    tooltip: {
                        y: { formatter: (val) => '₱' + Number(val).toLocaleString() }
                    }
                };
                this.charts['category'] = new ApexCharts(catEl, catOptions);
                this.charts['category'].render();
            }

            // Chart 3: Top Products Bar
            const prodEl = document.querySelector("#chart-top-products");
            if (prodEl) {
                this.destroyChart('topProducts');
                const prodOptions = {
                    series: [{
                        name: 'Gross Revenue (₱)',
                        data: @json($topProductRevenue)
                    }],
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            dataLabels: { position: 'top' }
                        }
                    },
                    colors: ['#8b5cf6'],
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => '₱' + Number(val).toLocaleString(),
                        offsetX: 30,
                        style: { fontSize: '11px', fontWeight: 700, colors: ['#142259'] }
                    },
                    xaxis: {
                        categories: @json($topProductNames),
                        labels: {
                            formatter: (val) => '₱' + Number(val).toLocaleString(),
                            style: { fontSize: '10px', fontWeight: 600, colors: '#64748b' }
                        }
                    },
                    yaxis: {
                        labels: { style: { fontSize: '11px', fontWeight: 700, colors: '#1e293b' } }
                    },
                    tooltip: {
                        y: { formatter: (val) => '₱' + Number(val).toLocaleString() }
                    }
                };
                this.charts['topProducts'] = new ApexCharts(prodEl, prodOptions);
                this.charts['topProducts'].render();
            }

            // Chart 4: Layaway Aging Stacked Bar
            const agingEl = document.querySelector("#chart-layaway-aging");
            if (agingEl) {
                this.destroyChart('aging');
                const agingOptions = {
                    series: [
                        { name: 'Paid Downpayment (₱)', data: @json($agingPaidSeries) },
                        { name: 'Unpaid Balance (₱)', data: @json($agingBalanceSeries) }
                    ],
                    chart: {
                        type: 'bar',
                        height: 320,
                        stacked: true,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#10b981', '#f59e0b'],
                    plotOptions: {
                        bar: { borderRadius: 5, columnWidth: '40%' }
                    },
                    xaxis: {
                        categories: @json($agingCategories),
                        labels: { style: { fontSize: '10px', fontWeight: 700, colors: '#64748b' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => '₱' + Number(val).toLocaleString(),
                            style: { fontSize: '10px', fontWeight: 600, colors: '#64748b' }
                        }
                    },
                    legend: { position: 'top', fontSize: '11px', fontWeight: 700 },
                    tooltip: {
                        y: { formatter: (val) => '₱' + Number(val).toLocaleString() }
                    }
                };
                this.charts['aging'] = new ApexCharts(agingEl, agingOptions);
                this.charts['aging'].render();
            }

            // Chart 5: Payment Methods
            const payEl = document.querySelector("#chart-payment-methods");
            if (payEl) {
                this.destroyChart('paymentMethods');
                const payOptions = {
                    series: @json($paymentMethodSeries),
                    labels: @json($paymentMethodLabels),
                    chart: {
                        type: 'pie',
                        height: 260,
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#10b981', '#f59e0b'],
                    legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 },
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val.toFixed(1) + '%'
                    },
                    tooltip: {
                        y: { formatter: (val) => '₱' + Number(val).toLocaleString() }
                    }
                };
                this.charts['paymentMethods'] = new ApexCharts(payEl, payOptions);
                this.charts['paymentMethods'].render();
            }

            // Chart 6: Inventory Stock Health
            const invEl = document.querySelector("#chart-inventory-health");
            if (invEl) {
                this.destroyChart('invHealth');
                const invOptions = {
                    series: [{
                        name: 'Product SKUs',
                        data: @json($inventoryHealthSeries)
                    }],
                    chart: {
                        type: 'bar',
                        height: 260,
                        toolbar: { show: false },
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    colors: ['#10b981', '#f59e0b', '#ef4444'],
                    plotOptions: {
                        bar: {
                            distributed: true,
                            borderRadius: 6,
                            columnWidth: '45%'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (val) => val + ' SKUs',
                        style: { fontSize: '11px', fontWeight: 700 }
                    },
                    xaxis: {
                        categories: ['Healthy (≥5)', 'Low Stock (1-4)', 'Out of Stock (0)'],
                        labels: { style: { fontSize: '10px', fontWeight: 700 } }
                    },
                    legend: { show: false }
                };
                this.charts['invHealth'] = new ApexCharts(invEl, invOptions);
                this.charts['invHealth'].render();
            }
        }
    };
}
</script>
@endpush
