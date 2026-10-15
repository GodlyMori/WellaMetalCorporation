<div class="space-y-4">

    <!-- Toast Notification Banner -->
    @if(!empty($successMessage))
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

    <!-- Session Error Banner for Stock Requests -->
    @if (session()->has('request_error'))
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 px-4 py-2.5 rounded text-xs font-medium flex items-center justify-between">
            <span>{{ session('request_error') }}</span>
        </div>
    @endif

    <!-- Validation Error Banner (Shown when modal is closed) -->
    @if ($errors->any() && !$showAddModal)
        <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 px-4 py-3 rounded text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the errors before proceeding:</span>
            </div>
            <ul class="list-disc list-inside pl-1 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-sm font-semibold text-slate-900 dark:text-white">Product Catalog & Yard Inventory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Physical yard storage, batch intake, and stock levels</p>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. PRODUCT TABLE WORKBENCH (INTEGRATED TOOLBAR & DATA GRID)     -->
    <!-- ============================================================== -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">

        <!-- Integrated Table Workbench Bar -->
        <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Search Bar (Left) -->
            <div class="relative w-full md:w-72 lg:w-80">
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

            <!-- Filters & Stock Requests Hub Action (Right) -->
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5">
                <!-- Category Dropdown Filter -->
                <select wire:model.live="selectedCategory"
                        aria-label="Filter products by category"
                        class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors cursor-pointer">
                    <option value="ALL">Category: All ({{ $allCount }})</option>
                    @foreach($existingCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }} ({{ $categoryCounts[$cat] ?? 0 }})</option>
                    @endforeach
                    <option value="ARCHIVED">Archived ({{ $archivedCount }})</option>
                </select>

                <!-- Stock Level Filter -->
                <select wire:model.live="stockFilter"
                        aria-label="Filter products by stock status"
                        class="bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-1.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors cursor-pointer">
                    <option value="all">Stock: All</option>
                    <option value="in_stock">In stock (&gt; alert)</option>
                    <option value="low_stock">Low stock (&le; alert)</option>
                    <option value="out_of_stock">Out of stock (0)</option>
                </select>

                <!-- Stock Requests Hub Button -->
                <button type="button"
                        wire:click="openRequestsHubModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-slate-100 hover:bg-slate-200 dark:bg-[#0f1b40] dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap"
                        title="View stock requisitions hub & delivery tracking">
                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Requests Hub</span>
                    @if($pendingRequestsCount > 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white" title="{{ $pendingRequestsCount }} pending requisition(s)">{{ $pendingRequestsCount }}</span>
                    @endif
                    @if($awaitingDeliveryCount > 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white" title="{{ $awaitingDeliveryCount }} awaiting delivery">+{{ $awaitingDeliveryCount }} in transit</span>
                    @endif
                </button>
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
                        <th class="py-2.5 px-3.5 w-28 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">In stock</th>
                        <th class="py-2.5 px-3.5 w-28 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="py-2.5 px-3.5 w-44 text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858]">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/75 dark:hover:bg-[#0f1b40]/50 transition-colors">
                            <td class="py-2.5 px-3.5 font-mono text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
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
                            <td class="py-2.5 px-3.5 text-xs text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($product->status === 'archived')
                                        <button type="button" wire:click="restoreProduct({{ $product->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900 text-xs font-semibold transition-colors cursor-pointer"
                                                title="Restore to active catalog">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>Restore</span>
                                        </button>
                                    @else
                                        <!-- Stock Request Requisition Button (Replaces +Restock) -->
                                        <button type="button" wire:click="openRequestModal({{ $product->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer"
                                                title="Send stock requisition to General Manager for approval">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Request</span>
                                        </button>

                                        <!-- Edit Button -->
                                        <button type="button" wire:click="editProduct({{ $product->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-medium transition-colors cursor-pointer"
                                                title="Edit specifications and pricing">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Archive Button -->
                                        <button type="button" wire:click="archiveProduct({{ $product->id }})" wire:confirm="Archive this product from catalog?"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 text-xs font-medium transition-colors cursor-pointer"
                                                title="Archive product">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            <span>Archive</span>
                                        </button>
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
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#142259] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit product details</span>
                            <span class="text-xs font-normal text-slate-300 dark:text-slate-600">·</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-100 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                SKU: {{ $edit_sku ?: ($editingProductId ? sprintf('P%03d', $editingProductId) : 'N/A') }}
                            </span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Modify catalog item specifications and pricing</p>
                    </div>
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
                                Category <span class="text-[10px] text-slate-400 font-normal">(Read-only)</span>
                            </label>
                            <input type="text"
                                   wire:model="edit_category"
                                   readonly
                                   disabled
                                   class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-500 dark:text-slate-400 font-medium cursor-not-allowed select-none opacity-80"
                                   aria-readonly="true">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">Category cannot be changed once created.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Unit price <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 dark:text-slate-400 font-semibold text-xs">
                                    ₱
                                </div>
                                <input type="number" step="0.01" min="0" wire:model="edit_price" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded pl-7 pr-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            </div>
                            @error('edit_price') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Quantity in Stock (Strictly Read-Only as requested) & Low Stock Alert -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Quantity in stock 🔒 (Read-only)
                            </label>
                            <input type="number" wire:model="edit_stock" readonly disabled class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-500 dark:text-slate-400 font-mono cursor-not-allowed select-none opacity-80" aria-readonly="true">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">Stock cannot be edited here. It is automatically deducted upon purchase, and additions must go through Add Inventory or Stock Request.</span>
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
    <!-- MODAL: REQUEST STOCK REQUISITION (SECRETARY / STAFF -> GM)     -->
    <!-- ============================================================== -->
    @if ($showRequestModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showRequestModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#142259] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Request Stock Requisition</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $requestProductName }} (Current Stock: {{ $requestCurrentStock }} units)</p>
                    </div>
                    <button wire:click="$set('showRequestModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitStockRequest" class="p-5 space-y-4 text-xs">
                    <div class="p-3 rounded bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 text-blue-800 dark:text-blue-300 text-[11px] space-y-1">
                        <div class="font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>3-Stage Requisition Workflow:</span>
                        </div>
                        <p class="leading-relaxed text-blue-700 dark:text-blue-400">
                            Submitting sends this requisition to the General Manager. Once approved, items await physical delivery. Stock is added only when delivery arrives at the yard.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Quantity to request <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="1" wire:model="requestQuantity" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono font-bold focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('requestQuantity') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Purpose / Reason <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="requestReason" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            <option value="Stock Replenishment">Stock Replenishment (General reorder)</option>
                            <option value="Low Stock Reorder">Low Stock Reorder (Below alert threshold)</option>
                            <option value="High Customer Demand">High Customer Demand (Frequent inquiries)</option>
                            <option value="Reserved Customer Order">Reserved Customer Order (Custom purchase)</option>
                            <option value="Seasonal Production">Seasonal / Yard Batch Production</option>
                        </select>
                        @error('requestReason') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Notes / justification for General Manager
                        </label>
                        <textarea wire:model="requestNotes" rows="2" placeholder="e.g. Current stock is running critically low, 3 customer walk-ins requested this model today." class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                        @error('requestNotes') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showRequestModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Send Requisition to GM</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: STOCK REQUESTS HUB (APPROVAL & DELIVERY CONFIRMATION)   -->
    <!-- ============================================================== -->
    @if ($showRequestsHubModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-4xl bg-white dark:bg-[#0c163b] rounded-md shadow-2xl border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showRequestsHubModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded bg-[#142259]/10 dark:bg-blue-900/30 text-[#142259] dark:text-blue-300 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                Stock Requisitions &amp; Procurement Hub
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Manager approvals and delivery reception</p>
                        </div>
                    </div>
                    <button wire:click="$set('showRequestsHubModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Tabs Strip -->
                <div class="px-5 pt-3 border-b border-slate-200 dark:border-[#1a2858] flex items-center gap-4 text-xs">
                    <button type="button"
                            wire:click="$set('requestsTab', 'pending')"
                            class="pb-2.5 font-semibold transition-colors cursor-pointer border-b-2 flex items-center gap-1.5 {{ $requestsTab === 'pending' ? 'border-[#142259] text-[#142259] dark:text-white' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                        <span>Pending Approval</span>
                        @if($pendingRequestsCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-amber-500 text-white font-bold">{{ $pendingRequestsCount }}</span>
                        @endif
                    </button>

                    <button type="button"
                            wire:click="$set('requestsTab', 'awaiting_delivery')"
                            class="pb-2.5 font-semibold transition-colors cursor-pointer border-b-2 flex items-center gap-1.5 {{ $requestsTab === 'awaiting_delivery' ? 'border-[#142259] text-[#142259] dark:text-white' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                        <span>Awaiting Delivery (Yard Intake)</span>
                        @if($awaitingDeliveryCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-blue-600 text-white font-bold">{{ $awaitingDeliveryCount }}</span>
                        @endif
                    </button>

                    <button type="button"
                            wire:click="$set('requestsTab', 'history')"
                            class="pb-2.5 font-semibold transition-colors cursor-pointer border-b-2 flex items-center gap-1.5 {{ $requestsTab === 'history' ? 'border-[#142259] text-[#142259] dark:text-white' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                        <span>Requisition History (Received / Rejected)</span>
                    </button>
                </div>

                <!-- Tab Body -->
                <div class="p-5 max-h-[60vh] overflow-y-auto">
                    @if(count($hubRequests) === 0)
                        <div class="py-12 text-center text-xs text-slate-400">
                            <svg class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p>No requisitions found under this tab.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($hubRequests as $req)
                                <div class="p-3.5 rounded border border-slate-200 dark:border-[#1a2858] bg-slate-50/50 dark:bg-[#0f1b40]/50 space-y-2.5 text-xs">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $req->request_number }}</span>
                                            @php $badge = $req->status_badge; @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>

                                        <div class="text-[11px] text-slate-400">
                                            Requested on {{ $req->created_at->format('M d, Y h:i A') }} by <strong class="text-slate-700 dark:text-slate-300">{{ $req->requester?->name ?? 'Staff' }}</strong>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5 p-2.5 rounded bg-white dark:bg-[#0c163b] border border-slate-100 dark:border-slate-800">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block">Product:</span>
                                            <span class="font-semibold text-slate-900 dark:text-white">{{ $req->product?->name ?? 'Product' }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono block">{{ $req->product?->category }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 block">Quantity Requested:</span>
                                            <span class="font-mono font-bold text-[#142259] dark:text-blue-400 text-sm">{{ $req->quantity_requested }} units</span>
                                            <span class="text-[10px] text-slate-400 block">Current stock: {{ $req->product?->quantity_in_stock ?? 0 }} units</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 block">Reason:</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $req->reason }}</span>
                                            @if($req->notes)
                                                <p class="text-[11px] text-slate-400 italic mt-0.5">"{{ $req->notes }}"</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Rejection Reason if Rejected -->
                                    @if($req->status === 'rejected' && $req->rejection_reason)
                                        <div class="p-2 rounded bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-[11px]">
                                            <strong>Rejection reason:</strong> {{ $req->rejection_reason }}
                                        </div>
                                    @endif

                                    <!-- Audit Details if Received -->
                                    @if($req->status === 'received')
                                        <div class="p-2 rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 text-[11px] flex flex-wrap items-center justify-between gap-2">
                                            <div>
                                                Received by <strong>{{ $req->receiver?->name ?? 'Staff' }}</strong> on {{ $req->received_at?->format('M d, Y h:i A') }}
                                            </div>
                                            @if($req->received_notes)
                                                <span class="text-slate-500 italic">"{{ $req->received_notes }}"</span>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Actions per Status -->
                                    @if($req->status === 'pending')
                                        <div class="flex flex-wrap items-center justify-end gap-2 pt-1 border-t border-slate-100 dark:border-slate-800">
                                            @if(auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('manager'))
                                                @if($rejectingRequestId === $req->id)
                                                    <!-- Inline Rejection Form -->
                                                    <div class="w-full flex items-center gap-2 mt-1">
                                                        <input type="text"
                                                               wire:model="rejectionReasonInput"
                                                               placeholder="State reason for rejecting this requisition..."
                                                               class="flex-1 bg-white dark:bg-[#0c163b] border border-rose-300 dark:border-rose-700 rounded px-2.5 py-1 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-rose-500">
                                                        <button type="button" wire:click="rejectStockRequest({{ $req->id }})" class="px-2.5 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold cursor-pointer">Confirm Reject</button>
                                                        <button type="button" wire:click="cancelRejectForm" class="px-2 py-1 text-xs text-slate-400 hover:text-slate-600 cursor-pointer">Cancel</button>
                                                    </div>
                                                @else
                                                    <button type="button"
                                                            wire:click="openRejectForm({{ $req->id }})"
                                                            class="px-3 py-1 rounded bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold cursor-pointer transition-colors">
                                                        Reject Requisition
                                                    </button>
                                                    <button type="button"
                                                            wire:click="approveStockRequest({{ $req->id }})"
                                                            class="px-3 py-1 rounded bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Approve (Await Delivery)</span>
                                                    </button>
                                                @endif
                                            @else
                                                <div class="text-[11px] text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1.5 py-1">
                                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Requisition submitted &mdash; awaiting review and approval by General Manager</span>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($req->status === 'awaiting_delivery')
                                        <!-- Stage 3 Delivery Receipt Action -->
                                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                                            <div class="text-[11px] text-blue-700 dark:text-blue-300 flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                                <span>Approved by {{ $req->approver?->name ?? 'General Manager' }} — Supplier delivery in transit</span>
                                            </div>

                                            <button type="button"
                                                    wire:click="receiveStockDelivery({{ $req->id }})"
                                                    wire:confirm="Confirm that the physical delivery of {{ $req->quantity_requested }} unit(s) of {{ $req->product?->name }} has arrived at the yard?"
                                                    class="px-3.5 py-1.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs cursor-pointer transition-colors flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Confirm Delivery / Receive Items (+{{ $req->quantity_requested }} to stock)</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="px-5 py-3 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50 dark:bg-[#0f1b40] flex justify-end">
                    <button type="button" wire:click="$set('showRequestsHubModal', false)" class="px-4 py-1.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-300 cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: DIRECT BRANCH RESTOCK (ADMINISTRATIVE FALLBACK)         -->
    <!-- ============================================================== -->
    @if ($showRestockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showRestockModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Direct Restock Inflow
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
                            Restock Source / Supplier <span class="text-rose-500">*</span>
                        </label>
                        <select wire:model="sourceBranch" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            <option value="Main Yard Production">Main Yard Production</option>
                            <option value="Supplier Direct Delivery">Supplier Direct Delivery</option>
                            <option value="Toril Facility">Toril Facility</option>
                            <option value="Warehouse Intake">Warehouse Intake</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Restock notes / reference
                        </label>
                        <input type="text" wire:model="restockNotes" placeholder="e.g. Delivery Waybill #1042 or Fabricator batch" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showRestockModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer">Add to stock</button>
                    </div>
                </form>
            </div>
        </div>
    @endif



</div>
