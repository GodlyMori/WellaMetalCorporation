<div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <h1 class="text-sm font-semibold text-slate-900 dark:text-white">Customer directory</h1>
        <div>
            <button wire:click="openAddModal" class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add customer</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="rounded bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900 text-emerald-800 dark:text-emerald-300 p-3.5 text-xs flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Integrated Customer Table Workbench -->
    <div class="border border-slate-200 dark:border-[#1a2858] rounded bg-white dark:bg-[#0c163b] overflow-hidden">
        
        <!-- Integrated Workbench Toolbar -->
        <div class="p-3.5 border-b border-slate-200 dark:border-[#1a2858] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by customer name, ID, phone, address..."
                       class="w-full pl-9 pr-3 py-1.5 bg-slate-50 dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
            </div>

            <!-- Operational Metric Highlights in Workbench Bar -->
            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                <span><strong class="text-slate-900 dark:text-white">{{ number_format($totalCustomers) }}</strong> clients</span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span><strong class="text-slate-900 dark:text-white">{{ number_format($activeCustomers) }}</strong> active buyers</span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span>Volume: <strong class="font-mono text-slate-900 dark:text-white">₱{{ number_format($totalCustomerRevenue, 2) }}</strong></span>
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
                        <th class="py-2.5 px-3.5 min-w-[180px] cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('name')">
                            Customer Name
                        </th>
                        <th class="py-2.5 px-3.5 w-32">Phone</th>
                        <th class="py-2.5 px-3.5 min-w-[180px]">Address</th>
                        <th class="py-2.5 px-3.5 w-24 text-right cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_orders_count')">
                            Orders
                        </th>
                        <th class="py-2.5 px-3.5 w-32 text-right cursor-pointer hover:text-slate-900 dark:hover:text-white transition-colors" wire:click="sortBy('total_spent')">
                            Total Spent
                        </th>
                        <th class="py-2.5 px-3.5 w-28 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858] text-slate-700 dark:text-slate-200">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-50 dark:hover:bg-[#0f1b40] transition-colors">
                            <td class="py-2.5 px-3.5 font-mono text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $customer->customer_number ?? 'CUST-' . $customer->id }}
                            </td>
                            <td class="py-2.5 px-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ $customer->initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-900 dark:text-white truncate">{{ $customer->name }}</div>
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
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button wire:click="viewHistory({{ $customer->id }})" class="text-xs font-semibold text-[#142259] dark:text-slate-300 hover:text-[#0e1840] dark:hover:text-white transition-colors cursor-pointer">History</button>
                                    <button wire:click="openEditModal({{ $customer->id }})" class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                No customers registered or matching search filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-3 border-t border-slate-200 dark:border-[#1a2858]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Add / Edit Customer -->
    @if ($showCustomerModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="$set('showCustomerModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ $editingCustomerId ? 'Edit customer' : 'Add customer' }}
                    </h3>
                    <button wire:click="$set('showCustomerModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveCustomer" class="p-5 space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Customer name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="name"
                               placeholder="e.g. John Doe / Ace Construction Corp"
                               class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('name') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Phone number
                            </label>
                            <input type="text"
                                   wire:model="phone"
                                   placeholder="0917-000-0000"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('phone') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Email address
                            </label>
                            <input type="email"
                                   wire:model="email"
                                   placeholder="client@example.com"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                            @error('email') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Address / location
                        </label>
                        <input type="text"
                               wire:model="address"
                               placeholder="e.g. Quezon City, Metro Manila"
                               class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
                        @error('address') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Internal notes
                        </label>
                        <textarea wire:model="notes"
                                  rows="3"
                                  placeholder="Corporate discounts, credit standing, or specific customer preferences..."
                                  class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors"></textarea>
                        @error('notes') <span class="text-xs text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="px-5 py-4 border-t border-slate-200 dark:border-[#1a2858] flex items-center justify-end gap-2 -mx-5 -mb-5 mt-5">
                        <button type="button"
                                wire:click="$set('showCustomerModal', false)"
                                class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                            {{ $editingCustomerId ? 'Update customer' : 'Save customer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal: Customer Purchase History -->
    @if ($showHistoryModal && $selectedCustomer)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-[#0c163b] rounded-md shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden" wire:click.outside="closeHistoryModal">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Purchase history — {{ $selectedCustomer->name }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">ID: {{ $selectedCustomer->customer_number }} &bull; Phone: {{ $selectedCustomer->phone ?: '—' }} &bull; Address: {{ $selectedCustomer->address ?: '—' }}</p>
                    </div>
                    <button wire:click="closeHistoryModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-5 max-h-[65vh] overflow-y-auto space-y-3">
                    @forelse ($selectedCustomer->sales->sortByDesc('created_at') as $sale)
                        <div class="border border-slate-200 dark:border-[#1a2858] rounded p-3 bg-slate-50 dark:bg-[#0f1b40] space-y-2">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 dark:border-[#1a2858] pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-semibold text-xs text-slate-900 dark:text-white">{{ $sale->sale_number }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">&bull; {{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if ($sale->payment_type === 'layaway')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900">
                                            Lay-away
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900">
                                            Full paid
                                        </span>
                                    @endif

                                    @if($sale->is_archived)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                                            Archived
                                        </span>
                                    @elseif($sale->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900">
                                            Completed
                                        </span>
                                    @elseif($sale->status === 'layaway')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900">
                                            Lay-away
                                        </span>
                                    @elseif($sale->status === 'cancelled')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-900">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Items in this sale -->
                            <div class="space-y-1.5">
                                <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Ordered items</div>
                                @if($sale->items && $sale->items->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($sale->items as $item)
                                            <div class="flex justify-between items-center bg-white dark:bg-[#0c163b] p-2 rounded border border-slate-200 dark:border-[#1a2858]">
                                                <span class="truncate max-w-[180px] text-xs font-medium text-slate-700 dark:text-slate-300">{{ $item->product->name ?? 'Product' }}</span>
                                                <span class="font-mono text-xs text-slate-500 dark:text-slate-400 tabular-nums">x{{ $item->quantity }} &bull; ₱{{ number_format($item->subtotal, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $sale->product_name }}</div>
                                @endif
                            </div>

                            <!-- Financial summary -->
                            <div class="flex flex-wrap items-center justify-between pt-2 border-t border-slate-200 dark:border-[#1a2858] text-xs">
                                <div class="space-x-3 text-slate-500 dark:text-slate-400">
                                    <span>Total: <strong class="text-slate-900 dark:text-white font-mono tabular-nums">₱{{ number_format($sale->amount, 2) }}</strong></span>
                                    @if ($sale->payment_type === 'layaway')
                                        <span>Paid: <strong class="text-emerald-700 dark:text-emerald-400 font-mono tabular-nums">₱{{ number_format($sale->amount_paid, 2) }}</strong></span>
                                        <span>Balance: <strong class="text-rose-600 dark:text-rose-400 font-mono tabular-nums">₱{{ number_format($sale->remaining_balance, 2) }}</strong></span>
                                    @endif
                                </div>
                                @if ($sale->promotion)
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Promo applied: {{ $sale->promotion->name }} (-₱{{ number_format($sale->discount_amount, 2) }})</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                            No purchases recorded for this customer yet.
                        </div>
                    @endforelse
                </div>

                <div class="px-5 py-4 border-t border-slate-200 dark:border-[#1a2858] flex justify-end">
                    <button wire:click="closeHistoryModal" class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
