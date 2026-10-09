<div class="space-y-5">

    <!-- Alert / Notification Banner -->
    @if($successMessage)
        <div class="p-3.5 rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-semibold">{{ $successMessage }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Metric Summary Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b]">
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Categories:</span>
            <div class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-1 tabular-nums">{{ $totalCount }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Catalog classification master</div>
        </div>
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Active in POS & Yard:</span>
            <div class="text-xl font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums">{{ $activeCount }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Available for new product entry</div>
        </div>
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Archived:</span>
            <div class="text-xl font-bold font-mono text-slate-500 dark:text-slate-400 mt-1 tabular-nums">{{ $archivedCount }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Retained historical categories</div>
        </div>
    </div>

    <!-- Main Container: Filter Bar & Table -->
    <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden shadow-xs">
        
        <!-- Controls Header Bar -->
        <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-[#0f1b40]/50">
            
            <!-- Left: Filter Tabs with Explicit Label -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1">Status filter:</span>
                <button type="button" wire:click="$set('statusFilter', 'all')"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $statusFilter === 'all' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    All ({{ $totalCount }})
                </button>
                <button type="button" wire:click="$set('statusFilter', 'active')"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Active ({{ $activeCount }})
                </button>
                <button type="button" wire:click="$set('statusFilter', 'archived')"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $statusFilter === 'archived' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Archived ({{ $archivedCount }})
                </button>
            </div>

            <!-- Right: Search & Create Button -->
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                <div class="relative w-full sm:w-56">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.250ms="search"
                           placeholder="Search category name..."
                           class="w-full pl-8 pr-3 py-1.5 bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                </div>

                <button type="button"
                        wire:click="openCreateModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold rounded shadow-xs transition-colors cursor-pointer whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>New Category</span>
                </button>
            </div>
        </div>

        <!-- Category Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-[#0f1b40]/80 border-b border-slate-200 dark:border-[#1a2858]">
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category Name</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Slug Identifier</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Active SKUs</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Status</th>
                        <th class="py-2.5 px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858] text-xs">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-[#0f1b40]/60 transition-colors {{ $category->trashed() ? 'opacity-60 bg-slate-50/30 dark:bg-slate-900/30' : '' }}">
                            
                            <!-- Name -->
                            <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full {{ $category->is_active && !$category->trashed() ? 'bg-emerald-500' : 'bg-slate-400' }}"></div>
                                    <span>{{ $category->name }}</span>
                                </div>
                            </td>

                            <!-- Slug -->
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $category->slug }}
                            </td>

                            <!-- Description -->
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?: '—' }}
                            </td>

                            <!-- Active SKUs Count -->
                            <td class="py-3 px-4 text-center font-mono font-semibold text-slate-800 dark:text-slate-200 tabular-nums">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $category->active_products_count }} items
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($category->trashed())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Archived
                                    </span>
                                @elseif($category->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Tactile Button Actions (Peer Review #4) -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($category->trashed())
                                        <!-- Restore Button -->
                                        <button type="button"
                                                wire:click="restoreCategory({{ $category->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900 text-xs font-semibold transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>Restore</span>
                                        </button>
                                    @else
                                        <!-- Edit Button -->
                                        <button type="button"
                                                wire:click="openEditModal({{ $category->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-medium transition-colors cursor-pointer"
                                                title="Edit category">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Toggle Active / Inactive Button -->
                                        <button type="button"
                                                wire:click="toggleActiveStatus({{ $category->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-medium transition-colors cursor-pointer"
                                                title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                            <span>{{ $category->is_active ? 'Disable' : 'Enable' }}</span>
                                        </button>

                                        <!-- Archive Button (Soft Delete exclusively) -->
                                        <button type="button"
                                                wire:click="archiveCategory({{ $category->id }})"
                                                wire:confirm="Archive category '{{ $category->name }}'? Existing products will remain preserved."
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 text-xs font-medium transition-colors cursor-pointer"
                                                title="Archive category">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            <span>Archive</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-slate-400 dark:text-slate-500">
                                No categories found matching filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-3.5 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50/50 dark:bg-[#0c163b]">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showModal', false)">
                
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $editingCategoryId ? 'Edit Category' : 'Create New Category' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Product catalog classification for yard & POS
                        </p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveCategory" class="p-5 space-y-4 text-xs">
                    
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Category Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               placeholder="e.g. Balcony Grills, Folding Tables"
                               class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('name') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Description / Material Scope
                        </label>
                        <textarea wire:model="description"
                                  rows="3"
                                  placeholder="Brief description of products belonging in this category..."
                                  class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                        @error('description') <span class="text-rose-500 text-[10px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="is_active_toggle" wire:model="is_active" class="rounded border-slate-300 text-[#142259] focus:ring-[#142259]">
                        <label for="is_active_toggle" class="text-xs text-slate-700 dark:text-slate-300 font-medium cursor-pointer">
                            Active category (visible in product creation & catalog)
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer">
                            {{ $editingCategoryId ? 'Save Changes' : 'Create Category' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
