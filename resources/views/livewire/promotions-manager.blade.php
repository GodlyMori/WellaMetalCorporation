<div class="space-y-7">
    
    <!-- Success / Error Toast Banners -->
    @if($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="bg-emerald-500 text-white px-5 py-3.5 rounded-2xl shadow-lg flex items-center justify-between text-sm font-bold transition-all">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $successMessage }}</span>
            </div>
            <button @click="show = false" class="text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    @if($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="bg-red-500 text-white px-5 py-3.5 rounded-2xl shadow-lg flex items-center justify-between text-sm font-bold transition-all">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ $errorMessage }}</span>
            </div>
            <button @click="show = false" class="text-white/80 hover:text-white">✕</button>
        </div>
    @endif

    <!-- TOP 3 METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Active Promos -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-emerald-500 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Live</span>
            </div>
            <div>
                <h3 class="text-[#142259] text-[34px] font-black tracking-tight leading-none">{{ $totalActive }}</h3>
                <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase mt-2">ACTIVE PROMOTIONS</p>
                <p class="text-[#9aa8c7] text-xs font-medium mt-0.5">Available for sales checkout</p>
            </div>
        </div>

        <!-- Card 2: Pending Admin Approvals -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-amber-500 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if($totalPending > 0)
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full animate-pulse">Needs Review</span>
                @else
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">Clear</span>
                @endif
            </div>
            <div>
                <h3 class="text-[#142259] text-[34px] font-black tracking-tight leading-none">{{ $totalPending }}</h3>
                <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase mt-2">PENDING APPROVAL</p>
                <p class="text-[#9aa8c7] text-xs font-medium mt-0.5">Manager submissions awaiting Admin</p>
            </div>
        </div>

        <!-- Card 3: Total Promos -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 border-t-4 border-t-[#142259] flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#142259] shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-[#142259] bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">All-Time</span>
            </div>
            <div>
                <h3 class="text-[#142259] text-[34px] font-black tracking-tight leading-none">{{ $totalPromos }}</h3>
                <p class="text-[#8a9bbd] text-xs font-extrabold tracking-wider uppercase mt-2">TOTAL CAMPAIGNS</p>
                <p class="text-[#9aa8c7] text-xs font-medium mt-0.5">Seasonal & event specials</p>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="bg-white rounded-3xl p-7 shadow-sm border border-slate-100 space-y-6 w-full min-w-0">
        
        <!-- HEADER & ACTIONS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <h2 class="text-[#142259] text-[18px] font-black tracking-wider uppercase">EVENT SALES PROMOTIONS</h2>
                <p class="text-slate-400 text-xs font-medium mt-0.5">
                    Configure seasonal discounts (Valentine's, Kadayawan). Managers propose; Admins approve & activate.
                </p>
            </div>

            <!-- Create Promo Button (Admin / Manager only) -->
            @if($isAdmin || $isManager)
                <button type="button" wire:click="openCreateModal"
                        class="bg-[#142259] hover:bg-blue-900 text-white font-black text-xs px-5 py-3 rounded-2xl shadow-md transition-all flex items-center gap-2 cursor-pointer flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Create Event Promo</span>
                </button>
            @endif
        </div>

        <!-- SEARCH & FILTER TABS -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none flex-nowrap">
                <button type="button" wire:click="setStatusFilter('ALL')"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {{ $statusFilter === 'ALL' ? 'bg-[#142259] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Promos
                </button>
                <button type="button" wire:click="setStatusFilter('active')"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {{ $statusFilter === 'active' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Active ({{ $totalActive }})
                </button>
                <button type="button" wire:click="setStatusFilter('pending_approval')"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {{ $statusFilter === 'pending_approval' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Pending Approval ({{ $totalPending }})
                </button>
                <button type="button" wire:click="setStatusFilter('inactive')"
                        class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap {{ $statusFilter === 'inactive' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Inactive / Expired
                </button>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full sm:w-72 flex-shrink-0">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search campaign name or code..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-2 text-xs font-medium focus:outline-none focus:border-[#142259] focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- SPACIOUS PROMOTIONS TABLE -->
        <div class="overflow-x-auto rounded-2xl border border-slate-100 w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#1558bf] text-white text-[11px] font-black tracking-wider uppercase">
                        <th class="py-4 px-7">PROMO CAMPAIGN</th>
                        <th class="py-4 px-7">CATEGORY SCOPE</th>
                        <th class="py-4 px-7">DISCOUNT RATE</th>
                        <th class="py-4 px-7">DATE RANGE</th>
                        <th class="py-4 px-7">STATUS</th>
                        <th class="py-4 px-7">APPROVAL & AUDIT</th>
                        <th class="py-4 px-7 text-right">ADMIN ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($promotions as $promo)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            
                            <!-- Promo Campaign & Code -->
                            <td class="py-5 px-7">
                                <div class="font-extrabold text-[#142259] text-[13px]">
                                    {{ $promo->name }}
                                </div>
                                @if($promo->code)
                                    <span class="inline-block mt-1 font-mono text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded border border-indigo-200">
                                        {{ $promo->code }}
                                    </span>
                                @endif
                                @if($promo->description)
                                    <div class="text-slate-400 text-[11px] mt-0.5 truncate max-w-xs">{{ $promo->description }}</div>
                                @endif
                            </td>

                            <!-- Category Scope -->
                            <td class="py-5 px-7">
                                @if($promo->applicable_category === 'ALL')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span>🌐 All Categories</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span>🎯 {{ $promo->applicable_category }} Only</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Discount Rate -->
                            <td class="py-5 px-7 font-black text-slate-800">
                                @if($promo->discount_type === 'percentage')
                                    <span class="text-emerald-700 text-sm font-black">{{ (float)$promo->discount_value }}% OFF</span>
                                @else
                                    <span class="text-emerald-700 text-sm font-black">₱{{ number_format($promo->discount_value, 2) }} OFF</span>
                                @endif
                                @if($promo->min_order_amount > 0)
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Min. Order: ₱{{ number_format($promo->min_order_amount, 2) }}</div>
                                @endif
                            </td>

                            <!-- Date Range -->
                            <td class="py-5 px-7">
                                <div class="font-bold text-slate-700">
                                    {{ $promo->starts_at->format('M d, Y') }} — {{ $promo->ends_at->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] font-semibold mt-0.5">
                                    @if(now()->toDateString() > $promo->ends_at->toDateString())
                                        <span class="text-red-500 font-bold">Expired</span>
                                    @elseif(now()->toDateString() < $promo->starts_at->toDateString())
                                        <span class="text-blue-500 font-bold">Upcoming</span>
                                    @else
                                        <span class="text-emerald-600 font-bold">Currently in effect</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-5 px-7">
                                @if($promo->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                        ACTIVE
                                    </span>
                                @elseif($promo->status === 'pending_approval')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        PENDING APPROVAL
                                    </span>
                                @elseif($promo->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-red-100 text-red-800">
                                        REJECTED
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-slate-100 text-slate-600">
                                        INACTIVE
                                    </span>
                                @endif
                            </td>

                            <!-- Approval & Audit Trail -->
                            <td class="py-5 px-7 text-[11px] text-slate-600">
                                <div><span class="font-semibold text-slate-400">Created:</span> {{ $promo->creator?->name ?? 'System' }}</div>
                                @if($promo->approved_by)
                                    <div class="mt-0.5 text-emerald-700">
                                        <span class="font-semibold text-slate-400">Approved:</span> {{ $promo->approver?->name }}
                                    </div>
                                @else
                                    <div class="mt-0.5 text-amber-600 font-bold">
                                        Awaiting Admin Review
                                    </div>
                                @endif
                            </td>

                            <!-- Admin Action Controls -->
                            <td class="py-5 px-7 text-right">
                                @if($isAdmin)
                                    @if($promo->status === 'pending_approval')
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="approvePromotion({{ $promo->id }})"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] px-3 py-1.5 rounded-xl shadow-sm transition cursor-pointer">
                                                Approve & Activate
                                            </button>
                                            <button type="button" wire:click="rejectPromotion({{ $promo->id }})"
                                                    class="bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-[11px] px-2.5 py-1.5 rounded-xl transition cursor-pointer">
                                                Reject
                                            </button>
                                        </div>
                                    @elseif($promo->status === 'active')
                                        <button type="button" wire:click="toggleStatus({{ $promo->id }})"
                                                class="bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-700 font-extrabold text-[11px] px-3 py-1.5 rounded-xl transition cursor-pointer border border-slate-200">
                                            Deactivate
                                        </button>
                                    @else
                                        <button type="button" wire:click="toggleStatus({{ $promo->id }})"
                                                class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-[11px] px-3 py-1.5 rounded-xl transition cursor-pointer border border-emerald-200">
                                            Activate
                                        </button>
                                    @endif
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Admin Action Required</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-bold">
                                No event promotions found matching this criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $promotions->links() }}
        </div>
    </div>

    <!-- CREATE EVENT PROMO MODAL -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
            <div @click.away="$wire.showCreateModal = false"
                 class="bg-white rounded-3xl max-w-2xl w-full p-8 shadow-2xl border border-slate-100 relative">
                
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-[#142259] font-black text-xl">Create Event Promotion</h3>
                        <p class="text-slate-400 text-xs font-semibold mt-0.5">
                            Set up event discounts like Kadayawan Festival or Valentine's Day Special.
                        </p>
                    </div>
                    <button type="button" wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <!-- Role Governance Notice -->
                <div class="mt-5 p-3.5 rounded-2xl text-xs {{ $isAdmin ? 'bg-blue-50 text-blue-900 border border-blue-100' : 'bg-amber-50 text-amber-900 border border-amber-200' }}">
                    @if($isAdmin)
                        <strong>⚡ Admin Access:</strong> Creating this promo will activate it immediately for secretaries to apply.
                    @else
                        <strong>🛡️ Manager Submission:</strong> Promos created by Managers will be submitted as <strong>Pending Approval</strong> and must be reviewed & activated by an Admin before secretaries can apply it.
                    @endif
                </div>

                <form wire:submit="savePromotion" class="space-y-4 mt-5">
                    
                    <!-- Campaign Name & Code -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campaign Event Name *</label>
                            <input type="text" wire:model="name" placeholder="e.g. Kadayawan Festival Promo, Valentine's Day Sale"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            @error('name') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Promo Code (Optional)</label>
                            <input type="text" wire:model="code" placeholder="e.g. KADAYAWAN26"
                                   class="w-full uppercase font-mono bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            @error('code') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Category Scope & Minimum Spend -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category Restriction *</label>
                            <select wire:model="applicable_category"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                                <option value="ALL">🌐 All Categories (Entire Sale)</option>
                                <option value="Sofa">🛋️ Sofa Only</option>
                                <option value="Dining Table">🍽️ Dining Table Only</option>
                                <option value="Closet">🚪 Closet Only</option>
                            </select>
                            <span class="text-[10px] text-slate-400">Discount will only apply to items matching this category.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Min. Eligible Spend (₱)</label>
                            <input type="number" step="0.01" wire:model="min_order_amount" placeholder="0.00"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            <span class="text-[10px] text-slate-400">Leave at 0.00 for no minimum cart threshold.</span>
                            @error('min_order_amount') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Discount Type & Value -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Discount Type *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer {{ $discount_type === 'percentage' ? 'border-[#142259] bg-blue-50 text-[#142259] font-bold' : 'border-slate-200 text-slate-600' }}">
                                    <input type="radio" value="percentage" wire:model.live="discount_type" class="hidden">
                                    <span>Percentage (%)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer {{ $discount_type === 'fixed' ? 'border-[#142259] bg-blue-50 text-[#142259] font-bold' : 'border-slate-200 text-slate-600' }}">
                                    <input type="radio" value="fixed" wire:model.live="discount_type" class="hidden">
                                    <span>Fixed Cash (₱)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Discount Value {{ $discount_type === 'percentage' ? '(%)' : '(PHP ₱)' }} *
                            </label>
                            <input type="number" step="0.01" wire:model="discount_value" placeholder="{{ $discount_type === 'percentage' ? 'e.g. 10 for 10%' : 'e.g. 1000' }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-black text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            @error('discount_value') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Start & End Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Start Date *</label>
                            <input type="date" wire:model="starts_at"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            @error('starts_at') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">End Date *</label>
                            <input type="date" wire:model="ends_at"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none">
                            @error('ends_at') <span class="text-red-500 text-[11px] font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Description / Campaign Notes</label>
                        <textarea wire:model="description" rows="2" placeholder="Terms and conditions or promo mechanics..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 focus:bg-white focus:border-[#142259] focus:outline-none"></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-[#142259] hover:bg-blue-900 text-white font-black text-xs shadow-md transition cursor-pointer">
                            {{ $isAdmin ? 'Create & Activate' : 'Submit for Admin Approval' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
