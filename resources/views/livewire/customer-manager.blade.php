<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-sm font-semibold text-slate-900 dark:text-white">Customer Directory</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Normalized client registry and purchase ledger</p>
        </div>
        <div>
            <button wire:click="openAddModal" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-3.5 py-2 rounded transition-colors cursor-pointer inline-flex items-center gap-1.5 shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add Customer</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($successMessage || session()->has('success'))
        <div class="rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-3.5 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="font-semibold">{{ $successMessage ?: session('success') }}</span>
            </div>
            <button type="button" wire:click="$set('successMessage', '')" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Metric Summary Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b]">
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Registered Clients:</span>
            <div class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-1 tabular-nums">{{ number_format($totalCustomers) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Client master database</div>
        </div>
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Active Buyers:</span>
            <div class="text-xl font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums">{{ number_format($activeCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Active retail & corporate buyers</div>
        </div>
        <div class="p-4 sm:p-5">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Customer Revenue:</span>
            <div class="text-xl font-bold font-mono text-[#142259] dark:text-white mt-1 tabular-nums">₱{{ number_format($totalCustomerRevenue, 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Lifetime settled order volume</div>
        </div>
    </div>

    <!-- Integrated Customer Table Workbench -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden shadow-xs">
        
        <!-- Integrated Workbench Toolbar -->
        <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-slate-50/50 dark:bg-[#0f1b40]/50">
            
            <!-- Left: Filter Status with Explicit Label (Peer Review #9) -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1">Status filter:</span>
                <button type="button" wire:click="$set('statusFilter', 'all')"
                        class="px-2.5 py-1 rounded text-xs font-semibold transition-colors cursor-pointer {{ $statusFilter === 'all' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    All ({{ $totalCustomers }})
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

            <!-- Right: Search Bar -->
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search name, customer ID, phone, address..."
                       class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
            </div>
        </div>

        <!-- Customers Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858] text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-2.5 px-3.5 w-24 cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('customer_number')">
                            Customer ID
                        </th>
                        <th class="py-2.5 px-3.5 min-w-[200px] cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('name')">
                            Customer Name (3NF)
                        </th>
                        <th class="py-2.5 px-3.5 w-32">Phone</th>
                        <th class="py-2.5 px-3.5 min-w-[180px]">Address</th>
                        <th class="py-2.5 px-3.5 w-24 text-right cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_orders_count')">
                            Orders
                        </th>
                        <th class="py-2.5 px-3.5 w-32 text-right cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_spent')">
                            Total Spent
                        </th>
                        <th class="py-2.5 px-3.5 w-36 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858] text-slate-700 dark:text-slate-200">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors {{ $customer->trashed() ? 'opacity-60 bg-slate-50/40 dark:bg-slate-900/40' : '' }}">
                            <td class="py-2.5 px-3.5 font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $customer->customer_number ?? 'CUST-' . $customer->id }}
                            </td>
                            <td class="py-2.5 px-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded bg-[#142259]/10 dark:bg-slate-800 text-[#142259] dark:text-slate-300 text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ $customer->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900 dark:text-white truncate">
                                            {{ $customer->name }}
                                        </div>
                                        @if($customer->email)
                                            <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate">{{ $customer->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-600 dark:text-slate-400 font-mono text-xs whitespace-nowrap">
                                {{ $customer->phone ?: '—' }}
                            </td>
                            <td class="py-2.5 px-3.5 text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $customer->address }}">
                                {{ $customer->address ?: '—' }}
                            </td>
                            <td class="py-2.5 px-3.5 text-right font-mono tabular-nums text-slate-700 dark:text-slate-300">
                                {{ $customer->total_orders_count }}
                            </td>
                            <td class="py-2.5 px-3.5 text-right font-mono font-semibold text-slate-900 dark:text-white tabular-nums whitespace-nowrap">
                                ₱{{ number_format($customer->total_spent, 2) }}
                            </td>
                            <!-- Tactile Button Actions (Peer Review #4 & #7) -->
                            <td class="py-2.5 px-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    @if($customer->trashed())
                                        <!-- Restore Button -->
                                        <button wire:click="restoreCustomer({{ $customer->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 text-xs font-semibold transition-colors cursor-pointer"
                                                title="Restore customer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>Restore</span>
                                        </button>
                                    @else
                                        <!-- History Button -->
                                        <button wire:click="viewHistory({{ $customer->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-medium shadow-xs transition-colors cursor-pointer"
                                                title="View purchase history">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>History</span>
                                        </button>

                                        <!-- Edit Button -->
                                        <button wire:click="openEditModal({{ $customer->id }})"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-medium transition-colors cursor-pointer"
                                                title="Edit details">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Archive Button (Soft Delete exclusively) -->
                                        <button wire:click="archiveCustomer({{ $customer->id }})"
                                                wire:confirm="Archive customer record for '{{ $customer->name }}'?"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 text-xs font-medium transition-colors cursor-pointer"
                                                title="Archive customer">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                            <span>Archive</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-xs text-slate-400 dark:text-slate-500">
                                No customers registered or matching search filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-3.5 border-t border-slate-200 dark:border-[#1a2858] bg-slate-50/50 dark:bg-[#0c163b]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Add / Edit Customer with 3NF First & Last Name -->
    @if ($showCustomerModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showCustomerModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $editingCustomerId ? 'Edit customer' : 'Add customer' }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">3NF Normalized customer registration</p>
                    </div>
                    <button wire:click="$set('showCustomerModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveCustomer" class="p-5 space-y-4 text-xs">
                    
                    <!-- 3NF Atomic Names: First Name & Last Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                First name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="first_name"
                                   placeholder="e.g. Maria"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('first_name') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Last name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   wire:model="last_name"
                                   placeholder="e.g. Santos"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('last_name') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Phone number
                            </label>
                            <input type="text"
                                   wire:model="phone"
                                   placeholder="0917-000-0000"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('phone') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Email address
                            </label>
                            <input type="email"
                                   wire:model="email"
                                   placeholder="customer@domain.com"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('email') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Delivery address / Location
                        </label>
                        <input type="text"
                               wire:model="address"
                               placeholder="Street, Barangay, City, Province"
                               class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('address') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Internal notes
                        </label>
                        <textarea wire:model="notes"
                                  rows="2"
                                  placeholder="Fabrication requirements, preferred payment terms..."
                                  class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                        @error('notes') <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showCustomerModal', false)" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded shadow-xs transition-colors cursor-pointer">
                            {{ $editingCustomerId ? 'Update customer' : 'Save customer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Purchase History Modal -->
    @if ($showHistoryModal && $selectedCustomer)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-3xl bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="closeHistoryModal">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Purchase ledger: {{ $selectedCustomer->name }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-mono">
                            {{ $selectedCustomer->customer_number }} • Total Orders: {{ $selectedCustomer->sales->count() }} • Total Spent: ₱{{ number_format($selectedCustomer->sales->sum('amount'), 2) }}
                        </p>
                    </div>
                    <button wire:click="closeHistoryModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 max-h-[60vh] overflow-y-auto divide-y divide-slate-100 dark:divide-[#1a2858]">
                    @forelse($selectedCustomer->sales as $sale)
                        <div class="py-3 first:pt-0 last:pb-0 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $sale->sale_number ?? 'ORD-' . $sale->id }}
                                </div>
                                <div class="font-mono font-bold text-[#142259] dark:text-white">
                                    ₱{{ number_format($sale->amount, 2) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">
                                <div>{{ $sale->sale_date ? \Illuminate\Support\Carbon::parse($sale->sale_date)->format('M d, Y') : '—' }} • {{ ucfirst($sale->payment_type) }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $sale->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($sale->status === 'layaway' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                    <button type="button"
                                            wire:click="openReceipt({{ $sale->id }})"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-[#142259] text-white hover:bg-[#0e1840] text-[10px] font-semibold transition-colors cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>View Receipt</span>
                                    </button>
                                </div>
                            </div>
                            @if($sale->items && $sale->items->count() > 0)
                                <div class="mt-2 pl-3 border-l-2 border-slate-200 dark:border-slate-700 space-y-0.5 text-[11px] text-slate-600 dark:text-slate-300">
                                    @foreach($sale->items as $item)
                                        <div class="flex justify-between">
                                            <span>{{ $item->product?->name ?? 'Custom Item' }} &times; {{ $item->quantity }}</span>
                                            <span class="font-mono">₱{{ number_format($item->subtotal, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            No purchases recorded for this customer yet.
                        </div>
                    @endforelse
                </div>

                <div class="px-5 py-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end bg-slate-50/50 dark:bg-[#0f1b40]/50">
                    <button wire:click="closeHistoryModal" class="bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- DIGITAL SALES RECEIPT MODAL (VIEW FROM HISTORY)                -->
    <!-- ============================================================== -->
    @if(!empty($showReceiptModal) && !empty($receiptData))
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" @click.stop>

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
                        @if(!empty($receiptData['customer_email']))
                            <div class="flex justify-between text-[11px]">
                                <span class="text-slate-400">Email:</span>
                                <span class="text-slate-600 dark:text-slate-400 font-mono">{{ $receiptData['customer_email'] }}</span>
                            </div>
                        @endif
                        @if(!empty($receiptData['customer_address']))
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
                                @if(!empty($item['is_negotiated']) && ($item['srp'] ?? 0) > $item['unit_price'])
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        <span class="line-through">SRP ₱{{ number_format($item['srp'], 2) }}</span>
                                        <span class="text-amber-700 dark:text-amber-400 font-medium"> → Agreed ₱{{ number_format($item['unit_price'], 2) }}</span>
                                        <span>({{ $item['quantity'] }} × ₱{{ number_format($item['unit_price'], 2) }})</span>
                                    </div>
                                @else
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $item['quantity'] }} × ₱{{ number_format($item['unit_price'], 2) }}</div>
                                @endif
                            </div>
                            <div class="font-mono font-bold text-slate-900 dark:text-white text-right">
                                ₱{{ number_format($item['subtotal'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial Breakdown -->
                <div class="p-5 space-y-2 text-xs border-b border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#0f1b40]">
                    @if(!empty($receiptData['has_negotiated_items']) && ($receiptData['negotiated_concession_total'] ?? 0) > 0)
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Gross SRP total:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($receiptData['gross_srp_total'] ?? $receiptData['total_amount'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-amber-700 dark:text-amber-400">
                            <span>Price adjustment (negotiated):</span>
                            <span class="font-mono font-semibold">-₱{{ number_format($receiptData['negotiated_concession_total'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 dark:text-slate-300">
                            <span>Agreed subtotal:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($receiptData['total_amount'], 2) }}</span>
                        </div>
                    @else
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Items subtotal:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200">₱{{ number_format($receiptData['total_amount'], 2) }}</span>
                        </div>
                    @endif

                    @if(!empty($receiptData['promo_discount_amount']) && $receiptData['promo_discount_amount'] > 0)
                        <div class="flex justify-between text-emerald-700 dark:text-emerald-400">
                            <span>Promo discount ({{ $receiptData['promo_name'] ?? 'Applied' }}):</span>
                            <span class="font-mono font-semibold">-₱{{ number_format($receiptData['promo_discount_amount'], 2) }}</span>
                        </div>
                    @elseif(!empty($receiptData['discount_amount']) && $receiptData['discount_amount'] > 0 && empty($receiptData['has_negotiated_items']))
                        <div class="flex justify-between text-amber-700 dark:text-amber-400">
                            <span>Promo discount ({{ $receiptData['promo_name'] ?? 'Applied' }}):</span>
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
                        @else
                            <div class="flex justify-between text-amber-700 dark:text-amber-400 font-semibold">
                                <span>Downpayment paid:</span>
                                <span class="font-mono">₱{{ number_format($receiptData['downpayment_amount'], 2) }}</span>
                            </div>
                            <div class="flex justify-between text-rose-600 dark:text-rose-400 font-bold">
                                <span>Remaining balance:</span>
                                <span class="font-mono">₱{{ number_format($receiptData['remaining_balance'], 2) }}</span>
                            </div>
                            @if(!empty($receiptData['expiry_date']))
                                <div class="flex justify-between text-slate-500 text-[11px]">
                                    <span>Reserved until:</span>
                                    <span class="font-mono text-amber-600 dark:text-amber-400">{{ $receiptData['expiry_date'] }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>


                <!-- Receipt Footer & Dismissal -->
                <div class="px-5 py-4 border-t border-slate-200 dark:border-[#1a2858] flex items-center justify-between gap-2 bg-white dark:bg-[#0c163b]">
                    <a href="{{ route('sales.receipt', $receiptData['order_number'] ?? 'preview') }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Open printable tab</span>
                    </a>
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
