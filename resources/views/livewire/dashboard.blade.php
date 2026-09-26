<div class="space-y-6">
    <!-- OPERATIONAL TELEMETRY RAIL: Active Shortage Alert (Rendered conditionally) -->
    @if($stockAlerts->isNotEmpty())
        <div class="bg-amber-500/[0.08] border border-amber-500/20 rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-slate-900">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-700 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18h.01"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-mono font-bold tracking-wider uppercase text-amber-800">Critical Stock Notice</span>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-900 font-bold">
                            {{ $stockAlerts->count() }} {{ Str::plural('SKU', $stockAlerts->count()) }} Low
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Items requiring immediate reorder: 
                        <span class="font-medium text-slate-800">
                            {{ $stockAlerts->take(2)->pluck('name')->join(', ') }}
                            @if($stockAlerts->count() > 2)
                                and {{ $stockAlerts->count() - 2 }} others
                            @endif
                        </span>
                    </p>
                </div>
            </div>
            <a href="{{ route('inventory') }}" 
               class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-500/30 text-amber-800 hover:bg-amber-50 text-xs font-semibold shadow-xs transition-colors whitespace-nowrap self-start sm:self-auto">
                <span>Inspect Ledger</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    @endif

    <!-- 4-PANEL TELEMETRY BILLET (Unified Milled Industrial Surfaces) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        
        <!-- 1. Total Products (Clickable -> Navigates to Inventory) -->
        <a href="{{ route('inventory') }}"
           class="group bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)] ring-1 ring-slate-900/[0.02] flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all duration-150 cursor-pointer select-none">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 12v9m-4-7l4 2.5 4-2.5"/>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-slate-500 group-hover:text-slate-900 transition-colors">
                    <span>LEDGER</span>
                    <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-900 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black tracking-tight text-slate-900 tabular-nums">
                        {{ number_format($totalProducts) }}
                    </span>
                    <span class="text-xs font-mono text-slate-400 uppercase">SKUs</span>
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-mono font-medium tracking-wider text-slate-500 uppercase">Total Inventory</span>
                    <span class="text-xs font-medium text-slate-600">{{ $categoryCount }} active classes</span>
                </div>
            </div>
        </a>

        <!-- 2. Stock Units (Clickable -> Opens Breakdown Modal) -->
        <div wire:click="openStockModal"
             class="group bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)] ring-1 ring-slate-900/[0.02] flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all duration-150 cursor-pointer select-none">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        <circle cx="12" cy="16" r="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-slate-500 group-hover:text-slate-900 transition-colors">
                    <span>BREAKDOWN</span>
                    <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-900 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black tracking-tight text-slate-900 tabular-nums">
                        {{ number_format($stockUnits) }}
                    </span>
                    <span class="text-xs font-mono text-slate-400 uppercase">Units</span>
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-mono font-medium tracking-wider text-slate-500 uppercase">Physical Volume</span>
                    <span class="text-xs font-medium text-slate-600">On-hand yard count</span>
                </div>
            </div>
        </div>

        <!-- 3. Monthly Revenue (Tactile Privacy Vault) -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)] ring-1 ring-slate-900/[0.02] flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all duration-150 select-none">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9l3-3m0 0h-2.5m2.5 0v2.5"/>
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            wire:click="toggleRevenue"
                            class="p-1.5 rounded-md hover:bg-slate-100 text-slate-500 hover:text-slate-900 border border-transparent hover:border-slate-200 transition"
                            title="{{ $revealRevenue ? 'Obscure fiscal figures' : 'Reveal fiscal figures' }}"
                            aria-label="Toggle revenue visibility">
                        @if($revealRevenue)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        @endif
                    </button>
                    <a href="{{ route('sales') }}" 
                       class="text-[11px] font-mono font-medium text-slate-500 hover:text-slate-900 flex items-center gap-1 transition-colors">
                        <span>SALES</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <div class="flex items-baseline">
                    @if($revealRevenue)
                        <span class="text-lg font-semibold text-slate-400 mr-1">₱</span>
                        <span class="text-3xl font-black tracking-tight text-slate-900 tabular-nums">
                            {{ number_format($monthlyRevenue, 0) }}
                        </span>
                    @else
                        <span class="text-3xl font-mono font-bold tracking-widest text-slate-400 select-all">••••••••</span>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-mono font-medium tracking-wider text-slate-500 uppercase">Gross Revenue</span>
                    <span class="text-[11px] font-mono text-slate-400">{{ $revealRevenue ? 'Audited' : 'Encrypted' }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Avg Order Value -->
        <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)] ring-1 ring-slate-900/[0.02] flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all duration-150 select-none">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <a href="{{ route('sales') }}" 
                   class="text-[11px] font-mono font-medium text-slate-500 hover:text-slate-900 flex items-center gap-1 transition-colors">
                    <span>ORDERS</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div>
                <div class="flex items-baseline">
                    @if($revealRevenue)
                        <span class="text-lg font-semibold text-slate-400 mr-1">₱</span>
                        <span class="text-3xl font-black tracking-tight text-slate-900 tabular-nums">
                            {{ number_format($avgOrder, 0) }}
                        </span>
                    @else
                        <span class="text-3xl font-mono font-bold tracking-widest text-slate-400 select-all">••••••••</span>
                    @endif
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-mono font-medium tracking-wider text-slate-500 uppercase">Avg Order Ticket</span>
                    <span class="text-xs font-medium text-slate-600">Per Completed PO</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TWO COLUMN WORKSTATION GRID (Col 8: Sales Ledger | Col 4: Performance Analytics) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: RECENT SALES DISPATCH LEDGER -->
        <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between overflow-hidden">
            <div>
                <!-- Ledger Header -->
                <div class="px-6 py-4.5 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                        <h2 class="text-xs font-mono font-bold tracking-wider text-slate-900 uppercase">
                            Recent Sales Dispatch
                        </h2>
                    </div>
                    <span class="text-[11px] font-mono font-medium text-slate-500 bg-white border border-slate-200/80 px-2.5 py-1 rounded-md">
                        Last 6 Transactions
                    </span>
                </div>

                <!-- Ledger Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-mono font-semibold tracking-wider text-slate-500 uppercase bg-slate-50/40">
                                <th class="py-3 px-6">Customer / Account</th>
                                <th class="py-3 px-4">Item Spec</th>
                                <th class="py-3 px-4 text-right">Amount</th>
                                <th class="py-3 px-4">Dispatched</th>
                                <th class="py-3 px-6 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-slate-50/70 transition-colors duration-100 group">
                                    <!-- Customer Profile -->
                                    <td class="py-3.5 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-md bg-slate-900 text-white font-mono text-[11px] font-bold flex items-center justify-center flex-shrink-0 ring-1 ring-slate-900/10">
                                                {{ $sale->computed_initials }}
                                            </div>
                                            <span class="font-semibold text-slate-800 group-hover:text-slate-900">
                                                {{ $sale->customer_name }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Product Name -->
                                    <td class="py-3.5 px-4 text-slate-600 font-medium max-w-[200px] truncate" title="{{ $sale->product_name }}">
                                        {{ $sale->product_name }}
                                    </td>

                                    <!-- Amount with Tabular Lining -->
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 tabular-nums">
                                        ₱{{ number_format($sale->amount, 0) }}
                                    </td>

                                    <!-- Date in Monospaced ISO Format -->
                                    <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]">
                                        {{ $sale->sale_date->format('Y-m-d') }}
                                    </td>

                                    <!-- Calibrated Status Badges -->
                                    <td class="py-3.5 px-6 text-right">
                                        @if($sale->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Settled
                                            </span>
                                        @elseif($sale->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium bg-amber-50 text-amber-700 border border-amber-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-medium bg-rose-50 text-rose-700 border border-rose-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                Voided
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 font-mono text-xs">
                                        No transaction telemetry recorded in current ledger period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Footer Rail -->
            <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/40 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Viewing real-time settlement log</span>
                <a href="{{ route('sales') }}" class="font-mono text-xs font-semibold text-slate-700 hover:text-slate-900 inline-flex items-center gap-1">
                    <span>Full Ledger Archive</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- RIGHT COLUMN: ANALYTICAL & INVENTORY TELEMETRY -->
        <div class="lg:col-span-4 space-y-4">

            <!-- 1. Weekly Volume - Billet Bar Chart -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-mono font-bold tracking-wider text-slate-900 uppercase">
                        Weekly Revenue Run
                    </h3>
                    <span class="text-[10px] font-mono font-medium text-slate-400">SEPTEMBER</span>
                </div>

                <div class="h-28 flex items-end justify-between gap-3 pt-3 px-1 border-b border-slate-100 pb-2">
                    <!-- Week 1-7 -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 group">
                        <div class="w-full bg-slate-200 group-hover:bg-slate-300 rounded-xs h-5 transition-colors"
                             title="W1 (1-7): ₱1,200"></div>
                        <span class="text-[10px] font-mono text-slate-500 font-medium">1-7</span>
                    </div>

                    <!-- Week 8-14 -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 group">
                        <div class="w-full bg-slate-400 group-hover:bg-slate-500 rounded-xs h-16 transition-colors"
                             title="W2 (8-14): ₱4,500"></div>
                        <span class="text-[10px] font-mono text-slate-500 font-medium">8-14</span>
                    </div>

                    <!-- Week 15-21 (Peak) -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 group">
                        <div class="w-full bg-slate-900 group-hover:bg-slate-800 rounded-xs h-24 transition-colors relative"
                             title="W3 (15-21): ₱6,800 [Peak]">
                            <span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[9px] font-mono font-bold text-slate-900 hidden group-hover:block whitespace-nowrap">
                                PEAK
                            </span>
                        </div>
                        <span class="text-[10px] font-mono text-slate-900 font-bold">15-21</span>
                    </div>

                    <!-- Week 22-25 -->
                    <div class="flex-1 flex flex-col items-center gap-1.5 group">
                        <div class="w-full bg-slate-300 group-hover:bg-slate-400 rounded-xs h-10 transition-colors"
                             title="W4 (22-25): ₱2,598"></div>
                        <span class="text-[10px] font-mono text-slate-500 font-medium">22-25</span>
                    </div>
                </div>
                <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 pt-2">
                    <span>Base: ₱1.2k</span>
                    <span class="font-semibold text-slate-700">Peak: ₱6.8k</span>
                </div>
            </div>

            <!-- 2. High-Velocity Product Leaders -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <div class="flex items-center justify-between mb-3.5">
                    <h3 class="text-xs font-mono font-bold tracking-wider text-slate-900 uppercase">
                        Product Velocity
                    </h3>
                    <span class="text-[10px] font-mono text-slate-400">BY REVENUE</span>
                </div>

                <div class="space-y-3">
                    @foreach($topProducts as $tp)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-slate-700 truncate max-w-[160px]">
                                    <span class="font-mono text-[10px] text-slate-400 mr-1">{{ $tp['rank'] }}</span>
                                    {{ $tp['name'] }}
                                </span>
                                <span class="font-mono font-bold text-slate-900 tabular-nums">
                                    ₱{{ number_format($tp['amount']) }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-slate-800 transition-all duration-300"
                                     style="width: {{ $tp['pct'] }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Stock Capacity by Category -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <div class="flex items-center justify-between mb-3.5">
                    <h3 class="text-xs font-mono font-bold tracking-wider text-slate-900 uppercase">
                        Yard Inventory Classes
                    </h3>
                    <span class="text-[10px] font-mono text-slate-400">UNITS</span>
                </div>

                <div class="space-y-3">
                    @foreach($categories as $cat)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="text-slate-600 font-medium w-24 truncate">{{ $cat['name'] }}</span>
                            <div class="flex-1 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-slate-700"
                                     style="width: {{ min(100, ($cat['count'] / 30) * 100) }}%;"></div>
                            </div>
                            <span class="font-mono font-bold text-slate-900 w-8 text-right tabular-nums">{{ $cat['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. Stock Reorder Warnings -->
            <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $stockAlerts->count() > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                        <h3 class="text-xs font-mono font-bold tracking-wider text-slate-900 uppercase">
                            Reorder Thresholds
                        </h3>
                    </div>
                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded {{ $stockAlerts->count() > 0 ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">
                        {{ $stockAlerts->count() }}
                    </span>
                </div>

                <div class="space-y-2">
                    @forelse($stockAlerts->take(3) as $alert)
                        <div class="p-2.5 rounded-lg border border-slate-200/70 bg-slate-50/60 flex items-center justify-between">
                            <div class="truncate mr-2">
                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $alert->name }}</p>
                                <p class="text-[10px] font-mono text-slate-500 uppercase">{{ $alert->category }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded font-mono text-xs font-bold text-amber-800 bg-amber-100 border border-amber-200/60 whitespace-nowrap">
                                {{ $alert->quantity_in_stock }} rem
                            </span>
                        </div>
                    @empty
                        <p class="text-xs font-mono text-slate-400 py-2">All warehouse lots above minimum safety stock.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- STOCK BREAKDOWN MODAL: Architectural Overlay -->
    @if($showStockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-800 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                <circle cx="12" cy="16" r="1.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-slate-900 font-bold text-base leading-tight">Physical Stock Breakdown</h3>
                            <p class="text-slate-500 font-mono text-xs mt-0.5">{{ number_format($stockUnits) }} aggregate units across active inventory</p>
                        </div>
                    </div>
                    <button wire:click="closeStockModal" 
                            class="text-slate-400 hover:text-slate-700 text-sm p-1.5 rounded-md hover:bg-slate-100 transition cursor-pointer"
                            aria-label="Close modal">
                        ✕
                    </button>
                </div>

                <div class="space-y-2.5 my-5">
                    @foreach($categories as $cat)
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-sm bg-slate-800"></span>
                                <span class="font-semibold text-slate-800 text-xs">{{ $cat['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 font-mono">
                                <span class="font-bold text-slate-900 text-sm tabular-nums">{{ number_format($cat['count']) }}</span>
                                <span class="text-[10px] text-slate-500 uppercase">units</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-100 text-xs">
                    <span class="text-slate-500 font-medium">Reconcile individual serials?</span>
                    <a href="{{ route('inventory') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-1.5 shadow-xs">
                        <span>Open Inventory Yard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
