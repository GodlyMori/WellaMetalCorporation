<div class="space-y-7">

    <!-- TOP HEADER: REPORT NAVIGATION TABS + EXPORT ACTIONS -->
    <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-5 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        
        <!-- Report Type Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 xl:pb-0 scrollbar-none">
            <!-- 1. Sales Report Tab -->
            <button
                type="button"
                wire:click="setTab('sales')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'sales' ? 'bg-[#142259] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2"/></svg>
                <span>SALES & REVENUE</span>
            </button>

            <!-- 2. Inventory Report Tab -->
            <button
                type="button"
                wire:click="setTab('inventory')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'inventory' ? 'bg-[#142259] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>INVENTORY & VALUATION</span>
            </button>

            <!-- 3. Transfers Report Tab -->
            <button
                type="button"
                wire:click="setTab('transfers')"
                class="px-5 py-3 rounded-2xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-2 {{ $activeTab === 'transfers' ? 'bg-[#142259] text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 font-bold' }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>BRANCH TRANSFERS</span>
            </button>
        </div>

        <!-- Export Buttons (PDF, CSV, Print) -->
        <div class="flex items-center gap-2.5">
            @if($activeTab === 'sales')
                <!-- Export Sales CSV -->
                <a href="{{ route('reports.export.sales-csv', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $salesStatus]) }}"
                   class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>EXPORT CSV</span>
                </a>

                <!-- Export Sales PDF -->
                <a href="{{ route('reports.export.sales-pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $salesStatus]) }}"
                   target="_blank"
                   class="bg-[#dc2626] hover:bg-[#b91c1c] text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>EXPORT PDF</span>
                </a>
            @elseif($activeTab === 'inventory')
                <!-- Export Inventory CSV -->
                <a href="{{ route('reports.export.inventory-csv', ['category' => $inventoryCategory]) }}"
                   class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>EXPORT CSV</span>
                </a>

                <!-- Export Inventory PDF -->
                <a href="{{ route('reports.export.inventory-pdf', ['category' => $inventoryCategory]) }}"
                   target="_blank"
                   class="bg-[#dc2626] hover:bg-[#b91c1c] text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>EXPORT PDF</span>
                </a>
            @else
                <button type="button" onclick="window.print()"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>PRINT REPORT</span>
                </button>
            @endif
        </div>

    </div>

    <!-- ========================================== -->
    <!-- TAB 1: SALES & REVENUE AUDIT REPORT        -->
    <!-- ========================================== -->
    @if($activeTab === 'sales')
        <div class="space-y-6">

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                <!-- Total Sales Volume -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#dc2626]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">TOTAL REVENUE (PHP)</p>
                    <h3 class="text-[#dc2626] text-3xl font-black mt-2">₱{{ number_format($totalSalesRevenue, 2) }}</h3>
                    <p class="text-slate-400 text-xs mt-1">From completed orders</p>
                </div>

                <!-- Completed Transactions -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#10b981]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">COMPLETED ORDERS</p>
                    <h3 class="text-[#10b981] text-3xl font-black mt-2">{{ $completedTransactionsCount }} / {{ $totalTransactionsCount }}</h3>
                    <p class="text-slate-400 text-xs mt-1">Successful customer settlements</p>
                </div>

                <!-- Average Order Value -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#2563eb]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">AVERAGE TICKET</p>
                    <h3 class="text-[#2563eb] text-3xl font-black mt-2">₱{{ number_format($averageOrderValue, 2) }}</h3>
                    <p class="text-slate-400 text-xs mt-1">Average per completed sale</p>
                </div>

                <!-- Fulfillment Rate -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#142366]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">FULFILLMENT RATE</p>
                    <h3 class="text-[#142259] text-3xl font-black mt-2">
                        {{ $totalTransactionsCount > 0 ? round(($completedTransactionsCount / $totalTransactionsCount) * 100) : 0 }}%
                    </h3>
                    <p class="text-slate-400 text-xs mt-1">Conversion completion ratio</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-semibold">
                <!-- Date Presets -->
                <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
                    <button type="button" wire:click="$set('datePreset', 'this_month')" class="px-3.5 py-2 rounded-xl {{ $datePreset === 'this_month' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">This Month</button>
                    <button type="button" wire:click="$set('datePreset', 'this_week')" class="px-3.5 py-2 rounded-xl {{ $datePreset === 'this_week' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">This Week</button>
                    <button type="button" wire:click="$set('datePreset', 'today')" class="px-3.5 py-2 rounded-xl {{ $datePreset === 'today' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Today</button>
                    <button type="button" wire:click="$set('datePreset', 'all')" class="px-3.5 py-2 rounded-xl {{ $datePreset === 'all' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">All Time</button>
                </div>

                <!-- Custom Range + Status Select -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <input type="date" wire:model.live="startDate" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700">
                    <span class="text-slate-400">to</span>
                    <input type="date" wire:model.live="endDate" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700">
                    <select wire:model.live="salesStatus" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700">
                        <option value="ALL">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="layaway">Lay-Away / Reserve</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Detailed Sales Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                                <th class="py-4 px-6">ORDER #</th>
                                <th class="py-4 px-6">CUSTOMER</th>
                                <th class="py-4 px-6">PRODUCT</th>
                                <th class="py-4 px-6">AMOUNT</th>
                                <th class="py-4 px-6">DATE</th>
                                <th class="py-4 px-6 text-right">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px]">
                            @forelse($sales as $sale)
                                <tr class="hover:bg-blue-50/20">
                                    <td class="py-4 px-6 font-bold text-blue-600">{{ $sale->sale_number }}</td>
                                    <td class="py-4 px-6 font-bold text-slate-800">{{ $sale->customer_name }}</td>
                                    <td class="py-4 px-6 text-slate-600">{{ $sale->product_name }}</td>
                                    <td class="py-4 px-6 font-black text-[#142259]">₱{{ number_format($sale->amount, 2) }}</td>
                                    <td class="py-4 px-6 text-slate-400 text-xs">{{ $sale->sale_date->format('Y-m-d') }}</td>
                                    <td class="py-4 px-6 text-right">
                                        @if($sale->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 bg-[#ecfdf5] text-[#059669] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                                Completed
                                            </span>
                                        @elseif($sale->status === 'layaway')
                                            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/80 text-xs font-extrabold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                                Lay-Away
                                            </span>
                                        @elseif($sale->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 bg-[#fffbeb] text-[#d97706] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b]"></span>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-[#fef2f2] text-[#dc2626] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444]"></span>
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400">No sales transactions found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sales->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: INVENTORY & VALUATION REPORT        -->
    <!-- ========================================== -->
    @if($activeTab === 'inventory')
        <div class="space-y-6">

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                <!-- Total Valuation -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#2563eb]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">INVENTORY VALUATION</p>
                    <h3 class="text-[#2563eb] text-3xl font-black mt-2">₱{{ number_format($totalInventoryValuation, 2) }}</h3>
                    <p class="text-slate-400 text-xs mt-1">Total physical inventory worth</p>
                </div>

                <!-- Total Stock In Hand -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#142366]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">PHYSICAL STOCK UNITS</p>
                    <h3 class="text-[#142259] text-3xl font-black mt-2">{{ $totalStockUnits }} units</h3>
                    <p class="text-slate-400 text-xs mt-1">On-hand across all categories</p>
                </div>

                <!-- Low Stock Items -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#ea580c]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">LOW STOCK ALERTS</p>
                    <h3 class="text-[#ea580c] text-3xl font-black mt-2">{{ $totalLowStockItems }} items</h3>
                    <p class="text-slate-400 text-xs mt-1">Items with &le; 4 units remaining</p>
                </div>

                <!-- Active Products -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#10b981]">
                    <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase">ACTIVE PRODUCT SKUS</p>
                    <h3 class="text-[#10b981] text-3xl font-black mt-2">{{ $products->total() }}</h3>
                    <p class="text-slate-400 text-xs mt-1">Unique catalog references</p>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-semibold">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <span class="text-slate-500 uppercase font-bold">Category:</span>
                    <select wire:model.live="inventoryCategory" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700">
                        <option value="ALL">All Categories</option>
                        <option value="Sofa">Sofa</option>
                        <option value="Dining Table">Dining Table</option>
                        <option value="Closet">Closet</option>
                    </select>

                    <span class="text-slate-500 uppercase font-bold ml-2">Stock Level:</span>
                    <select wire:model.live="stockFilter" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700">
                        <option value="all">All Levels</option>
                        <option value="in_stock">Healthy (&gt; 4)</option>
                        <option value="low_stock">Low Stock (&le; 4)</option>
                        <option value="out_of_stock">Out of Stock (0)</option>
                    </select>
                </div>

                <div class="w-full md:w-64">
                    <input type="text" wire:model.live.debounce.300ms="inventorySearch" placeholder="Search item or material..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-700">
                </div>
            </div>

            <!-- Inventory Valuation Table -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                                <th class="py-4 px-6">ID</th>
                                <th class="py-4 px-6">PRODUCT</th>
                                <th class="py-4 px-6">CATEGORY</th>
                                <th class="py-4 px-6">PRICE</th>
                                <th class="py-4 px-6">UNITS</th>
                                <th class="py-4 px-6 font-black">ASSET VALUE</th>
                                <th class="py-4 px-6 text-right">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[13px]">
                            @forelse($products as $p)
                                <tr class="hover:bg-blue-50/20">
                                    <td class="py-4 px-6 font-black text-blue-600">{{ $p->formatted_id }}</td>
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-slate-800">{{ $p->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $p->description }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $p->category === 'Sofa' ? 'bg-blue-50 text-blue-600 border border-blue-100' : ($p->category === 'Dining Table' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100') }}">
                                            {{ $p->category }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-700">₱{{ number_format($p->tagged_price, 2) }}</td>
                                    <td class="py-4 px-6 font-black text-slate-900">{{ $p->quantity_in_stock }}</td>
                                    <td class="py-4 px-6 font-black text-[#142259]">₱{{ number_format($p->tagged_price * $p->quantity_in_stock, 2) }}</td>
                                    <td class="py-4 px-6 text-right">
                                        @if($p->quantity_in_stock <= 0)
                                            <span class="inline-flex items-center gap-1.5 bg-[#fef2f2] text-[#dc2626] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444]"></span>
                                                Out of Stock
                                            </span>
                                        @elseif($p->quantity_in_stock <= 4)
                                            <span class="inline-flex items-center gap-1.5 bg-[#fffbeb] text-[#d97706] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b]"></span>
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 bg-[#ecfdf5] text-[#059669] text-xs font-bold px-3 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                                In Stock
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400">No products match this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: BRANCH TRANSFERS & RESTOCK LOG      -->
    <!-- ========================================== -->
    @if($activeTab === 'transfers')
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full min-w-0">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-[#142259] font-black text-base uppercase tracking-wider">Inbound Branch Delivery History</h3>
                <p class="text-slate-400 text-xs mt-1">Audit log of all physical stock movements received at this branch</p>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                            <th class="py-4 px-6">DELIVERY DATE</th>
                            <th class="py-4 px-6">SOURCE BRANCH</th>
                            <th class="py-4 px-6">ITEMS & QUANTITIES</th>
                            <th class="py-4 px-6">RECEIVED BY</th>
                            <th class="py-4 px-6">NOTES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[13px]">
                        @forelse($transfers as $tf)
                            <tr class="hover:bg-blue-50/20">
                                <td class="py-4 px-6 font-bold text-slate-800">{{ $tf->date_received->format('Y-m-d') }}</td>
                                <td class="py-4 px-6 font-extrabold text-[#142259]">{{ $tf->source_branch }}</td>
                                <td class="py-4 px-6">
                                    @foreach($tf->items as $it)
                                        <div class="text-xs text-slate-700 py-0.5">
                                            <span class="font-bold">{{ $it->product?->name ?? 'Product' }}:</span>
                                            <span class="text-emerald-600 font-extrabold">+{{ $it->quantity_received }} units</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-medium">{{ $tf->receiver?->name ?? 'Admin' }}</td>
                                <td class="py-4 px-6 text-slate-400 text-xs">{{ $tf->notes ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400">No branch transfers on record.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
