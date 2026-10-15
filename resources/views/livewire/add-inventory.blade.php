<div class="space-y-6 max-w-7xl mx-auto py-2">
    <!-- Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('inventory') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Inventory Yard</span>
                </a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Add Inventory Batch</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </span>
                Stock Intake & Batch Entry
            </h1>
            <p class="text-xs text-slate-500 mt-1">Record newly fabricated catalog products or restock physical yard quantities in batch</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('inventory') }}"
               class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-300 rounded shadow-xs transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>View Inventory Yard</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if ($successMessage)
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="p-1 rounded bg-emerald-100 text-emerald-700 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-emerald-900">Batch Saved Successfully</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ $successMessage }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('inventory') }}" class="text-xs font-semibold text-emerald-800 underline hover:text-emerald-950">Go to Yard &rarr;</a>
                <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-500 hover:text-emerald-700 text-xs p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    @if ($errorMessage)
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
            <div class="p-1 rounded bg-rose-100 text-rose-700 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-rose-900">Validation Notice</h3>
                <p class="text-xs text-rose-700 mt-0.5">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the following row errors:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Existing Products Banner -->
    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-xs">
        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            Search Existing Product Catalog (Auto-fill row to restock)
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.250ms="product_search"
                   placeholder="Type existing product name, category, or specs to search and add stock..."
                   class="w-full pl-10 pr-9 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
            
            <div wire:loading wire:target="product_search" class="absolute inset-y-0 right-8 pr-2 flex items-center">
                <svg class="animate-spin w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            </div>

            @if($product_search)
                <button type="button" wire:click="$set('product_search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            @endif
        </div>

        <!-- Autocomplete Suggestions List -->
        @if(!empty($product_search) && count($searchedExistingProducts) > 0)
            <div class="mt-2 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden divide-y divide-slate-100">
                <div class="bg-slate-50 px-3 py-1.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Matching Catalog Products (Click to pre-fill)
                </div>
                @foreach($searchedExistingProducts as $sp)
                    <button type="button"
                            wire:click="selectExistingProduct({{ $sp->id }})"
                            class="w-full px-4 py-3 text-left hover:bg-blue-50/60 transition-colors flex items-center justify-between cursor-pointer group">
                        <div>
                            <div class="font-semibold text-xs text-slate-900 group-hover:text-blue-700">{{ $sp->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                {{ $sp->category }} &bull; ₱{{ number_format($sp->tagged_price, 2) }} &bull; Current Stock: <span class="font-semibold text-slate-700">{{ $sp->quantity_in_stock }}</span>
                            </div>
                        </div>
                        <span class="text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            + Restock This Item
                        </span>
                    </button>
                @endforeach
            </div>
        @elseif(!empty($product_search) && count($searchedExistingProducts) === 0)
            <div class="mt-2 bg-slate-50 border border-slate-200 rounded-lg p-3 text-center text-xs text-slate-500">
                No matching catalog product found. You can enter new product details directly in the rows below.
            </div>
        @endif
    </div>

    <!-- Batch Item Workbench -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden"
         x-data="{
             duplicateErrors: {},
             hasDuplicates: false,
             hasIncomplete: false,
             get hasErrors() {
                 return this.hasDuplicates || this.hasIncomplete;
             },
             checkRows() {
                 const rows = this.$el.querySelectorAll('.batch-entry-row');
                 const seen = new Map();
                 const newDuplicates = {};
                 let incompleteFound = false;

                 rows.forEach((row, idx) => {
                     const prodIdInput = row.querySelector('[data-field=\'product_id\']');
                     const nameInput = row.querySelector('[data-field=\'name\']');
                     const catInput = row.querySelector('[data-field=\'category\']');
                     const specsInput = row.querySelector('[data-field=\'description\']');
                     const priceInput = row.querySelector('[data-field=\'tagged_price\']');
                     const qtyInput = row.querySelector('[data-field=\'quantity_in_stock\']');

                     const prodId = prodIdInput ? (prodIdInput.value || '').trim() : '';
                     const name = nameInput ? (nameInput.value || '').trim() : '';
                     const cat = catInput ? (catInput.value || '').trim() : '';
                     const specs = specsInput ? (specsInput.value || '').trim() : '';
                     const price = priceInput ? (priceInput.value || '').trim() : '';
                     const qty = qtyInput ? (qtyInput.value || '').trim() : '';

                     if (!name || price === '' || isNaN(price) || parseFloat(price) < 0 || qty === '' || isNaN(qty) || parseInt(qty) < 1) {
                         incompleteFound = true;
                     }

                     if (!name && !prodId) return;

                     const normName = name.toLowerCase();
                     const normCat = cat.toLowerCase();
                     const normSpecs = specs.toLowerCase();
                     const key = prodId ? ('id:' + prodId) : (normName + '|' + normCat + '|' + normSpecs);

                     if (seen.has(key)) {
                         const firstIdx = seen.get(key);
                         newDuplicates[idx] = 'Duplicate entry: This product is already entered in Item #' + (firstIdx + 1) + '. Adjust quantity there instead.';
                     } else {
                         seen.set(key, idx);
                     }
                 });

                 this.duplicateErrors = newDuplicates;
                 this.hasDuplicates = Object.keys(newDuplicates).length > 0;
                 this.hasIncomplete = incompleteFound;
             }
         }"
         x-init="checkRows(); $nextTick(() => checkRows())"
         @input="checkRows()"
         @change="checkRows()">

        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Item Batch List</span>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-mono">
                    {{ count($entryItems) }} {{ count($entryItems) === 1 ? 'row' : 'rows' }}
                </span>
            </div>
            <p class="text-xs text-slate-500">Fill in all required fields indicated with (*)</p>
        </div>

        <div class="p-5 space-y-4">
            @foreach($entryItems as $index => $item)
                @php
                    $isExisting = !empty($item['is_existing']);
                    $rowHasDuplicate = isset($duplicateErrors[$index]);
                @endphp
                <div class="batch-entry-row rounded-lg p-4 space-y-3 transition-colors border {{ $rowHasDuplicate ? 'border-red-500 bg-red-50/20 ring-1 ring-red-500/30' : ($isExisting ? 'border-blue-200 bg-blue-50/20' : 'border-slate-200 bg-slate-50/60') }}"
                     :class="duplicateErrors[{{ $index }}] ? 'border-red-500 bg-red-50/20 ring-1 ring-red-500/30' : ({{ $isExisting ? 'true' : 'false' }} ? 'border-blue-200 bg-blue-50/20' : 'border-slate-200 bg-slate-50/60')">

                    <input type="hidden" data-field="product_id" value="{{ $item['product_id'] ?? '' }}">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-2.5 py-1 rounded">
                                Item #{{ $index + 1 }}
                            </span>
                            @if($isExisting)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Existing Catalog Product (Price &amp; Category locked — stock addition only)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    + New Product Creation
                                </span>
                            @endif
                        </div>
                        @if(count($entryItems) > 1)
                            <button type="button"
                                    wire:click="removeEntryRow({{ $index }})"
                                    class="text-slate-400 hover:text-rose-600 text-xs font-semibold flex items-center gap-1 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Remove Row</span>
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs">
                        <!-- Product Name -->
                        <div class="md:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                Product Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   data-field="name"
                                   wire:model.blur="entryItems.{{ $index }}.name"
                                   @if($isExisting) readonly @endif
                                   placeholder="e.g. 6-Seater Wooden Dining Set"
                                   class="w-full {{ $isExisting ? 'bg-slate-100 text-slate-600 cursor-not-allowed' : 'bg-white text-slate-900' }} border border-slate-300 rounded-md px-3 py-2 text-xs placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                            @error("entryItems.{$index}.name") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Category -->
                        <div class="md:col-span-2 relative" x-data="{ open: false }" :class="{ 'z-40': open }">
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                Category <span class="text-rose-500">*</span>
                                @if($isExisting) <span class="text-[10px] text-slate-400 font-normal">(Locked)</span> @endif
                            </label>
                            @if($isExisting)
                                <input type="text"
                                       data-field="category"
                                       wire:model="entryItems.{{ $index }}.category"
                                       readonly
                                       disabled
                                       class="w-full bg-slate-100 border border-slate-300 rounded-md px-3 py-2 text-xs text-slate-500 cursor-not-allowed">
                            @else
                                <div class="relative">
                                    <input type="text"
                                           data-field="category"
                                           wire:model.blur="entryItems.{{ $index }}.category"
                                           @focus="open = true"
                                           @click="open = true"
                                           @click.outside="open = false"
                                           placeholder="Select or type..."
                                           autocomplete="off"
                                           class="w-full bg-white border border-slate-300 rounded-md pl-3 pr-7 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors cursor-pointer">
                                    <button type="button" 
                                            @click="open = !open" 
                                            class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-slate-600 pointer-events-auto">
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Custom Dropdown Menu -->
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 mt-1 w-full min-w-[200px] bg-white border border-slate-200 rounded-md shadow-xl overflow-hidden divide-y divide-slate-100 max-h-56 overflow-y-auto ring-1 ring-black/10"
                                     style="display: none;">
                                    @foreach($existingCategories as $cat)
                                        <button type="button"
                                                @click="$wire.set('entryItems.{{ $index }}.category', '{{ addslashes($cat) }}'); open = false; $nextTick(() => checkRows());"
                                                class="w-full px-3 py-2 text-left hover:bg-slate-50 transition-colors flex items-center justify-between group cursor-pointer">
                                            <span class="font-medium text-xs text-slate-800 group-hover:text-blue-600">{{ $cat }}</span>
                                            <span class="text-[10px] text-slate-400 group-hover:text-slate-600">Select</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @error("entryItems.{$index}.category") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Specs / Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                Specs / Notes
                                @if($isExisting) <span class="text-[10px] text-slate-400 font-normal">(Locked)</span> @endif
                            </label>
                            <input type="text"
                                   data-field="description"
                                   wire:model.blur="entryItems.{{ $index }}.description"
                                   @if($isExisting) readonly disabled @endif
                                   placeholder="e.g. Galvanized 6m"
                                   class="w-full {{ $isExisting ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' }} border border-slate-300 rounded-md px-3 py-2 text-xs placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                        </div>

                        <!-- Unit Price -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                Unit Price (₱) <span class="text-rose-500">*</span>
                                @if($isExisting) <span class="text-[10px] text-slate-400 font-normal">(Locked)</span> @endif
                            </label>
                            <input type="number"
                                   data-field="tagged_price"
                                   step="0.01"
                                   min="0"
                                   wire:model.blur="entryItems.{{ $index }}.tagged_price"
                                   @if($isExisting) readonly disabled @endif
                                   placeholder="0.00"
                                   class="w-full {{ $isExisting ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' }} border border-slate-300 rounded-md px-3 py-2 text-xs placeholder-slate-400 font-mono focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors">
                            @error("entryItems.{$index}.tagged_price") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Quantity to Add -->
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-semibold text-slate-900 mb-1">
                                {{ $isExisting ? '+ Add Qty' : 'Stock' }} <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   data-field="quantity_in_stock"
                                   min="1"
                                   wire:model.blur="entryItems.{{ $index }}.quantity_in_stock"
                                   placeholder="1"
                                   class="w-full bg-white border-2 border-blue-600 rounded-md px-3 py-2 text-xs text-slate-900 placeholder-slate-400 font-mono font-bold text-center focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-colors">
                            @error("entryItems.{$index}.quantity_in_stock") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Low Stock Threshold -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-semibold text-amber-700 mb-1">
                                Low Stock Threshold <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   data-field="low_stock_threshold"
                                   min="0"
                                   wire:model.blur="entryItems.{{ $index }}.low_stock_threshold"
                                   @if($isExisting) readonly disabled @endif
                                   placeholder="5"
                                   title="When inventory reaches this count, alert triggers"
                                   class="w-full {{ $isExisting ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white text-slate-900' }} border border-amber-300 rounded-md px-3 py-2 text-xs placeholder-slate-400 font-mono text-center focus:outline-none focus:border-amber-500 transition-colors">
                            @error("entryItems.{$index}.low_stock_threshold") <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Duplicate Error Banner -->
                    <div x-show="duplicateErrors[{{ $index }}]"
                         x-cloak
                         class="mt-2 text-xs font-semibold text-red-600 flex items-center gap-1.5 p-2.5 rounded bg-red-50 border border-red-200">
                        <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span x-text="duplicateErrors[{{ $index }}]"></span>
                    </div>
                    @if(isset($duplicateErrors[$index]))
                        <div x-show="!duplicateErrors[{{ $index }}]"
                             class="mt-2 text-xs font-semibold text-red-600 flex items-center gap-1.5 p-2.5 rounded bg-red-50 border border-red-200">
                            <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>{{ $duplicateErrors[$index] }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Action Footer -->
        <div class="px-5 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <button type="button"
                    wire:click="addEntryRow"
                    wire:loading.attr="disabled"
                    wire:target="addEntryRow"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-md border border-dashed border-slate-300 hover:border-blue-600 text-slate-700 hover:text-blue-700 bg-white text-xs font-semibold shadow-xs transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Add Another Item Row</span>
            </button>

            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="resetEntryForm"
                        class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 cursor-pointer transition-colors">
                    Clear Form
                </button>
                <a href="{{ route('inventory') }}"
                   class="bg-white border border-slate-300 text-slate-700 text-xs font-semibold px-4 py-2 rounded-md hover:bg-slate-50 transition-colors cursor-pointer">
                    Cancel
                </a>
                <button type="button"
                        wire:click="saveInventoryEntries"
                        wire:loading.attr="disabled"
                        wire:target="saveInventoryEntries"
                        :disabled="hasErrors || {{ $hasDuplicateOrIncomplete ? 'true' : 'false' }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-5 py-2 rounded-md shadow-xs transition-colors cursor-pointer flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none">
                    <span wire:loading.remove wire:target="saveInventoryEntries" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Save to Inventory</span>
                    </span>
                    <span wire:loading wire:target="saveInventoryEntries" class="flex items-center gap-1.5">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Saving Batch...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
