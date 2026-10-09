<div class="space-y-6">
    
    <!-- Success / Error Feedback Banners -->
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-3.5 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 p-1 cursor-pointer" aria-label="Close message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="rounded bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-300 p-3.5 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 p-1 cursor-pointer" aria-label="Close message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Main Operational Container -->
    <div class="space-y-4">
        <!-- Header & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-[#1a2858]">
            <h1 class="text-sm font-semibold text-slate-900 dark:text-white">Event sales promotions</h1>
            @if($isAdmin || $isManager)
                <button type="button" wire:click="openCreateModal" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer inline-flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Create event promo</span>
                </button>
            @endif
        </div>

        <!-- Integrated Promotions Table Workbench -->
        <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
            <!-- Filter Tab Strip & Search Header -->
            <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="inline-flex items-center gap-3 overflow-x-auto pb-0.5">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 whitespace-nowrap">Campaign filter:</span>
                    <button type="button" wire:click="setStatusFilter('ALL')" class="text-xs font-semibold pb-1 cursor-pointer whitespace-nowrap {{ $statusFilter === 'ALL' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">All campaigns ({{ $totalPromos }})</button>
                    <button type="button" wire:click="setStatusFilter('active')" class="text-xs font-semibold pb-1 cursor-pointer whitespace-nowrap {{ $statusFilter === 'active' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Active ({{ $totalActive }})</button>
                    <button type="button" wire:click="setStatusFilter('pending_approval')" class="text-xs font-semibold pb-1 cursor-pointer whitespace-nowrap {{ $statusFilter === 'pending_approval' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Pending approval ({{ $totalPending }})</button>
                    <button type="button" wire:click="setStatusFilter('inactive')" class="text-xs font-semibold pb-1 cursor-pointer whitespace-nowrap {{ $statusFilter === 'inactive' ? 'border-b-2 border-[#142259] text-[#142259] dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">Inactive</button>
                </div>
                <div class="relative w-full sm:w-64 flex-shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search campaign or code..." class="w-full bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-1.5 pl-8 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                </div>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                        <th class="py-3 px-4 min-w-[200px] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Promo Campaign</th>
                        <th class="py-3 px-4 w-36 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category Scope</th>
                        <th class="py-3 px-4 w-36 whitespace-nowrap text-right text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Discount Rate</th>
                        <th class="py-3 px-4 w-44 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Validity Period</th>
                        <th class="py-3 px-4 w-32 whitespace-nowrap text-center text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="py-3 px-4 w-48 whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Audit Trail</th>
                        <th class="py-3 px-4 text-right min-w-[130px] whitespace-nowrap text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @forelse($promotions as $promo)
                        <tr class="border-b border-slate-100 dark:border-[#1a2858] hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                            
                            <!-- Promo Campaign & Code -->
                            <td class="py-2.5 px-4">
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    {{ $promo->name }}
                                </div>
                                @if($promo->code)
                                    <span class="inline-block mt-1 font-mono tabular-nums text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">
                                        {{ $promo->code }}
                                    </span>
                                @endif
                                @if($promo->description)
                                    <div class="text-slate-400 dark:text-slate-500 text-[11px] mt-0.5 truncate max-w-xs">{{ $promo->description }}</div>
                                @endif
                            </td>

                            <!-- Category Scope -->
                            <td class="py-2.5 px-4">
                                @if($promo->applicable_category === 'ALL')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        All categories
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $promo->applicable_category }}
                                    </span>
                                @endif
                            </td>

                            <!-- Discount Rate -->
                            <td class="py-2.5 px-4 text-right">
                                @if($promo->discount_type === 'percentage')
                                    <span class="text-slate-900 dark:text-white font-semibold font-mono tabular-nums">{{ (float)$promo->discount_value }}% off</span>
                                @else
                                    <span class="text-slate-900 dark:text-white font-semibold font-mono tabular-nums">₱{{ number_format($promo->discount_value, 2) }} off</span>
                                @endif
                                @if($promo->min_order_amount > 0)
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 font-normal font-mono tabular-nums mt-0.5">Min: ₱{{ number_format($promo->min_order_amount, 2) }}</div>
                                @endif
                            </td>

                            <!-- Date Range -->
                            <td class="py-2.5 px-4">
                                <div class="font-medium text-slate-700 dark:text-slate-300 font-mono tabular-nums">
                                    {{ $promo->starts_at->format('M d, Y') }} — {{ $promo->ends_at->format('M d, Y') }}
                                </div>
                                <div class="text-[11px] mt-0.5">
                                    @if(now()->toDateString() > $promo->ends_at->toDateString())
                                        <span class="text-rose-600 dark:text-rose-400 font-medium">Expired</span>
                                    @elseif(now()->toDateString() < $promo->starts_at->toDateString())
                                        <span class="text-slate-500 dark:text-slate-400 font-medium">Upcoming</span>
                                    @else
                                        <span class="text-emerald-700 dark:text-emerald-400 font-medium">Currently active</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-2.5 px-4 text-center">
                                @if($promo->status === 'active')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900">
                                        Active
                                    </span>
                                @elseif($promo->status === 'pending_approval')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900">
                                        Pending
                                    </span>
                                @elseif($promo->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900">
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- Approval & Audit Trail -->
                            <td class="py-2.5 px-4 text-slate-600 dark:text-slate-300">
                                <div><span class="text-slate-400 dark:text-slate-500">By:</span> {{ $promo->creator?->name ?? 'System' }}</div>
                                @if($promo->approved_by)
                                    <div class="mt-0.5 text-slate-700 dark:text-slate-300">
                                        <span class="text-slate-400 dark:text-slate-500">Approved:</span> {{ $promo->approver?->name }}
                                    </div>
                                @else
                                    <div class="mt-0.5 text-amber-700 dark:text-amber-400 font-medium">
                                        Awaiting review
                                    </div>
                                @endif
                            </td>

                            <!-- Admin Action Controls (Tactile Buttons - Peer Review #4 & #7) -->
                            <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                @if($isAdmin)
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if($promo->status === 'pending_approval')
                                            <button type="button" wire:click="approvePromotion({{ $promo->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 text-xs font-semibold cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Approve</span>
                                            </button>
                                            <button type="button" wire:click="rejectPromotion({{ $promo->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 text-xs font-medium cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Reject</span>
                                            </button>
                                        @elseif($promo->status === 'active')
                                            <button type="button" wire:click="toggleStatus({{ $promo->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-medium cursor-pointer">
                                                <span>Deactivate</span>
                                            </button>
                                        @else
                                            <button type="button" wire:click="toggleStatus({{ $promo->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-medium cursor-pointer">
                                                <span>Activate</span>
                                            </button>
                                        @endif

                                        <button type="button" wire:click="archivePromotion({{ $promo->id }})"
                                                wire:confirm="Archive promotion '{{ $promo->name }}'?"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 text-xs font-medium cursor-pointer"
                                                title="Archive promo">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            <span>Archive</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">Admin only</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                No event promotions found matching this criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($promotions->hasPages())
                <div class="p-3 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50/50 dark:bg-[#0c163b]">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- CREATE EVENT PROMO MODAL -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div @click.away="$wire.showCreateModal = false"
                 class="bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] w-full max-w-xl relative">
                
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Create event promotion</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Set up event discounts like Kadayawan Festival or Valentine's Day Special.
                        </p>
                    </div>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer" aria-label="Close modal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="savePromotion">
                    
                    <div class="p-5 space-y-4">
                        <!-- Role Governance Notice -->
                        @if($isAdmin)
                            <div class="p-3 rounded text-xs bg-slate-50 dark:bg-[#0f1b40] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#1a2858]">
                                <strong>Admin access:</strong> Creating this promo will activate it immediately for checkout.
                            </div>
                        @else
                            <div class="p-3 rounded text-xs bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900">
                                <strong>Manager submission:</strong> Promos created by Managers are submitted as <strong>Pending approval</strong> for Admin review before activation.
                            </div>
                        @endif

                        <!-- Campaign Name & Code -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Campaign event name *</label>
                                <input type="text" wire:model="name" placeholder="e.g. Kadayawan Festival Promo"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                @error('name') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Promo code (optional)</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" wire:model="code" placeholder="e.g. WM-SUMMER"
                                           class="w-full uppercase font-mono bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-2.5 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    <button type="button" wire:click="generatePromoCode"
                                            class="inline-flex items-center gap-1 px-2.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer"
                                            title="Auto generate random voucher code">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Auto</span>
                                    </button>
                                </div>
                                @error('code') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Category Scope & Minimum Spend -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Category restriction *</label>
                                <select wire:model="applicable_category"
                                        class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                    <option value="ALL">All Categories (Entire Cart)</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }} Only</option>
                                    @endforeach
                                </select>
                                <span class="block text-[11px] text-slate-400 dark:text-slate-500 mt-1">Discount will only apply to items matching this category.</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Min. eligible spend (₱)</label>
                                <input type="number" step="0.01" wire:model="min_order_amount" placeholder="0.00"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors font-mono tabular-nums">
                                <span class="block text-[11px] text-slate-400 dark:text-slate-500 mt-1">Leave at 0.00 for no minimum cart threshold.</span>
                                @error('min_order_amount') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Discount Type & Value -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Discount type *</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center justify-center gap-1.5 rounded border px-3 py-1.5 text-xs font-medium cursor-pointer transition-colors {{ $discount_type === 'percentage' ? 'border-[#142259] bg-[#142259]/5 dark:bg-[#142259]/20 text-[#142259] dark:text-white font-semibold' : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-[#0f1b40] hover:bg-slate-50 dark:hover:bg-[#142259]/10' }}">
                                        <input type="radio" value="percentage" wire:model.live="discount_type" class="hidden">
                                        <span>Percentage (%)</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-1.5 rounded border px-3 py-1.5 text-xs font-medium cursor-pointer transition-colors {{ $discount_type === 'fixed' ? 'border-[#142259] bg-[#142259]/5 dark:bg-[#142259]/20 text-[#142259] dark:text-white font-semibold' : 'border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-white dark:bg-[#0f1b40] hover:bg-slate-50 dark:hover:bg-[#142259]/10' }}">
                                        <input type="radio" value="fixed" wire:model.live="discount_type" class="hidden">
                                        <span>Fixed cash (₱)</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                    Discount value {{ $discount_type === 'percentage' ? '(%)' : '(PHP ₱)' }} *
                                </label>
                                <input type="number" step="0.01" wire:model="discount_value" placeholder="{{ $discount_type === 'percentage' ? 'e.g. 10 for 10%' : 'e.g. 1000' }}"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs font-mono tabular-nums text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                @error('discount_value') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Start & End Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Start date *</label>
                                <input type="date" wire:model="starts_at"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                @error('starts_at') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">End date *</label>
                                <input type="date" wire:model="ends_at"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                                @error('ends_at') <span class="block text-rose-600 dark:text-rose-400 text-[11px] font-medium mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Description / campaign notes</label>
                            <textarea wire:model="description" rows="2" placeholder="Terms and conditions or promo mechanics..."
                                      class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-[#1a2858] flex items-center justify-end gap-2">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                            {{ $isAdmin ? 'Create & activate' : 'Submit for admin approval' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
