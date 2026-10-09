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

            @if($activeTab === 'sales')
                <button type="button" wire:click="processExpiredLayaways" title="Scan & restore cancelled/expired layaway reserves"
                   class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-3 py-1.5 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Check expired reserves</span>
                </button>

                <!-- Export Sales PDF -->
                <a href="{{ route('reports.export.sales-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $salesStatus]) }}"
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
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total (Paid)</th>
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Order Date</th>
                                <th class="py-2.5 px-4 w-28 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                                <th class="py-2.5 px-4 w-32 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
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
                                    <td class="py-2.5 px-4 text-right font-mono tabular-nums">
                                        <div class="font-bold text-slate-900 dark:text-white">₱{{ number_format($sale->amount, 2) }}</div>
                                        @if($sale->status === 'layaway')
                                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400">Paid: ₱{{ number_format($sale->amount_paid, 2) }}</div>
                                            <div class="text-[10px] text-rose-500">Bal: ₱{{ number_format($sale->remaining_balance, 2) }}</div>
                                        @elseif($sale->discount_amount > 0)
                                            <div class="text-[11px] text-slate-400">Disc: -₱{{ number_format($sale->discount_amount, 2) }}</div>
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
                                    <td class="py-2.5 px-4 text-center">
                                        @if($sale->is_archived)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                Archived
                                            </span>
                                        @elseif($sale->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                                Completed
                                            </span>
                                        @elseif($sale->status === 'layaway')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                                Lay-away
                                            </span>
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

</div>
