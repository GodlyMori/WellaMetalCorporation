<div class="space-y-6" x-data="{ addMenuOpen: false }">

    <!-- Toast Notification Banner -->
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">✕</button>
        </div>
    @endif

    <!-- TOP CONTROLS BAR: CATEGORY PILLS + SEARCH & ADD PRODUCT (1:1 with Figma Design) -->
    <div class="flex flex-col 2xl:flex-row items-stretch 2xl:items-center justify-between gap-4">
        
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2.5 overflow-x-auto pb-1.5 scrollbar-none flex-nowrap">
            <!-- ALL -->
            <button type="button"
                    wire:click="setCategory('ALL')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $selectedCategory === 'ALL' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                ALL ({{ $allCount }})
            </button>

            <!-- SOFA -->
            <button type="button"
                    wire:click="setCategory('Sofa')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $selectedCategory === 'Sofa' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                SOFA ({{ $sofaCount }})
            </button>

            <!-- DINING TABLE -->
            <button type="button"
                    wire:click="setCategory('Dining Table')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $selectedCategory === 'Dining Table' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                DINING TABLE ({{ $diningCount }})
            </button>

            <!-- CLOSET -->
            <button type="button"
                    wire:click="setCategory('Closet')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer {{ $selectedCategory === 'Closet' ? 'bg-[#142259] text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                CLOSET ({{ $closetCount }})
            </button>

            <!-- ARCHIVED (User ADD ON) -->
            <button type="button"
                    wire:click="setCategory('ARCHIVED')"
                    class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider transition-all whitespace-nowrap cursor-pointer flex items-center gap-1.5 {{ $selectedCategory === 'ARCHIVED' ? 'bg-[#64748b] text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200/80 shadow-sm' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span>ARCHIVED ({{ $archivedCount }})</span>
            </button>
        </div>

        <!-- Right: Search Bar & Add Product Button -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search products..."
                    class="w-full bg-white border border-slate-200/90 rounded-xl pl-10 pr-4 py-2.5 text-slate-800 text-xs font-semibold placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm transition"
                />
            </div>

            <!-- + ADD PRODUCT Dropdown (1:1 with Figma Screenshot 3) -->
            <div class="relative flex-shrink-0">
                <button
                    type="button"
                    @click="addMenuOpen = !addMenuOpen"
                    class="bg-[#dc2626] hover:bg-[#b91c1c] active:bg-[#991b1b] text-white text-xs font-extrabold tracking-wider px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition cursor-pointer whitespace-nowrap flex-shrink-0"
                >
                    <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    <span>ADD PRODUCT</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="addMenuOpen"
                    @click.away="addMenuOpen = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50 divide-y divide-slate-100"
                >
                    <!-- Add Single Product -->
                    <button
                        type="button"
                        wire:click="openSingleModal"
                        @click="addMenuOpen = false"
                        class="w-full text-left p-3 rounded-xl hover:bg-slate-50 flex items-start gap-3 transition cursor-pointer"
                    >
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                            +
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 leading-tight">Add Single Product</p>
                            <p class="text-[11px] text-slate-400 font-normal mt-0.5">Fill one product at a time</p>
                        </div>
                    </button>

                    <!-- Add Multiple Products -->
                    <button
                        type="button"
                        wire:click="openMultipleModal"
                        @click="addMenuOpen = false"
                        class="w-full text-left p-3 rounded-xl hover:bg-slate-50 flex items-start gap-3 transition cursor-pointer"
                    >
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                            ⊞
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 leading-tight">Add Multiple Products</p>
                            <p class="text-[11px] text-slate-400 font-normal mt-0.5">Enter many products at once</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- MAIN PRODUCTS TABLE (1:1 with Figma Design) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <!-- Solid Blue Table Header (1:1 Figma) -->
                <thead>
                    <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                        <th class="py-4.5 px-6 font-black w-24 whitespace-nowrap">ID</th>
                        <th class="py-4.5 px-6 font-black min-w-[200px]">PRODUCT</th>
                        <th class="py-4.5 px-6 font-black w-32 whitespace-nowrap">CATEGORY</th>
                        <th class="py-4.5 px-6 font-black w-28 whitespace-nowrap">PRICE</th>
                        <th class="py-4.5 px-6 font-black w-36 whitespace-nowrap">STOCK</th>
                        <th class="py-4.5 px-6 font-black w-28 whitespace-nowrap">STATUS</th>
                        <th class="py-4.5 px-6 font-black text-right min-w-[180px] whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]">
                    @forelse($products as $product)
                        <tr class="hover:bg-blue-50/20 transition">
                            <!-- ID (e.g. P001) -->
                            <td class="py-5 px-6 font-black whitespace-nowrap">
                                <span class="bg-[#eef4ff] text-[#2563eb] text-xs font-black px-3 py-1.5 rounded-lg border border-blue-100/60 shadow-xs">
                                    {{ $product->formatted_id }}
                                </span>
                            </td>

                            <!-- Product Name & Description -->
                            <td class="py-5 px-6">
                                <p class="font-bold text-slate-800 text-[15px] leading-snug">
                                    {{ $product->name }}
                                </p>
                                <p class="text-slate-400 text-xs font-normal mt-1 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </td>

                            <!-- Category Pill -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                @if($product->category === 'Sofa')
                                    <span class="inline-block bg-[#eef4ff] text-[#3b82f6] border border-[#dbeafe] px-3.5 py-1.5 rounded-full text-xs font-bold">
                                        Sofa
                                    </span>
                                @elseif($product->category === 'Dining Table')
                                    <span class="inline-block bg-[#fff7ed] text-[#ea580c] border border-[#ffedd5] px-3.5 py-1.5 rounded-full text-xs font-bold">
                                        Dining Table
                                    </span>
                                @elseif($product->category === 'Closet')
                                    <span class="inline-block bg-[#ecfdf5] text-[#059669] border border-[#d1fae5] px-3.5 py-1.5 rounded-full text-xs font-bold">
                                        Closet
                                    </span>
                                @else
                                    <span class="inline-block bg-slate-100 text-slate-600 border border-slate-200 px-3.5 py-1.5 rounded-full text-xs font-bold">
                                        {{ $product->category }}
                                    </span>
                                @endif
                            </td>

                            <!-- Price -->
                            <td class="py-5 px-6 font-black text-slate-900 text-[15px] whitespace-nowrap">
                                ₱{{ number_format($product->tagged_price, 0) }}
                            </td>

                            <!-- Stock Count & Progress Bar -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="font-extrabold text-slate-800 w-6 text-left text-sm">
                                        {{ $product->quantity_in_stock }}
                                    </span>
                                    <div class="w-20 bg-slate-100 h-2 rounded-full overflow-hidden">
                                        @if($product->quantity_in_stock >= 5)
                                            <div class="h-full rounded-full bg-[#10b981]" style="width: {{ min(100, $product->quantity_in_stock * 8) }}%;"></div>
                                        @elseif($product->quantity_in_stock > 0)
                                            <div class="h-full rounded-full bg-[#f59e0b]" style="width: {{ min(100, $product->quantity_in_stock * 15) }}%;"></div>
                                        @else
                                            <div class="h-full rounded-full bg-[#ef4444]" style="width: 100%;"></div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-5 px-6 whitespace-nowrap">
                                @if($product->status === 'archived')
                                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Archived
                                    </span>
                                @elseif($product->quantity_in_stock <= 0)
                                    <span class="inline-flex items-center gap-1.5 bg-[#fef2f2] text-[#dc2626] text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#ef4444]"></span>
                                        Out of Stock
                                    </span>
                                @elseif($product->quantity_in_stock <= 4)
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

                            <!-- Actions Buttons (Edit, Restock, Archive) -->
                            <td class="py-5 px-6 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    @if($product->status === 'active')
                                        <!-- Edit Button -->
                                        <button
                                            type="button"
                                            wire:click="editProduct({{ $product->id }})"
                                            class="border border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50/50 text-[#3b82f6] text-xs font-bold px-3 py-1.5 rounded-xl inline-flex items-center gap-1 transition cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Restock Button -->
                                        <button
                                            type="button"
                                            wire:click="openRestockModal({{ $product->id }})"
                                            class="border border-emerald-200 hover:border-emerald-400 bg-white hover:bg-emerald-50/50 text-[#10b981] text-xs font-bold px-3 py-1.5 rounded-xl inline-flex items-center gap-1 transition cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span>Restock</span>
                                        </button>

                                        <!-- Archive Button (Instead of Delete - User Requirement) -->
                                        <button
                                            type="button"
                                            wire:click="archiveProduct({{ $product->id }})"
                                            wire:confirm="Archive product '{{ $product->name }}'? It can be viewed and restored from the ARCHIVED tab."
                                            class="border border-slate-200 hover:border-amber-400 bg-white hover:bg-amber-50/50 text-slate-500 hover:text-amber-600 text-xs font-bold px-2.5 py-1.5 rounded-xl inline-flex items-center gap-1 transition cursor-pointer"
                                            title="Archive Product"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        </button>
                                    @else
                                        <!-- Restore Button (When on Archived tab) -->
                                        <button
                                            type="button"
                                            wire:click="restoreProduct({{ $product->id }})"
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
                                No products found matching your filter.
                            </td>
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

    <!-- RESTOCK HISTORY ACCORDION (1:1 with Figma Screenshot 4) -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <button
            type="button"
            wire:click="$toggle('restockHistoryOpen')"
            class="w-full flex items-center justify-between text-left cursor-pointer"
        >
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-[#142259] text-[13px] font-black tracking-wider uppercase">
                    RESTOCK HISTORY
                </h3>
                <span class="bg-[#eef2ff] text-[#4f46e5] text-xs px-2.5 py-0.5 rounded-full font-bold">
                    {{ $restockTransfers->count() }} entries
                </span>
            </div>
            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ $restockHistoryOpen ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>

        @if($restockHistoryOpen)
            <div class="mt-5 pt-4 border-t border-slate-100 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-extrabold uppercase tracking-wider pb-2 border-b border-slate-100">
                            <th class="pb-2">DATE</th>
                            <th class="pb-2">SOURCE BRANCH</th>
                            <th class="pb-2">PRODUCT(S) & QUANTITY</th>
                            <th class="pb-2">RECEIVED BY</th>
                            <th class="pb-2">NOTES</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($restockTransfers as $transfer)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3 font-semibold text-slate-800">{{ $transfer->date_received->format('Y-m-d') }}</td>
                                <td class="py-3 font-bold text-slate-700">{{ $transfer->source_branch }}</td>
                                <td class="py-3 text-slate-600">
                                    @foreach($transfer->items as $item)
                                        <span class="inline-block mr-2 font-medium">
                                            {{ $item->product?->name ?? 'Product' }}: 
                                            <span class="text-emerald-600 font-bold">+{{ $item->quantity_received }}</span>
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 text-slate-500">{{ $transfer->receiver?->name ?? 'Admin' }}</td>
                                <td class="py-3 text-slate-400">{{ $transfer->notes ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No restock deliveries logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- MODAL: ADD SINGLE PRODUCT -->
    @if($showSingleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-[#142259] font-black text-lg">Add Single Product</h3>
                    <button wire:click="$set('showSingleModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form wire:submit="saveSingleProduct" class="mt-5 space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-slate-600 mb-1.5 uppercase font-bold">Product Name</label>
                        <input type="text" wire:model="new_name" placeholder="e.g. Modern Accent Chair" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                        @error('new_name') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Category</label>
                            <select wire:model="new_category" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none">
                                <option value="Sofa">Sofa</option>
                                <option value="Dining Table">Dining Table</option>
                                <option value="Closet">Closet</option>
                                <option value="Cabinet">Cabinet</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Tagged Price (₱)</label>
                            <input type="number" step="0.01" wire:model="new_price" placeholder="0.00" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                            @error('new_price') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Initial Stock</label>
                            <input type="number" wire:model="new_stock" placeholder="0" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                            @error('new_stock') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Material / Description</label>
                            <input type="text" wire:model="new_description" placeholder="e.g. Solid oak, grey linen" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showSingleModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white font-extrabold shadow-sm">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: ADD MULTIPLE PRODUCTS -->
    @if($showMultipleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-4xl w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-[#142259] font-black text-lg">Add Multiple Products</h3>
                        <p class="text-slate-400 text-xs mt-0.5">Quickly register multiple inventory items at once</p>
                    </div>
                    <button wire:click="$set('showMultipleModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form wire:submit="saveMultipleProducts" class="mt-5 space-y-4">
                    <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
                        @foreach($bulkProducts as $index => $bulk)
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col md:flex-row items-center gap-3 text-xs font-semibold">
                                <div class="flex-1 w-full">
                                    <label class="block text-slate-500 mb-1 text-[11px] font-bold uppercase">Product Name</label>
                                    <input type="text" wire:model="bulkProducts.{{ $index }}.name" placeholder="Name" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800" required>
                                </div>
                                <div class="w-full md:w-36">
                                    <label class="block text-slate-500 mb-1 text-[11px] font-bold uppercase">Category</label>
                                    <select wire:model="bulkProducts.{{ $index }}.category" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2 text-slate-800">
                                        <option value="Sofa">Sofa</option>
                                        <option value="Dining Table">Dining Table</option>
                                        <option value="Closet">Closet</option>
                                        <option value="Cabinet">Cabinet</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-28">
                                    <label class="block text-slate-500 mb-1 text-[11px] font-bold uppercase">Price (₱)</label>
                                    <input type="number" step="0.01" wire:model="bulkProducts.{{ $index }}.tagged_price" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800" required>
                                </div>
                                <div class="w-full md:w-24">
                                    <label class="block text-slate-500 mb-1 text-[11px] font-bold uppercase">Stock</label>
                                    <input type="number" wire:model="bulkProducts.{{ $index }}.quantity_in_stock" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800" required>
                                </div>
                                <div class="pt-5 flex items-center">
                                    <button type="button" wire:click="removeBulkRow({{ $index }})" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg">✕</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addBulkRow" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1.5 mt-2">
                        + Add another product row
                    </button>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showMultipleModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#dc2626] hover:bg-[#b91c1c] text-white font-extrabold text-xs shadow-sm">Save All Products</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: EDIT PRODUCT -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-[#142259] font-black text-lg">Edit Product</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form wire:submit="updateProduct" class="mt-5 space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-slate-600 mb-1.5 uppercase font-bold">Product Name</label>
                        <input type="text" wire:model="edit_name" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Category</label>
                            <select wire:model="edit_category" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none">
                                <option value="Sofa">Sofa</option>
                                <option value="Dining Table">Dining Table</option>
                                <option value="Closet">Closet</option>
                                <option value="Cabinet">Cabinet</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Price (₱)</label>
                            <input type="number" step="0.01" wire:model="edit_price" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Stock Count</label>
                            <input type="number" wire:model="edit_stock" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-slate-600 mb-1.5 uppercase font-bold">Description</label>
                            <input type="text" wire:model="edit_description" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showEditModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: RESTOCK PRODUCT -->
    @if($showRestockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-[#142259] font-black text-lg">Restock Product</h3>
                        <p class="text-slate-400 text-xs">{{ $restockProductName }}</p>
                    </div>
                    <button wire:click="$set('showRestockModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <form wire:submit="submitRestock" class="mt-5 space-y-4 text-xs font-semibold">
                    <div>
                        <label class="block text-slate-600 mb-1.5 uppercase font-bold">Quantity Received</label>
                        <input type="number" min="1" wire:model="restockQuantity" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-emerald-500 focus:outline-none text-base font-black" required>
                    </div>

                    <div>
                        <label class="block text-slate-600 mb-1.5 uppercase font-bold">Source Branch / Warehouse</label>
                        <input type="text" wire:model="sourceBranch" placeholder="e.g. Toril Branch" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:border-emerald-500 focus:outline-none" required>
                    </div>

                    <div>
                        <label class="block text-slate-600 mb-1.5 uppercase font-bold">Notes / Delivery Reference</label>
                        <textarea wire:model="restockNotes" rows="2" placeholder="Optional delivery notes" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showRestockModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#10b981] hover:bg-[#059669] text-white font-extrabold shadow-sm">Confirm Restock</button>
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
                            <p class="text-slate-400 text-xs">Permission required to archive inventory</p>
                        </div>
                    </div>
                    <button wire:click="$set('showAuthModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <div class="bg-amber-50/80 border border-amber-200/80 rounded-2xl p-4 mt-5 text-xs text-amber-800 leading-relaxed">
                    <p class="font-bold">⚠️ Role Policy Notice:</p>
                    <p class="mt-1">As a Sales Secretary, you must request authorization from an <strong>Administrator</strong> or <strong>Operations Manager</strong> to archive this inventory item.</p>
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
                        @error('authPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase font-bold">Reason for Archiving</label>
                        <textarea wire:model="authReason" rows="2" placeholder="e.g. Discontinued item, phasing out fabrication line" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-800 focus:border-blue-500 focus:outline-none" required></textarea>
                        @error('authReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
