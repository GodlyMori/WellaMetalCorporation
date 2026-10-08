<div class="space-y-4">

    <!-- Alert / Success Notification Banner -->
    @if($successMessage)
        <div class="p-3 rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold">{{ $successMessage }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Validation Error Banner -->
    @if ($errors->any())
        <div class="p-3 rounded bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the errors before completing transaction:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- 1. COMPACT CUSTOMER DISPATCH BAR (STREAMLINED WORKFLOW)         -->
    <!-- ============================================================== -->
    <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-3.5 sm:p-4">
        <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Customer assignment</h2>
            </div>
            @if($selected_customer_id || $customer_name)
                <button type="button" wire:click="clearSelectedCustomer" class="text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                    Clear customer
                </button>
            @endif
        </div>

        <div class="space-y-3">
            <!-- Search Existing Customer Field with Live Autocomplete -->
            <div class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.250ms="customer_search"
                           placeholder="Search existing customer by name, contact phone, or customer ID..."
                           class="w-full pl-9 pr-8 py-2 bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    @if($customer_search)
                        <button type="button" wire:click="$set('customer_search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>

                <!-- Autocomplete Dropdown -->
                @if(!empty($customer_search) && count($searchedCustomers) > 0)
                    <div class="absolute z-20 mt-1 w-full rounded border border-slate-200 dark:border-[#1a2858] shadow-md bg-white dark:bg-[#0c163b] overflow-hidden divide-y divide-slate-100 dark:divide-[#1a2858]">
                        @foreach($searchedCustomers as $sc)
                            <button type="button"
                                    wire:click="selectCustomer({{ $sc->id }})"
                                    class="w-full px-3.5 py-2.5 text-left hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $sc->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $sc->customer_number }} • {{ $sc->phone ?: 'No phone' }}</div>
                                </div>
                                <span class="text-[11px] font-semibold text-[#142259] dark:text-slate-300">Link record</span>
                            </button>
                        @endforeach
                    </div>
                @elseif(!empty($customer_search) && count($searchedCustomers) === 0)
                    <div class="absolute z-20 mt-1 w-full rounded border border-slate-200 dark:border-[#1a2858] shadow-md bg-white dark:bg-[#0c163b] p-3 text-center text-xs text-slate-400">
                        No registered customer matched. Fill fields below to record a new customer.
                    </div>
                @endif
            </div>

            <!-- Customer Details Inline Form -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Full name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="customer_name"
                           placeholder="e.g. Maria Santos"
                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    @error('customer_name') <span class="text-rose-500 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Contact phone <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="customer_phone"
                           placeholder="0917-000-0000"
                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    @error('customer_phone') <span class="text-rose-500 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Address / Destination <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="customer_address"
                           placeholder="e.g. Davao City"
                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    @error('customer_address') <span class="text-rose-500 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Internal notes
                    </label>
                    <input type="text"
                           wire:model="sale_notes"
                           placeholder="Pickup schedule or specs"
                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. MAIN CHECKOUT WORKSTATION (CATALOG & CART DOMINANT)         -->
    <!-- ============================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- LEFT COLUMN (7 COLS): PRODUCT CATALOG (MAX VIEWPORT REAL ESTATE) -->
        <div class="lg:col-span-7 bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5 flex flex-col">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800 gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Product catalog</h2>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-56">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.250ms="catalogSearch"
                           placeholder="Search product SKU, name..."
                           class="w-full pl-8 pr-3 bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                </div>
            </div>

            <!-- Category Filter Tabs -->
            <div class="flex items-center gap-4 border-b border-slate-200 dark:border-[#1a2858] mt-3 overflow-x-auto scrollbar-none pb-1">
                <button type="button"
                        wire:click="$set('catalogCategory', 'ALL')"
                        class="{{ $catalogCategory === 'ALL' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors' }} text-xs pb-1.5 cursor-pointer whitespace-nowrap">
                    All items
                </button>
                @foreach($existingCategories as $cat)
                    <button type="button"
                            wire:click="$set('catalogCategory', '{{ $cat }}')"
                            class="{{ $catalogCategory === $cat ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors' }} text-xs pb-1.5 cursor-pointer whitespace-nowrap">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <!-- Deep Scrollable Product List (max-h-[520px]) -->
            <div class="mt-3 overflow-y-auto max-h-[520px] divide-y divide-slate-100 dark:divide-[#1a2858]">
                @forelse($availableProducts as $prod)
                    <div wire:click="addProductToCart({{ $prod->id }})"
                         class="py-2.5 px-1.5 hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors cursor-pointer flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-xs text-slate-900 dark:text-white truncate">
                                    {{ $prod->name }}
                                </span>
                                <span class="text-[11px] font-medium px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $prod->category }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5 truncate">
                                {{ $prod->description ?: 'Standard manufacturing specification' }}
                            </div>
                            <div class="mt-1 flex items-center gap-2 text-[11px]">
                                <span class="font-medium {{ $prod->quantity_in_stock <= 4 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $prod->quantity_in_stock }} units in stock
                                </span>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-semibold font-mono text-slate-900 dark:text-white">
                                ₱{{ number_format($prod->tagged_price, 2) }}
                            </div>
                            <button type="button" class="mt-1 text-xs font-semibold text-[#142259] dark:text-slate-300 hover:text-[#0e1840] transition-colors">
                                + Add to cart
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-xs text-slate-400 dark:text-slate-500">
                        No active in-stock products found for this category.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT COLUMN (5 COLS): SELECTED CART & SETTLEMENT -->
        <div class="lg:col-span-5 space-y-4 flex flex-col">

            <!-- CART SUMMARY -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5 flex flex-col">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Selected items ({{ count($items) }})
                        </h2>
                    </div>
                    @if(count($items) > 0)
                        <button type="button" wire:click="clearCart" class="text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                            Clear cart
                        </button>
                    @endif
                </div>

                <!-- Line Items List -->
                <div class="mt-2.5 flex-1 overflow-y-auto max-h-[220px] divide-y divide-slate-100 dark:divide-[#1a2858]">
                    @forelse($items as $idx => $item)
                        <div class="py-2 flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $item['name'] }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    ₱{{ number_format($item['unit_price'], 2) }} each
                                </div>
                            </div>

                            <!-- Stepper -->
                            <div class="rounded border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#0f1b40] flex items-center">
                                <button type="button" wire:click="decrementQuantity({{ $idx }})" class="w-6 h-6 flex items-center justify-center font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition-colors">
                                    -
                                </button>
                                <span class="w-7 text-center font-bold font-mono text-slate-900 dark:text-white text-xs">
                                    {{ $item['quantity'] }}
                                </span>
                                <button type="button" wire:click="incrementQuantity({{ $idx }})" class="w-6 h-6 flex items-center justify-center font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 rounded transition-colors">
                                    +
                                </button>
                            </div>

                            <!-- Line Subtotal -->
                            <div class="text-right w-20">
                                <div class="font-bold font-mono text-slate-900 dark:text-white tabular-nums">
                                    ₱{{ number_format($item['subtotal'], 2) }}
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button type="button" wire:click="removeItem({{ $idx }})" class="text-slate-400 hover:text-rose-600 transition-colors p-1" title="Remove item">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            No products in cart yet. Click items in catalog to add.
                        </div>
                    @endforelse
                </div>

                <!-- TOTAL PAYABLE SUMMARY -->
                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Items subtotal:</span>
                        <span class="font-mono font-medium text-slate-800 dark:text-slate-200 tabular-nums">₱{{ number_format($this->totalAmount, 2) }}</span>
                    </div>

                    @if($this->discountAmount > 0)
                        <div class="flex justify-between text-amber-700 dark:text-amber-400">
                            <span>Promo discount:</span>
                            <span class="font-mono font-semibold tabular-nums">-₱{{ number_format($this->discountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-baseline pt-2 border-t border-slate-200 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">Total payable:</span>
                        <span class="text-xl font-bold font-mono text-[#142259] dark:text-white tabular-nums">
                            ₱{{ number_format($this->netAmount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- PAYMENT & SETTLEMENT -->
            <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4 sm:p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200 dark:border-slate-800">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Payment & settlement</h2>
                    </div>

                    <!-- Promo Code / Event Discount -->
                    <div class="rounded bg-slate-50 dark:bg-[#0f1b40] border border-slate-200 dark:border-slate-700 mt-3 p-2.5 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-400">
                                Event promo / discount
                            </span>
                            @if($selected_promotion_id)
                                <button type="button" wire:click="clearPromo" class="text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                                    Remove
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <select wire:model.live="selected_promotion_id" class="w-full bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500">
                                <option value="">No promo applied</option>
                                @foreach($this->availablePromotions as $promo)
                                    <option value="{{ $promo->id }}">
                                        {{ $promo->name }} ({{ $promo->discount_type === 'percentage' ? (float)$promo->discount_value . '% off' : '₱' . number_format($promo->discount_value, 2) . ' off' }})
                                    </option>
                                @endforeach
                            </select>

                            <div class="flex items-center gap-1.5">
                                <input type="text"
                                       wire:model="promo_code_input"
                                       placeholder="Code"
                                       class="flex-1 uppercase font-mono bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259]">
                                <button type="button"
                                        wire:click="applyPromoCode"
                                        class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-2.5 py-1.5 rounded transition-colors cursor-pointer">
                                    Apply
                                </button>
                            </div>
                        </div>

                        @if($promoErrorMessage)
                            <p class="text-rose-600 dark:text-rose-400 text-[11px] font-medium">{{ $promoErrorMessage }}</p>
                        @endif
                    </div>

                    <!-- Payment Mode Switch -->
                    <div class="mt-3">
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Settlement mode
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                    wire:click="$set('payment_type', 'full')"
                                    class="p-2 rounded border text-left transition-all cursor-pointer {{ $payment_type === 'full' ? 'bg-[#142259]/5 border-[#142259] text-[#142259] dark:text-white font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-[#0f1b40]' }}">
                                <div class="text-xs">Full cash settlement</div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">100% full payment</span>
                            </button>

                            <button type="button"
                                    wire:click="$set('payment_type', 'layaway')"
                                    class="p-2 rounded border text-left transition-all cursor-pointer {{ $payment_type === 'layaway' ? 'bg-amber-600/10 border-amber-500 text-amber-700 dark:text-amber-400 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-[#0f1b40]' }}">
                                <div class="text-xs">Lay-away / reserve</div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Min 20% DP • 3 months</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cash Settlement Calculation -->
                    @if($payment_type === 'full')
                        <div class="rounded bg-slate-50 dark:bg-[#0f1b40] border border-slate-200 dark:border-slate-700 p-3 mt-3 space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">Tendered cash</span>
                                <span class="text-[11px] text-slate-400">Due: <strong class="font-mono text-slate-900 dark:text-white tabular-nums">₱{{ number_format($this->netAmount, 2) }}</strong></span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5 items-end">
                                <div>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           wire:model.live="cash_tendered"
                                           placeholder="0.00"
                                           class="w-full bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white font-mono placeholder-slate-400 focus:outline-none focus:border-[#142259]">
                                    @error('cash_tendered') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="p-2 rounded border text-right {{ (float)$cash_tendered >= $this->netAmount && $this->netAmount > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900' : ((float)$cash_tendered > 0 ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900' : 'bg-white dark:bg-[#0c163b] border-slate-200 dark:border-slate-700') }}">
                                    <span class="text-[10px] font-medium block text-slate-400">Change:</span>
                                    <span class="text-sm font-bold font-mono {{ (float)$cash_tendered >= $this->netAmount && $this->netAmount > 0 ? 'text-emerald-700 dark:text-emerald-400' : ((float)$cash_tendered > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400') }} tabular-nums">
                                        ₱{{ (float)$cash_tendered >= $this->netAmount && $this->netAmount > 0 ? number_format($this->changeDue, 2) : '0.00' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Lay-Away Downpayment Calculation -->
                    @if($payment_type === 'layaway')
                        <div class="rounded bg-slate-50 dark:bg-[#0f1b40] border border-slate-200 dark:border-slate-700 p-3 mt-3 space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-amber-700 dark:text-amber-400">Downpayment calculation</span>
                                <span class="text-[11px] text-slate-400">Min 20%: <strong class="font-mono text-amber-600 tabular-nums">₱{{ number_format($this->minDownpayment, 2) }}</strong></span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5 items-end">
                                <div class="flex items-center gap-1.5">
                                    <input type="number"
                                           step="0.01"
                                           min="{{ $this->minDownpayment }}"
                                           max="{{ $this->netAmount }}"
                                           wire:model.live="downpayment_amount"
                                           placeholder="{{ number_format($this->minDownpayment, 2) }}"
                                           class="w-full bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white font-mono focus:outline-none focus:border-[#142259]">
                                    <button type="button"
                                            wire:click="setMinimumDownpayment"
                                            class="px-2 py-1.5 rounded bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 text-amber-700 dark:text-amber-400 border border-amber-200 text-xs font-semibold whitespace-nowrap cursor-pointer">
                                        20%
                                    </button>
                                </div>

                                <div class="p-2 rounded border bg-white dark:bg-[#0c163b] border-slate-200 dark:border-slate-700 text-right">
                                    <span class="text-[10px] font-medium block text-slate-400">Remaining bal:</span>
                                    <span class="text-sm font-bold font-mono text-rose-600 dark:text-rose-400 tabular-nums">
                                        ₱{{ number_format($this->calculatedBalance, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-[11px] text-amber-700 dark:text-amber-400">
                                Reserved 3 months until {{ now()->addMonths(3)->format('M d, Y') }}.
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Process Sale CTA -->
                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button"
                            wire:click="recordSale"
                            wire:loading.attr="disabled"
                            @if(count($items) === 0) disabled @endif
                            class="w-full py-3 rounded font-semibold text-xs transition-colors flex items-center justify-center gap-2 {{ count($items) > 0 ? 'bg-[#142259] hover:bg-[#0e1840] text-white cursor-pointer' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed' }}">
                        <span wire:loading.remove>
                            @if($payment_type === 'layaway')
                                Confirm lay-away reservation (Pay ₱{{ number_format((float)$downpayment_amount, 2) }})
                            @else
                                Complete cash sale (Pay ₱{{ number_format($this->netAmount, 2) }})
                            @endif
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            Processing transaction...
                        </span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- DIGITAL SALES RECEIPT MODAL                                   -->
    <!-- ============================================================== -->
    @if($showReceiptModal && $receiptData)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="closeReceiptModal">

                <!-- Receipt Header -->
                <div class="p-6 text-center border-b border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#0f1b40]">
                    <div class="flex items-center justify-end mb-2">
                        <button type="button" wire:click="closeReceiptModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <h2 class="text-base font-bold tracking-wider text-slate-900 dark:text-white">
                        Wella Metal Corporation
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Furniture & Metal Fabrications</p>
                    <p class="text-[11px] font-mono text-slate-400 mt-1 uppercase">Official sales receipt • Store pickup</p>
                </div>

                <!-- Receipt Metadata & Customer -->
                <div class="p-5 space-y-3 text-xs border-b border-dashed border-slate-300 dark:border-slate-700">
                    <div class="flex justify-between">
                        <span class="text-slate-400 uppercase tracking-wider text-[11px]">Order number:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white text-xs">{{ $receiptData['order_number'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 uppercase tracking-wider text-[11px]">Date / time:</span>
                        <span class="text-slate-700 dark:text-slate-300 font-mono">{{ $receiptData['sale_date'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 uppercase tracking-wider text-[11px]">Cashier:</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ $receiptData['cashier'] }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Customer:</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $receiptData['customer_name'] }}</span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-400">Contact:</span>
                            <span class="text-slate-600 dark:text-slate-400 font-mono">{{ $receiptData['customer_phone'] }}</span>
                        </div>
                        @if($receiptData['customer_address'])
                            <div class="flex justify-between text-[11px]">
                                <span class="text-slate-400">Address:</span>
                                <span class="text-slate-600 dark:text-slate-400 text-right truncate max-w-[200px]">{{ $receiptData['customer_address'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Line Items Breakdown -->
                <div class="p-5 space-y-2 text-xs border-b border-dashed border-slate-300 dark:border-slate-700 max-h-48 overflow-y-auto">
                    <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 pb-1 border-b border-slate-200 dark:border-slate-800 flex justify-between">
                        <span>Item description</span>
                        <span>Subtotal</span>
                    </div>
                    @foreach($receiptData['items'] as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex-1 pr-4">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item['quantity'] }} × ₱{{ number_format($item['unit_price'], 2) }}</div>
                            </div>
                            <div class="font-mono font-bold text-slate-900 dark:text-white text-right">
                                ₱{{ number_format($item['subtotal'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Breakdown -->
                <div class="p-5 space-y-2 text-xs border-b border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#0f1b40]">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Items subtotal:</span>
                        <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($receiptData['total_amount'], 2) }}</span>
                    </div>

                    @if($receiptData['discount_amount'] > 0)
                        <div class="flex justify-between text-amber-700 dark:text-amber-400">
                            <span>Promo discount ({{ $receiptData['promo_name'] }}):</span>
                            <span class="font-mono font-semibold">-₱{{ number_format($receiptData['discount_amount'], 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-sm font-bold text-slate-900 dark:text-white pt-1.5 border-t border-slate-200 dark:border-slate-800">
                        <span>Total payable:</span>
                        <span class="font-mono text-base text-[#142259] dark:text-white">₱{{ number_format($receiptData['net_amount'], 2) }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400 text-[11px]">
                            <span>Settlement method:</span>
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ $receiptData['payment_type'] === 'layaway' ? 'Lay-away reservation' : 'Full cash settlement' }}
                            </span>
                        </div>

                        @if($receiptData['payment_type'] === 'full')
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Cash tendered:</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">₱{{ number_format($receiptData['cash_tendered'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-emerald-700 dark:text-emerald-400 font-bold">
                                <span>Change given:</span>
                                <span class="font-mono">₱{{ number_format($receiptData['change_due'], 2) }}</span>
                            </div>
                        @else
                            <div class="flex justify-between text-amber-700 dark:text-amber-400 font-semibold">
                                <span>Downpayment paid:</span>
                                <span class="font-mono">₱{{ number_format($receiptData['downpayment_amount'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-rose-600 dark:text-rose-400 font-bold">
                                <span>Remaining balance:</span>
                                <span class="font-mono">₱{{ number_format($receiptData['remaining_balance'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500 text-[11px]">
                                <span>Reserved until:</span>
                                <span class="font-mono text-amber-600 dark:text-amber-400">{{ $receiptData['expiry_date'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Receipt Footer & Dismissal -->
                <div class="px-5 py-4 border-t border-slate-200 dark:border-[#1a2858] flex items-center justify-between gap-2 bg-white dark:bg-[#0c163b]">
                    <p class="text-[11px] text-slate-400 flex-1">
                        Goods strictly for store pickup • Thank you for choosing Wella Metal Corporation!
                    </p>
                    <button type="button"
                            wire:click="closeReceiptModal"
                            class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer whitespace-nowrap">
                        Close receipt
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
