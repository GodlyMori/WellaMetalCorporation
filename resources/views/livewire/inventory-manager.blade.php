<div class="space-y-4" x-data="{ isFormOpen: @entangle('isFormOpen') }">

    <!-- Toast Notification Banner -->
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
             class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 px-4 py-2.5 rounded text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Validation Error Banner -->
    @if ($errors->any())
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 px-4 py-3 rounded text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the errors in the inventory entry:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- 1. TOP SECTION: SLIDING ADD INVENTORY CARD (EXPANDABLE/COLLAPSIBLE) -->
    <!-- ============================================================== -->
    <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden transition-all duration-300">

        <!-- Form Content (Visible when isFormOpen is true) -->
        <div x-show="isFormOpen" x-collapse>
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Add inventory / product batch
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-mono">{{ count($entryItems) }} item row(s)</span>
                        <button type="button"
                                wire:click="resetEntryForm"
                                class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Existing Product Search & Auto-Fill (Mirrors Customer Fill-Up in Sales) -->
                <div class="relative">
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Search existing product to pre-fill / add stock
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.200ms="product_search"
                               placeholder="Type existing product name, category, or specs to apply..."
                               class="w-full pl-9 pr-8 py-2 bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @if($product_search)
                            <button type="button" wire:click="$set('product_search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>

                    <!-- Autocomplete Suggestions Dropdown -->
                    @if(!empty($product_search) && count($searchedExistingProducts) > 0)
                        <div class="absolute z-20 mt-1 w-full bg-white dark:bg-[#0c163b] border border-slate-200 dark:border-[#1a2858] rounded shadow-md overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($searchedExistingProducts as $sp)
                                <button type="button"
                                        wire:click="selectExistingProduct({{ $sp->id }})"
                                        class="w-full px-3.5 py-2.5 text-left hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-xs text-slate-900 dark:text-white">{{ $sp->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $sp->category }} • ₱{{ number_format($sp->tagged_price, 2) }} • {{ $sp->quantity_in_stock }} in stock</div>
                                    </div>
                                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">Apply to row</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif(!empty($product_search) && count($searchedExistingProducts) === 0)
                        <div class="absolute z-20 mt-1 w-full bg-white dark:bg-[#0c163b] border border-slate-200 dark:border-[#1a2858] rounded shadow-md p-3 text-center text-xs text-slate-400">
                            No existing catalog product found. You can type a new product directly in the rows below.
                        </div>
                    @endif
                </div>

                <!-- Dynamic Item Rows -->
                <div class="space-y-3">
                    @foreach($entryItems as $index => $item)
                        <div class="border border-slate-200 dark:border-slate-700 rounded bg-slate-50 dark:bg-[#0f1b40] p-3 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                    Item #{{ $index + 1 }}
                                </span>
                                @if(count($entryItems) > 1)
                                    <button type="button"
                                            wire:click="removeEntryRow({{ $index }})"
                                            class="text-slate-400 hover:text-rose-500 text-xs font-semibold flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Remove</span>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs">
                                <!-- Product Name -->
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Product name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model="entryItems.{{ $index }}.name"
                                           placeholder="e.g. 6-Seater Wooden Dining Set"
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    @error("entryItems.{$index}.name") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Category (Dropdown / Datalist) -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Category <span class="text-rose-500">*</span>
                                    </label>
                                    <input list="category-options-{{ $index }}"
                                           wire:model="entryItems.{{ $index }}.category"
                                           placeholder="Select or type..."
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    <datalist id="category-options-{{ $index }}">
                                        @foreach($existingCategories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </datalist>
                                    @error("entryItems.{$index}.category") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Material / Spec Notes -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Specs / notes
                                    </label>
                                    <input type="text"
                                           wire:model="entryItems.{{ $index }}.description"
                                           placeholder="e.g. Galvanized 6m"
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                </div>

                                <!-- Tagged Price -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Unit price (₱) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           wire:model="entryItems.{{ $index }}.tagged_price"
                                           placeholder="0.00"
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    @error("entryItems.{$index}.tagged_price") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Quantity -->
                                <div class="md:col-span-1">
                                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                        Stock <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           min="1"
                                           wire:model="entryItems.{{ $index }}.quantity_in_stock"
                                           placeholder="1"
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono text-center focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    @error("entryItems.{$index}.quantity_in_stock") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Low Stock Alert Threshold -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-semibold text-amber-700 dark:text-amber-400 mb-1">
                                        Low stock threshold <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           min="0"
                                           wire:model="entryItems.{{ $index }}.low_stock_threshold"
                                           placeholder="5"
                                           title="When inventory falls to or below this quantity, a low-stock alert will trigger"
                                           class="w-full bg-white dark:bg-[#0f1b40] border border-amber-300 dark:border-amber-700/60 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono text-center focus:outline-none focus:border-amber-500 transition-colors">
                                    @error("entryItems.{$index}.low_stock_threshold") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form Bottom Actions -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                    <button type="button"
                            wire:click="addEntryRow"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded border border-dashed border-slate-300 dark:border-slate-700 hover:border-[#142259] text-slate-600 dark:text-slate-400 hover:text-[#142259] text-xs font-semibold transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Add another item</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button"
                                wire:click="resetEntryForm"
                                class="text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 px-4 py-2 cursor-pointer transition-colors">
                            Clear
                        </button>
                        <button type="button"
                                wire:click="saveInventoryEntries"
                                wire:loading.attr="disabled"
                                class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer flex items-center gap-2">
                            <span wire:loading.remove>Save to inventory</span>
                            <span wire:loading class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sliding Pull Handle at Bottom Center (collapse handle) -->
        <button type="button"
                @click="isFormOpen = !isFormOpen"
                class="w-full py-2 bg-slate-50 hover:bg-slate-100 dark:bg-[#0f1b40] dark:hover:bg-[#142259]/20 border-t border-slate-200 dark:border-[#1a2858] flex flex-col items-center justify-center transition-colors cursor-pointer group"
                title="Click to slide Add Inventory form up/down">
            <!-- Double Horizontal Lines Grip Handle -->
            <div class="flex flex-col items-center gap-0.5">
                <div class="w-10 h-0.5 bg-slate-300 dark:bg-slate-600 group-hover:bg-[#142259] rounded-full transition-colors"></div>
                <div class="w-10 h-0.5 bg-slate-300 dark:bg-slate-600 group-hover:bg-[#142259] rounded-full transition-colors"></div>
            </div>
            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 group-hover:text-[#142259] mt-1 flex items-center gap-1">
                <span x-text="isFormOpen ? 'Collapse add inventory section' : 'Add inventory section — click to slide down'"></span>
                <svg class="w-3 h-3 transition-transform" :class="isFormOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- 2. PRODUCT TABLE WORKBENCH (INTEGRATED TOOLBAR & DATA GRID)     -->
    <!-- ============================================================== -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">

        <!-- Integrated Table Workbench Bar -->
        <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <!-- Search Bar (Left) -->
            <div class="relative w-full lg:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search product SKU, name, or material specs..."
                       class="w-full pl-9 pr-3.5 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
            </div>

            <!-- Filters (Right) -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Category Filter Tab Strip -->
                <div class="inline-flex items-center gap-3 border-b border-slate-200 dark:border-[#1a2858] overflow-x-auto scrollbar-none pb-0.5">
                    <button type="button"
                            wire:click="setCategory('ALL')"
                            class="whitespace-nowrap cursor-pointer pb-1.5 {{ $selectedCategory === 'ALL' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white font-semibold text-xs' : 'text-slate-500 dark:text-slate-400 text-xs hover:text-slate-900 dark:hover:text-white transition-colors' }}">
                        All ({{ $allCount }})
                    </button>
                    @foreach($existingCategories as $cat)
                        <button type="button"
                                wire:click="setCategory('{{ $cat }}')"
                                class="whitespace-nowrap cursor-pointer pb-1.5 {{ $selectedCategory === $cat ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white font-semibold text-xs' : 'text-slate-500 dark:text-slate-400 text-xs hover:text-slate-900 dark:hover:text-white transition-colors' }}">
                            {{ $cat }} ({{ $categoryCounts[$cat] ?? 0 }})
                        </button>
                    @endforeach
                    <button type="button"
                            wire:click="setCategory('ARCHIVED')"
                            class="whitespace-nowrap cursor-pointer pb-1.5 {{ $selectedCategory === 'ARCHIVED' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white font-semibold text-xs' : 'text-slate-500 dark:text-slate-400 text-xs hover:text-slate-900 dark:hover:text-white transition-colors' }}">
                        Archived ({{ $archivedCount }})
                    </button>
                </div>

                <!-- Stock Level Filter -->
                <select wire:model.live="stockFilter"
                        class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors cursor-pointer">
                    <option value="all">All quantities</option>
                    <option value="in_stock">In stock (&gt; 4 units)</option>
                    <option value="low_stock">Low stock (&le; 4 units)</option>
                    <option value="out_of_stock">Out of stock (0 units)</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                        <th class="py-2.5 px-3.5 w-24 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product SKU</th>
                        <th class="py-2.5 px-3.5 min-w-[200px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product name &amp; spec</th>
                        <th class="py-2.5 px-3.5 w-32 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                        <th class="py-2.5 px-3.5 w-28 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Unit price (₱)</th>
                        <th class="py-2.5 px-3.5 w-28 text-right whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Stock on hand</th>
                        <th class="py-2.5 px-3.5 w-28 text-center whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="py-2.5 px-3.5 w-32 text-center whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858]">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                            <td class="py-2.5 px-3.5 text-xs font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $product->formatted_id }}
                            </td>
                            <td class="py-2.5 px-3.5 text-xs">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $product->name }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-sm mt-0.5">{{ $product->description ?: 'No material specs recorded' }}</div>
                            </td>
                            <td class="py-2.5 px-3.5 text-xs whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $product->category }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3.5 text-xs text-right font-mono tabular-nums text-slate-900 dark:text-white whitespace-nowrap">
                                ₱{{ number_format($product->tagged_price, 2) }}
                            </td>
                            <td class="py-2.5 px-3.5 text-xs text-right whitespace-nowrap">
                                @if($product->quantity_in_stock <= 0)
                                    <span class="font-mono tabular-nums font-semibold text-rose-600 dark:text-rose-400">
                                        0 units
                                    </span>
                                @elseif($product->quantity_in_stock <= ($product->low_stock_threshold ?? 5))
                                    <span class="font-mono tabular-nums font-semibold text-amber-700 dark:text-amber-400">
                                        {{ $product->quantity_in_stock }} units
                                    </span>
                                @else
                                    <span class="font-mono tabular-nums text-slate-700 dark:text-slate-300">
                                        {{ $product->quantity_in_stock }} units
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">Alert &le; {{ $product->low_stock_threshold ?? 5 }}</div>
                            </td>
                            <td class="py-2.5 px-3.5 text-xs text-center whitespace-nowrap">
                                @if($product->status === 'archived')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        Archived
                                    </span>
                                @elseif($product->quantity_in_stock <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                                        Out of stock
                                    </span>
                                @elseif($product->quantity_in_stock <= ($product->low_stock_threshold ?? 5))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                        Low stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                        In stock
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3.5 text-xs text-center">
                                <div class="flex items-center justify-center gap-3">
                                    @if($product->status === 'archived')
                                        <button type="button" wire:click="restoreProduct({{ $product->id }})" class="text-xs font-semibold text-[#142259] dark:text-slate-300 hover:text-[#0e1840] transition-colors cursor-pointer">Restore</button>
                                    @else
                                        <button type="button" wire:click="openRestockModal({{ $product->id }})" class="text-xs font-semibold text-[#142259] dark:text-slate-300 hover:text-[#0e1840] dark:hover:text-white transition-colors cursor-pointer" title="Restock">Restock</button>
                                        <button type="button" wire:click="editProduct({{ $product->id }})" class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer" title="Edit">Edit</button>
                                        <button type="button" wire:click="archiveProduct({{ $product->id }})" wire:confirm="Archive this product from catalog?" class="text-xs font-medium text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer" title="Archive">Archive</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center">
                                <p class="text-xs text-slate-400 dark:text-slate-500">No products found matching filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-[#1a2858]">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: EDIT PRODUCT DETAILS                                    -->
    <!-- ============================================================== -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showEditModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Edit product details
                    </h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="updateProduct" class="p-5 space-y-4 text-xs">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Product name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="edit_name" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('edit_name') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <input list="edit-category-options" wire:model="edit_category" placeholder="Select or type..." class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            <datalist id="edit-category-options">
                                @foreach($existingCategories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </datalist>
                            @error('edit_category') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Unit price (₱) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.01" min="0" wire:model="edit_price" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('edit_price') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Quantity in stock <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="0" wire:model="edit_stock" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('edit_stock') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-amber-700 dark:text-amber-400 mb-1">
                                Low stock threshold <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="0" wire:model="edit_low_stock_threshold" placeholder="5" title="Threshold when item is flagged as low stock" class="w-full bg-white dark:bg-[#0f1b40] border border-amber-300 dark:border-amber-700/60 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-amber-500 transition-colors">
                            @error('edit_low_stock_threshold') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Material / spec notes
                        </label>
                        <textarea wire:model="edit_description" rows="2" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showEditModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: BRANCH RESTOCK INFLOW                                   -->
    <!-- ============================================================== -->
    @if ($showRestockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showRestockModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Branch restock transfer
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $restockProductName }}</p>
                    </div>
                    <button wire:click="$set('showRestockModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveRestock" class="p-5 space-y-4 text-xs">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Quantity to add <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="1" wire:model="restockQuantity" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('restockQuantity') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Source branch <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="sourceBranch" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            <option value="Toril">Toril Branch</option>
                            <option value="Main Warehouse">Main Warehouse</option>
                            <option value="Tagum">Tagum Branch</option>
                            <option value="Supplier Direct">Supplier Direct</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Transfer notes / reference
                        </label>
                        <input type="text" wire:model="restockNotes" placeholder="e.g. Delivery Waybill #1042" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showRestockModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">Receive stock</button>
                    </div>
                </form>
            </div>
        </div>
    @endif


</div>
