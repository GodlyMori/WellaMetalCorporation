<div class="space-y-6">

    <!-- Toast Notification Banner -->
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 text-xs font-black">✓</div>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 font-bold ml-4">✕</button>
        </div>
    @endif

    <!-- TOP CONTROLS BAR: STATUS PILLS + SEARCH & NEW SALE -->
    <div class="flex flex-col 2xl:flex-row items-stretch 2xl:items-center justify-between gap-4">
        
        <!-- Status Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-none flex-nowrap">
            <button type="button"
                    wire:click="setStatusFilter('ALL')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $statusFilter === 'ALL' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                ALL SALES ({{ $counts['all'] }})
            </button>

            <button type="button"
                    wire:click="setStatusFilter('completed')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $statusFilter === 'completed' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                COMPLETED ({{ $counts['completed'] }})
            </button>

            <!-- LAY-AWAY / RESERVE TAB -->
            <button type="button"
                    wire:click="setStatusFilter('layaway')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-1.5 {{ $statusFilter === 'layaway' ? 'bg-[#4f46e5] text-white shadow-sm' : 'bg-white text-indigo-700 hover:bg-indigo-50/50 border border-indigo-200 shadow-sm' }}">
                <span>LAY-AWAY ({{ $counts['layaway'] }})</span>
            </button>

            <button type="button"
                    wire:click="setStatusFilter('pending')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $statusFilter === 'pending' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                PENDING ({{ $counts['pending'] }})
            </button>

            <button type="button"
                    wire:click="setStatusFilter('cancelled')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $statusFilter === 'cancelled' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                CANCELLED ({{ $counts['cancelled'] }})
            </button>

            <!-- ARCHIVED -->
            <button type="button"
                    wire:click="setStatusFilter('ARCHIVED')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-1.5 {{ $statusFilter === 'ARCHIVED' ? 'bg-[#64748b] text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span>ARCHIVED ({{ $counts['archived'] }})</span>
            </button>
        </div>

        <!-- Right: Search Bar & New Sale Button -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="relative w-full sm:w-72">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search order, customer, phone..."
                    class="w-full bg-white border border-slate-200/90 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-xs font-semibold placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm transition"
                />
            </div>

            <!-- New Sale Button -->
            <button
                type="button"
                wire:click="openNewSaleModal"
                class="bg-[#dc2626] hover:bg-[#b91c1c] active:bg-[#991b1b] text-white text-xs font-extrabold tracking-wider px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer whitespace-nowrap flex-shrink-0"
            >
                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <span>NEW SALE / LAY-AWAY</span>
            </button>
        </div>

    </div>

    <!-- SALES TABLE (Spacious & relaxed 1:1 like recent sales) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                        <th class="py-4.5 px-6 font-black w-28 whitespace-nowrap">ORDER #</th>
                        <th class="py-4.5 px-6 font-black min-w-[200px]">CUSTOMER</th>
                        <th class="py-4.5 px-6 font-black min-w-[180px]">ITEMS PURCHASED</th>
                        <th class="py-4.5 px-6 font-black min-w-[140px] whitespace-nowrap">AMOUNT & TERMS</th>
                        <th class="py-4.5 px-6 font-black min-w-[130px] whitespace-nowrap">DATE & EXPIRY</th>
                        <th class="py-4.5 px-6 font-black w-28 whitespace-nowrap">STATUS</th>
                        <th class="py-4.5 px-6 font-black text-right min-w-[160px] whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-blue-50/20 transition">
                            <!-- Order Number -->
                            <td class="py-5 px-6 font-black whitespace-nowrap">
                                <span class="bg-[#eef4ff] text-[#2563eb] text-xs font-black px-3 py-1.5 rounded-lg border border-blue-100/60 shadow-xs">
                                    {{ $sale->sale_number ?? 'ORD-' . $sale->id }}
                                </span>
                            </td>

                            <!-- Customer with Avatar & Complete Contact Info -->
                            <td class="py-5 px-6">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-9 h-9 rounded-full text-white font-extrabold text-xs flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5"
                                         style="background-color: {{ $sale->avatar_color ?? '#2563eb' }}">
                                        {{ $sale->computed_initials }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-[15px] leading-snug">
                                            {{ $sale->customer_name }}
                                        </p>
                                        @if($sale->customer_phone)
                                            <p class="text-slate-500 text-xs font-semibold flex items-center gap-1.5 mt-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                <span>{{ $sale->customer_phone }}</span>
                                            </p>
                                        @endif
                                        @if($sale->customer_address)
                                            <p class="text-slate-400 text-[11px] leading-tight mt-1 flex items-start gap-1">
                                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="line-clamp-1">{{ $sale->customer_address }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Items Purchased -->
                            <td class="py-5 px-6">
                                @if($sale->items && $sale->items->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($sale->items as $it)
                                            <div class="text-[13px] text-slate-700 flex items-center justify-between gap-2">
                                                <span class="font-bold text-slate-800">{{ $it->product?->name ?? $sale->product_name }}</span>
                                                <span class="text-slate-400 text-xs whitespace-nowrap">×{{ $it->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="font-bold text-slate-700 text-[14px] leading-snug">
                                        {{ $sale->product_name }}
                                    </p>
                                @endif
                            </td>

                            <!-- Amount & Terms -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                <p class="font-black text-slate-900 text-[15px]">
                                    ₱{{ number_format($sale->amount, 2) }}
                                </p>
                                @if($sale->discount_amount > 0)
                                    <p class="text-[11px] text-slate-400 font-semibold">
                                        <span class="line-through">₱{{ number_format($sale->original_amount ?? ($sale->amount + $sale->discount_amount), 2) }}</span>
                                        <span class="text-emerald-600 font-bold ml-1">-₱{{ number_format($sale->discount_amount, 2) }}</span>
                                    </p>
                                @endif
                                @if($sale->promo_name)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span>🏷️ {{ $sale->promo_name }}</span>
                                    </span>
                                @endif
                                @if($sale->status === 'layaway' || $sale->payment_type === 'layaway')
                                    <div class="mt-1 text-xs space-y-0.5">
                                        <p class="text-emerald-700 font-bold">Paid: ₱{{ number_format($sale->amount_paid, 2) }}</p>
                                        @if($sale->remaining_balance > 0)
                                            <p class="text-red-600 font-extrabold">Bal: ₱{{ number_format($sale->remaining_balance, 2) }}</p>
                                        @else
                                            <p class="text-emerald-600 font-extrabold">Fully Paid</p>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 uppercase block mt-0.5">Full Payment</span>
                                @endif
                            </td>

                            <!-- Date & Expiry (with 3-Month Lay-Away countdown) -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                <p class="text-slate-700 text-xs font-bold">
                                    {{ $sale->sale_date->format('M d, Y') }}
                                </p>
                                @if($sale->status === 'layaway' && $sale->layaway_expires_at)
                                    @php
                                        $diffDays = (int)now()->startOfDay()->diffInDays($sale->layaway_expires_at, false);
                                    @endphp
                                    <div class="mt-1">
                                        @if($diffDays > 0)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-extrabold {{ $diffDays <= 15 ? 'text-red-600' : 'text-amber-700' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>{{ $diffDays }} days left</span>
                                            </span>
                                            <p class="text-[10px] text-slate-400">Due: {{ $sale->layaway_expires_at->format('M d, Y') }}</p>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-red-600">
                                                <span>EXPIRED</span>
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Status Pill -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                @if($sale->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 bg-[#ecfdf5] text-[#059669] text-xs font-bold px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                        Completed
                                    </span>
                                @elseif($sale->status === 'layaway')
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200/80 text-xs font-extrabold px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                        Lay-Away
                                    </span>
                                @elseif($sale->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 bg-[#fffbeb] text-[#d97706] text-xs font-bold px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#f59e0b]"></span>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#fef2f2] text-[#dc2626] text-xs font-bold px-3 py-1.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444]"></span>
                                        Cancelled
                                    </span>
                                @endif
                            </td>

                            <!-- Action (Archive, Restore, Settle Lay-Away) -->
                            <td class="py-5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Record Lay-Away Payment Button -->
                                    @if($sale->status === 'layaway' && $sale->remaining_balance > 0 && !$sale->is_archived)
                                        <button
                                            type="button"
                                            wire:click="openPaymentModal({{ $sale->id }})"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-xl inline-flex items-center gap-1 transition shadow-xs cursor-pointer"
                                            title="Record Installment / Pay Balance"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span>Pay Balance</span>
                                        </button>
                                    @endif

                                    <!-- Archive / Restore -->
                                    @if(!$sale->is_archived)
                                        <button
                                            type="button"
                                            wire:click="archiveSale({{ $sale->id }})"
                                            class="border border-slate-200 hover:border-amber-400 bg-white hover:bg-amber-50/50 text-slate-500 hover:text-amber-600 text-xs font-bold px-3 py-1.5 rounded-xl inline-flex items-center gap-1 transition cursor-pointer"
                                            title="Archive Sale"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                            <span>Archive</span>
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            wire:click="restoreSale({{ $sale->id }})"
                                            class="border border-emerald-300 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-xl inline-flex items-center gap-1 transition cursor-pointer hover:bg-emerald-100"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Restore</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No sales records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: NEW SALE & MULTI-ITEM / LAY-AWAY -->
    @if($showNewSaleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-[#142259] font-black text-xl">Record New Sale / Lay-Away</h3>
                        <p class="text-slate-400 text-xs mt-0.5">Supports multi-item checkout, customer contact & 3-month lay-away reservation</p>
                    </div>
                    <button wire:click="$set('showNewSaleModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form wire:submit="recordSale" class="mt-6 space-y-5 text-xs font-semibold">
                    
                    <!-- 1. Customer Information Section -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[#142259] font-black text-xs uppercase tracking-wider">Customer Information</span>
                            <span class="text-slate-400 text-[11px]">All fields required</span>
                        </div>

                        <!-- Customer Name (Letters only, NO numbers) -->
                        <div>
                            <label class="block text-slate-700 mb-1 font-bold">
                                Customer Full Name <span class="text-slate-400 text-[11px] font-normal">(Letters only, numbers are not allowed)</span>
                            </label>
                            <input type="text" wire:model="customer_name" placeholder="e.g. Maria Clara Santos" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                            @error('customer_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Contact & Address -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-700 mb-1 font-bold">Contact Phone Number</label>
                                <input type="text" wire:model="customer_phone" placeholder="e.g. +63 917 123 4567" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                                @error('customer_phone') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-slate-700 mb-1 font-bold">Delivery / Complete Address</label>
                                <input type="text" wire:model="customer_address" placeholder="e.g. 123 Davao City, Toril District" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                                @error('customer_address') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 2. Multiple Products / Items Section -->
                    <div class="border border-slate-200/80 rounded-2xl p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[#142259] font-black text-xs uppercase tracking-wider">
                                Order Items ({{ count($items) }})
                            </span>
                            <button type="button" wire:click="addItem" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-extrabold px-3 py-1.5 rounded-xl border border-blue-200 inline-flex items-center gap-1 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Add Another Item</span>
                            </button>
                        </div>

                        @error('items')
                            <p class="text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror

                        <div class="space-y-3">
                            @foreach($items as $index => $item)
                                <div class="bg-slate-50/80 border border-slate-200/70 rounded-2xl p-4 flex flex-col md:flex-row items-stretch md:items-center gap-3">
                                    <!-- Product Select -->
                                    <div class="flex-1">
                                        <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Product SKU / Name</label>
                                        <select wire:model.live="items.{{ $index }}.product_id" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-800 text-xs focus:border-blue-500 focus:outline-none font-bold" required>
                                            @foreach($activeProducts as $prod)
                                                <option value="{{ $prod->id }}">
                                                    {{ $prod->name }} (₱{{ number_format($prod->tagged_price) }}) — {{ $prod->quantity_in_stock }} in stock
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="w-24">
                                        <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Qty</label>
                                        <input type="number" min="1" wire:model.live="items.{{ $index }}.quantity" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-800 text-xs focus:border-blue-500 focus:outline-none font-black text-center" required>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="w-32">
                                        <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Price (₱)</label>
                                        <input type="number" step="0.01" wire:model.live="items.{{ $index }}.unit_price" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-slate-800 text-xs focus:border-blue-500 focus:outline-none" required>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="w-32 text-right">
                                        <label class="block text-slate-500 text-[10px] uppercase font-bold mb-1">Subtotal</label>
                                        <p class="font-black text-slate-900 text-sm py-2">
                                            ₱{{ number_format(($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0), 2) }}
                                        </p>
                                    </div>

                                    <!-- Remove Item Button -->
                                    @if(count($items) > 1)
                                        <div class="flex items-end">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition" title="Remove item">
                                                ✕
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <!-- 2.5 EVENT PROMOTION / SEASONAL DISCOUNT (Option A: Dropdown + Optional Code) -->
                    <div class="bg-purple-50/50 border border-purple-200/80 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-purple-950 font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                                <span>🎉 Event Promo / Seasonal Discount</span>
                                <span class="text-[10px] text-purple-700 bg-purple-100 font-bold px-2 py-0.5 rounded-full">Option A</span>
                            </span>
                            @if($selected_promotion_id)
                                <button type="button" wire:click="clearPromo" class="text-red-500 hover:text-red-700 text-xs font-bold transition cursor-pointer">
                                    ✕ Remove Promo
                                </button>
                            @endif
                        </div>

                        <!-- Dropdown & Code Input -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-600 text-[10px] uppercase font-bold mb-1">Select Active Event</label>
                                <select wire:model.live="selected_promotion_id"
                                        class="w-full bg-white border border-purple-200 rounded-xl px-3 py-2 text-slate-800 text-xs font-bold focus:border-purple-600 focus:outline-none">
                                    <option value="">No Event Promo (Standard Pricing)</option>
                                    @foreach($this->availablePromotions as $promo)
                                        <option value="{{ $promo->id }}">
                                            {{ $promo->name }} ({{ $promo->discount_type === 'percentage' ? (float)$promo->discount_value . '% off' : '₱' . number_format($promo->discount_value, 2) . ' off' }}{{ $promo->applicable_category !== 'ALL' ? ' on ' . $promo->applicable_category : '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Manual Promo Code box -->
                            <div>
                                <label class="block text-slate-600 text-[10px] uppercase font-bold mb-1">Or Enter Promo Code</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" wire:model="promo_code_input" placeholder="e.g. KADAYAWAN26"
                                           class="flex-1 uppercase font-mono bg-white border border-purple-200 rounded-xl px-3 py-2 text-slate-800 text-xs font-bold focus:border-purple-600 focus:outline-none">
                                    <button type="button" wire:click="applyPromoCode"
                                            class="bg-purple-700 hover:bg-purple-800 text-white font-extrabold text-xs px-3.5 py-2 rounded-xl transition cursor-pointer">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if($promoErrorMessage)
                            <p class="text-red-500 text-xs font-bold">{{ $promoErrorMessage }}</p>
                        @endif

                        @if($selected_promotion_id)
                            @php
                                $activePromo = $this->availablePromotions->firstWhere('id', $selected_promotion_id);
                            @endphp
                            @if($activePromo)
                                <div class="p-3 bg-purple-100/70 border border-purple-200 rounded-xl text-xs text-purple-950 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">🏷️</span>
                                        <div>
                                            <span class="font-extrabold">{{ $activePromo->name }}</span>
                                            <span class="text-purple-700 text-[11px] block">
                                                Scope: {{ $activePromo->applicable_category === 'ALL' ? 'All products in order' : $activePromo->applicable_category . ' only' }}
                                                @if($activePromo->min_order_amount > 0)
                                                    • Min. spend: ₱{{ number_format($activePromo->min_order_amount, 2) }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <span class="font-black text-purple-900 text-sm">
                                        -₱{{ number_format($this->discountAmount, 2) }}
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Grand Total Breakdown Display -->
                    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200/80 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-slate-600 font-semibold">
                            <span>Subtotal (Before Discount):</span>
                            <span>₱{{ number_format($this->totalAmount, 2) }}</span>
                        </div>
                        @if($this->discountAmount > 0)
                            <div class="flex items-center justify-between text-xs text-emerald-700 font-bold">
                                <span>Promo Discount:</span>
                                <span>-₱{{ number_format($this->discountAmount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between pt-1.5 border-t border-blue-200/80">
                            <span class="text-xs font-black text-[#142259] uppercase tracking-wider">Total Payable Amount</span>
                            <span class="text-2xl font-black text-[#142259]">
                                ₱{{ number_format($this->netAmount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- 3. Payment Mode & Lay-Away Downpayment Section -->
                    <div class="bg-indigo-50/40 border border-indigo-200/80 rounded-2xl p-5 space-y-4">
                        <span class="text-indigo-950 font-black text-xs uppercase tracking-wider block">Payment Terms & Lay-Away</span>

                        <div class="grid grid-cols-2 gap-4">
                            <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition {{ $payment_type === 'full' ? 'border-blue-600 bg-blue-50/50' : 'border-slate-200 bg-white' }}">
                                <input type="radio" wire:model.live="payment_type" value="full" class="text-blue-600">
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">Full Payment</p>
                                    <p class="text-slate-400 text-[11px]">Completed settlement</p>
                                </div>
                            </label>

                            <label class="p-3.5 rounded-2xl border-2 cursor-pointer flex items-center gap-3 transition {{ $payment_type === 'layaway' ? 'border-indigo-600 bg-indigo-50/70' : 'border-slate-200 bg-white' }}">
                                <input type="radio" wire:model.live="payment_type" value="layaway" class="text-indigo-600">
                                <div>
                                    <p class="font-bold text-indigo-900 text-xs">Lay-Away / Reserve</p>
                                    <p class="text-indigo-500 text-[11px]">3-Month expiry reserve</p>
                                </div>
                            </label>
                        </div>

                        <!-- Lay-Away Downpayment Input & Calculations -->
                        @if($payment_type === 'layaway')
                            <div class="bg-white border border-indigo-200 rounded-2xl p-4 space-y-3">
                                <div>
                                    <label class="block text-slate-700 mb-1 font-bold">Initial Downpayment Amount (₱)</label>
                                    <input type="number" step="0.01" min="1" max="{{ $this->netAmount }}" wire:model.live="downpayment_amount" placeholder="e.g. 5000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 font-bold focus:border-indigo-500 focus:outline-none" required>
                                    @error('downpayment_amount') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-slate-100">
                                    <div class="p-2.5 rounded-xl bg-slate-50">
                                        <p class="text-slate-400 text-[10px] uppercase font-bold">Remaining Balance</p>
                                        <p class="text-red-600 font-black text-sm mt-0.5">₱{{ number_format($this->calculatedBalance, 2) }}</p>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-50">
                                        <p class="text-slate-400 text-[10px] uppercase font-bold">Lay-Away Expiry Date</p>
                                        <p class="text-indigo-700 font-black text-sm mt-0.5">{{ now()->addMonths(3)->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                <div class="p-3 bg-amber-50 rounded-xl text-[11px] text-amber-800 leading-snug">
                                    ℹ️ <strong>Lay-Away Policy:</strong> Product items will be immediately reserved from available stock. The lay-away reservation expires in <strong>3 months</strong> from payment date if not fully settled or extended by another payment.
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Notes Field -->
                    <div>
                        <label class="block text-slate-600 mb-1 font-bold uppercase">Order Notes (Optional)</label>
                        <textarea wire:model="sale_notes" rows="2" placeholder="e.g. Customer requested Saturday delivery" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showNewSaleModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white font-extrabold shadow-sm">
                            {{ $payment_type === 'layaway' ? 'Confirm Lay-Away Order' : 'Complete Sale Transaction' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: RECORD LAY-AWAY INSTALLMENT PAYMENT -->
    @if($showPaymentModal && $payingSale)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-[#142259] font-black text-lg">Record Lay-Away Payment</h3>
                        <p class="text-slate-400 text-xs">{{ $payingSale->sale_number }} — {{ $payingSale->customer_name }}</p>
                    </div>
                    <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 mt-5 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Order Amount:</span>
                        <span class="font-bold text-slate-800">₱{{ number_format($payingSale->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700">
                        <span>Amount Paid So Far:</span>
                        <span class="font-bold">₱{{ number_format($payingSale->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-red-600 font-black text-sm pt-1 border-t border-slate-200/60">
                        <span>Current Remaining Balance:</span>
                        <span>₱{{ number_format($payingSale->remaining_balance, 2) }}</span>
                    </div>
                </div>

                <form wire:submit="submitLayawayPayment" class="mt-5 space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase font-bold">Installment Payment Amount (₱)</label>
                        <input type="number" step="0.01" min="1" max="{{ $payingSale->remaining_balance }}" wire:model="paymentAmount" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-black text-lg focus:border-indigo-500 focus:outline-none" required>
                        @error('paymentAmount') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-3 bg-indigo-50/80 border border-indigo-100 rounded-xl text-[11px] text-indigo-900 leading-snug">
                        💡 <strong>3-Month Rule:</strong> Receiving this payment will extend the lay-away reservation expiration by <strong>3 months</strong> from today (until <strong>{{ now()->addMonths(3)->format('M d, Y') }}</strong>). If paid in full, order status will change to Completed.
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showPaymentModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-sm">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: SECRETARY ARCHIVE AUTHORIZATION REQUIRED -->
    @if($showAuthModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-[#142259] font-black text-lg">Admin Authorization</h3>
                            <p class="text-slate-400 text-xs">Permission required to archive sale</p>
                        </div>
                    </div>
                    <button wire:click="$set('showAuthModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-4 mt-5 text-xs text-amber-800 leading-relaxed">
                    <p class="font-bold">⚠️ Role Policy Notice:</p>
                    <p class="mt-1">As a Sales Secretary, you must request authorization from an <strong>Administrator</strong> or <strong>Operations Manager</strong> to archive this sales transaction.</p>
                </div>

                @if($authError)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 mt-3 text-xs text-red-600 font-bold">
                        {{ $authError }}
                    </div>
                @endif

                <form wire:submit="confirmAuthorizedArchive" class="mt-5 space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase font-bold">Authorizing Manager / Admin</label>
                        <select wire:model="authApproverId" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none font-bold" required>
                            @foreach($approvers as $appr)
                                <option value="{{ $appr->id }}">{{ $appr->name }} ({{ strtoupper($appr->roles->first()?->name ?? 'Admin') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase font-bold">Manager / Admin Password</label>
                        <input type="password" wire:model="authPassword" placeholder="Enter password to authorize" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                        @error('authPassword') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase font-bold">Reason for Archiving</label>
                        <textarea wire:model="authReason" rows="2" placeholder="e.g. Duplicate order entry, refunded customer settlement" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none" required></textarea>
                        @error('authReason') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showAuthModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white font-extrabold shadow-sm">Authorize & Archive</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
