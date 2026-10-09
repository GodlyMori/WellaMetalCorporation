<div class="space-y-6">

    <!-- Toast Notifications -->
    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="p-3.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded text-emerald-800 dark:text-emerald-300 text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ $successMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold">&times;</button>
        </div>
    @endif

    @if ($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="p-3.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded text-rose-800 dark:text-rose-300 text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium">{{ $errorMessage }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-800 text-xs font-bold">&times;</button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                Lay-Away Management
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Monitor installment contracts, collect periodic payments, track reserved stock, and manage account deadlines.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('sales') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold rounded shadow-sm transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>New Lay-Away Order (POS)</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Total Receivables -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4">
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                Total Receivables
            </div>
            <div class="text-xl font-bold text-slate-900 dark:text-white mt-1 tabular-nums font-mono">
                ₱{{ number_format($totalReceivables, 2) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Outstanding balance across active contracts
            </div>
        </div>

        <!-- 2. Active Accounts -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4">
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                Active Contracts
            </div>
            <div class="text-xl font-bold text-slate-900 dark:text-white mt-1 tabular-nums font-mono">
                {{ number_format($activeCount) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Currently open installment accounts
            </div>
        </div>

        <!-- 3. Expiring Soon Alert -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4">
            <div class="text-[11px] font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Near Due (&le; 14 days)</span>
            </div>
            <div class="text-xl font-bold text-amber-700 dark:text-amber-400 mt-1 tabular-nums font-mono">
                {{ number_format($nearDueCount) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Requires customer payment follow-up
            </div>
        </div>

        <!-- 4. Settled This Month -->
        <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] p-4">
            <div class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                Settled This Month
            </div>
            <div class="text-xl font-bold text-emerald-700 dark:text-emerald-400 mt-1 tabular-nums font-mono">
                {{ number_format($settledThisMonthCount) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                Contracts paid in full this month
            </div>
        </div>
    </div>

    <!-- Main Container: Controls & Contract Listing -->
    <div class="bg-white dark:bg-[#0c163b] rounded border border-slate-200 dark:border-[#1a2858] overflow-hidden shadow-sm">

        <!-- Controls Bar -->
        <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50 dark:bg-[#0f1b40]/50">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1 md:pb-0">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 flex items-center gap-1 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Status:
                </span>
                <button type="button" wire:click="setFilter('active')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'active' ? 'bg-[#142259] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Active ({{ $activeCount }})
                </button>
                <button type="button" wire:click="setFilter('near_due')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'near_due' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Near Due ({{ $nearDueCount }})
                </button>
                <button type="button" wire:click="setFilter('overdue')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'overdue' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Overdue ({{ $overdueCount }})
                </button>
                <button type="button" wire:click="setFilter('settled')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'settled' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Fully Settled
                </button>
                <button type="button" wire:click="setFilter('cancelled')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'cancelled' ? 'bg-slate-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    Cancelled / Defaulted
                </button>
                <button type="button" wire:click="setFilter('all')"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition-colors cursor-pointer {{ $statusFilter === 'all' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/60 dark:hover:bg-[#1a2858]' }}">
                    All Records
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative w-full md:w-72">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search customer, order #, phone..."
                       class="w-full pl-9 pr-3 py-1.5 bg-white dark:bg-[#0c163b] border border-slate-300 dark:border-slate-700 rounded text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#142259] dark:focus:border-slate-500 transition-colors">
            </div>
        </div>

        <!-- Contracts Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#0f1b40] border-b border-slate-200 dark:border-[#1a2858]">
                        <th class="py-2.5 px-3.5 w-32 font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Contract #</th>
                        <th class="py-2.5 px-3.5 min-w-[180px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Customer</th>
                        <th class="py-2.5 px-3.5 min-w-[200px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Reserved Items</th>
                        <th class="py-2.5 px-3.5 w-44 font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Payment Progress</th>
                        <th class="py-2.5 px-3.5 w-28 text-right font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Remaining (₱)</th>
                        <th class="py-2.5 px-3.5 w-36 font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Deadline</th>
                        <th class="py-2.5 px-3.5 w-40 text-center font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-[11px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#1a2858]">
                    @forelse ($contracts as $contract)
                        @php
                            $totalAmount = (float)$contract->amount;
                            $paidAmount = (float)$contract->amount_paid;
                            $remaining = (float)$contract->remaining_balance;
                            $pctPaid = $totalAmount > 0 ? min(100, round(($paidAmount / $totalAmount) * 100)) : 0;
                            $expiryDate = $contract->layaway_expires_at ? \Illuminate\Support\Carbon::parse($contract->layaway_expires_at) : null;
                            $isOverdue = $expiryDate && $expiryDate->isPast() && $contract->status === 'layaway';
                            $daysLeft = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-[#0f1b40]/80 transition-colors">
                            <!-- Contract # & Date -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <div class="font-mono font-semibold text-slate-900 dark:text-white">
                                    {{ $contract->sale_number }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $contract->sale_date ? $contract->sale_date->format('M d, Y') : '—' }}
                                </div>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-3 px-3.5">
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    {{ $contract->customer_name }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $contract->customer_phone ?: 'No phone recorded' }}</span>
                                </div>
                            </td>

                            <!-- Reserved Items -->
                            <td class="py-3 px-3.5">
                                @if($contract->items->isNotEmpty())
                                    <div class="space-y-0.5">
                                        @foreach($contract->items as $it)
                                            <div class="text-xs text-slate-800 dark:text-slate-200">
                                                <span class="font-medium">{{ $it->product?->name ?? 'Custom Item' }}</span>
                                                <span class="text-slate-400 text-[11px]">&times; {{ $it->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">{{ $contract->product_name ?: 'General Purchase' }}</span>
                                @endif
                            </td>

                            <!-- Progress Bar -->
                            <td class="py-3 px-3.5">
                                <div class="flex items-center justify-between text-[11px] font-mono mb-1">
                                    <span class="text-slate-600 dark:text-slate-300 font-semibold">₱{{ number_format($paidAmount, 2) }}</span>
                                    <span class="text-slate-400">{{ $pctPaid }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded overflow-hidden">
                                    <div class="h-full {{ $pctPaid >= 100 ? 'bg-emerald-600' : 'bg-[#142259] dark:bg-blue-500' }}"
                                         style="width: {{ $pctPaid }}%;"></div>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Contract Total: ₱{{ number_format($totalAmount, 2) }}
                                </div>
                            </td>

                            <!-- Remaining Balance -->
                            <td class="py-3 px-3.5 text-right font-mono tabular-nums whitespace-nowrap">
                                @if($contract->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                                        ₱0.00 (Settled)
                                    </span>
                                @elseif($contract->status === 'cancelled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="font-bold text-slate-900 dark:text-white text-xs">
                                        ₱{{ number_format($remaining, 2) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Deadline & Countdown -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if($contract->status === 'completed')
                                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Fully Settled
                                    </span>
                                @elseif($contract->status === 'cancelled')
                                    <span class="text-[11px] text-slate-400">
                                        Contract Terminated
                                    </span>
                                @elseif($expiryDate)
                                    <div class="text-xs font-medium text-slate-900 dark:text-white">
                                        {{ $expiryDate->format('M d, Y') }}
                                    </div>
                                    @if($isOverdue)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900 mt-0.5">
                                            Overdue by {{ abs((int)$daysLeft) }}d
                                        </span>
                                    @elseif($daysLeft !== null && $daysLeft <= 14)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900 mt-0.5">
                                            {{ (int)$daysLeft }} day{{ (int)$daysLeft === 1 ? '' : 's' }} left
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">
                                            {{ (int)$daysLeft }} days left
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">No deadline set</span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($contract->status === 'layaway')
                                        <!-- Record Payment Action -->
                                        <button type="button"
                                                wire:click="openPaymentModal({{ $contract->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#142259] hover:bg-[#0e1840] text-white text-[11px] font-semibold rounded transition-colors cursor-pointer"
                                                title="Record Installment Payment">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v2m0-10c-1.11 0-2.08.402-2.599 1M12 18c1.657 0 3-.895 3-2s-1.343-2-3-2" />
                                            </svg>
                                            <span>Pay</span>
                                        </button>

                                        <!-- Extend Deadline Action -->
                                        <button type="button"
                                                wire:click="openExtendModal({{ $contract->id }})"
                                                class="p-1 text-slate-500 hover:text-slate-800 dark:hover:text-white rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0c163b] transition-colors cursor-pointer"
                                                title="Extend Expiry Deadline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>

                                        <!-- Cancel & Restock Action -->
                                        <button type="button"
                                                wire:click="openCancelModal({{ $contract->id }})"
                                                class="p-1 text-rose-500 hover:text-rose-700 dark:hover:text-rose-300 rounded border border-rose-200 dark:border-rose-900 bg-rose-50/50 dark:bg-rose-950/20 transition-colors cursor-pointer"
                                                title="Forfeit / Restock Reserved Items">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @else
                                        <!-- View Payment History / Details -->
                                        <button type="button"
                                                wire:click="openPaymentModal({{ $contract->id }})"
                                                class="px-2 py-1 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded border border-slate-200 dark:border-slate-700 text-[11px] font-medium transition-colors cursor-pointer">
                                            View Ledger
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">No lay-away contracts found</div>
                                    <div class="text-[11px] text-slate-400">Create lay-away orders from the Sales & POS screen.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="p-3.5 border-t border-slate-200 dark:border-[#1a2858]">
                {{ $contracts->links() }}
            </div>
        @endif

    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: RECORD INSTALLMENT PAYMENT                            -->
    <!-- ============================================================== -->
    @if ($showPaymentModal && $payingSale)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-[#0c163b] rounded shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden"
                 wire:click.outside="$set('showPaymentModal', false)">

                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Installment Ledger &amp; Payment
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-mono">
                            {{ $payingSale->sale_number }} &middot; {{ $payingSale->customer_name }}
                        </p>
                    </div>
                    <button type="button" wire:click="$set('showPaymentModal', false)"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Financial Balance Strip -->
                <div class="bg-slate-50 dark:bg-[#0f1b40] p-4 border-b border-slate-200 dark:border-[#1a2858] grid grid-cols-3 gap-2 text-center text-xs">
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Total Order</div>
                        <div class="font-bold text-slate-900 dark:text-white font-mono mt-0.5">₱{{ number_format($payingSale->amount, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Amount Paid</div>
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">₱{{ number_format($payingSale->amount_paid, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-semibold">Remaining Due</div>
                        <div class="font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">₱{{ number_format($payingSale->remaining_balance, 2) }}</div>
                    </div>
                </div>

                <!-- Past Payment History -->
                <div class="p-4 border-b border-slate-200 dark:border-[#1a2858] max-h-40 overflow-y-auto">
                    <div class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Payment Timeline ({{ count($paymentHistory) }} records)
                    </div>
                    @if(count($paymentHistory) > 0)
                        <div class="space-y-1.5">
                            @foreach($paymentHistory as $hist)
                                <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 dark:border-slate-800">
                                    <div>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">₱{{ number_format($hist['amount'], 2) }}</span>
                                        <span class="text-[11px] text-slate-400 ml-1.5">via {{ $hist['method'] }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $hist['date'] }} &middot; By {{ $hist['recorded_by'] }}</span>
                                    </div>
                                    @if($hist['reference'] !== '—')
                                        <span class="text-[10px] font-mono text-slate-500 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded">
                                            Ref: {{ $hist['reference'] }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-slate-400 text-center py-2">No previous installment payments recorded.</div>
                    @endif
                </div>

                @if($payingSale->status === 'layaway' && $payingSale->remaining_balance > 0)
                    <!-- New Payment Form -->
                    <form wire:submit.prevent="submitLayawayPayment" class="p-5 space-y-3.5 text-xs">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                    Installment payment amount (₱) <span class="text-rose-500">*</span>
                                </label>
                                <button type="button"
                                        wire:click="$set('paymentAmount', '{{ $payingSale->remaining_balance }}')"
                                        class="text-[10px] font-semibold text-[#142259] dark:text-blue-400 hover:underline cursor-pointer">
                                    Pay full balance (₱{{ number_format($payingSale->remaining_balance, 2) }})
                                </button>
                            </div>
                            <input type="number" step="0.01" min="0.01" max="{{ $payingSale->remaining_balance }}"
                                   wire:model="paymentAmount"
                                   placeholder="0.00"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                            @error('paymentAmount') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Payment method <span class="text-rose-500">*</span>
                                </label>
                                <select wire:model="payment_method"
                                        class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Reference / Txn #
                                </label>
                                <input type="text"
                                       wire:model="payment_reference"
                                       placeholder="e.g. GCash Ref #, Check #"
                                       class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Cashier notes / remarks
                            </label>
                            <input type="text"
                                   wire:model="payment_notes"
                                   placeholder="e.g. 2nd Installment payment"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                        </div>

                        <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                            <button type="button" wire:click="$set('showPaymentModal', false)"
                                    class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 transition-colors cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                                Confirm &amp; Print Receipt
                            </button>
                        </div>
                    </form>
                @else
                    <div class="p-4 bg-slate-50 dark:bg-[#0f1b40] text-center text-xs text-slate-500">
                        This contract is {{ $payingSale->status === 'completed' ? 'already fully settled' : 'terminated' }}. No further payments can be accepted.
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 2: EXTEND DEADLINE                                       -->
    <!-- ============================================================== -->
    @if ($showExtendModal && $extendingSale)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden"
                 wire:click.outside="$set('showExtendModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                            Extend Lay-Away Deadline
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $extendingSale->sale_number }} &middot; {{ $extendingSale->customer_name }}</p>
                    </div>
                    <button type="button" wire:click="$set('showExtendModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitExtension" class="p-5 space-y-3.5 text-xs">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded text-amber-800 dark:text-amber-300 text-xs">
                        Current Deadline: <span class="font-bold">{{ $extendingSale->layaway_expires_at ? $extendingSale->layaway_expires_at->format('M d, Y') : 'None' }}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Days to extend from current expiry / today <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" min="1" max="180" wire:model="extensionDays"
                                   class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs font-mono font-bold text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                            <button type="button" wire:click="$set('extensionDays', 30)" class="px-2.5 py-2 border rounded text-xs font-medium hover:bg-slate-50 cursor-pointer">+30d</button>
                            <button type="button" wire:click="$set('extensionDays', 60)" class="px-2.5 py-2 border rounded text-xs font-medium hover:bg-slate-50 cursor-pointer">+60d</button>
                        </div>
                        @error('extensionDays') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Reason for extension (Audit log)
                        </label>
                        <input type="text" wire:model="extensionNotes" placeholder="e.g. Customer requested 30-day grace period"
                               class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-[#142259] transition-colors">
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showExtendModal', false)"
                                class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                            Save Extension
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 3: CANCEL & RESTOCK STOCK                                -->
    <!-- ============================================================== -->
    @if ($showCancelModal && $cancellingSale)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#0c163b] rounded shadow-lg border border-slate-200 dark:border-[#1a2858] overflow-hidden"
                 wire:click.outside="$set('showCancelModal', false)">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-[#1a2858] flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-rose-600 dark:text-rose-400">
                            Forfeit &amp; Restock Reserved Stock
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $cancellingSale->sale_number }} &middot; {{ $cancellingSale->customer_name }}</p>
                    </div>
                    <button type="button" wire:click="$set('showCancelModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitCancellation" class="p-5 space-y-3.5 text-xs">
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 rounded text-rose-800 dark:text-rose-300 text-xs space-y-1">
                        <div class="font-bold">Warning: Reserved furniture items will be returned to yard inventory.</div>
                        <div class="text-[11px] opacity-90">All physical stock will be incremented with an official audit log. Retained customer payments (₱{{ number_format($cancellingSale->amount_paid, 2) }}) will be preserved in records.</div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Forfeiture reason <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model="cancelReason" class="w-full bg-white dark:bg-[#0f1b40] border border-slate-300 dark:border-slate-700 rounded px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-rose-500 transition-colors">
                        @error('cancelReason') <span class="text-rose-500 text-[10px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-[#1a2858] flex justify-end gap-2">
                        <button type="button" wire:click="$set('showCancelModal', false)"
                                class="bg-white dark:bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold px-4 py-2 rounded hover:bg-slate-50 transition-colors cursor-pointer">
                            Keep Contract Active
                        </button>
                        <button type="submit"
                                class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2 rounded transition-colors cursor-pointer">
                            Confirm Cancellation &amp; Restock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 4: OFFICIAL INSTALLMENT PAYMENT RECEIPT                  -->
    <!-- ============================================================== -->
    @if ($showReceiptModal && $receiptData)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-md shadow-2xl overflow-hidden border border-slate-200"
                 wire:click.outside="closeReceiptModal">

                <!-- Printable Receipt Canvas -->
                <div id="layawayPrintableReceipt" class="p-6 text-slate-900 font-sans text-xs bg-white space-y-4">
                    <!-- Receipt Header -->
                    <div class="text-center pb-3 border-b border-dashed border-slate-300 space-y-1">
                        <h2 class="text-sm font-bold tracking-tight uppercase">Wella Metal Corporation</h2>
                        <p class="text-[11px] text-slate-500">Official Installment Acknowledgment Receipt</p>
                        <p class="text-[10px] font-mono text-slate-400">Order: {{ $receiptData['order_number'] }} &middot; {{ $receiptData['payment_date'] }}</p>
                    </div>

                    <!-- Customer Information -->
                    <div class="text-[11px] bg-slate-50 p-2.5 rounded space-y-0.5">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Customer:</span>
                            <span class="font-semibold">{{ $receiptData['customer_name'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Phone:</span>
                            <span class="font-mono">{{ $receiptData['customer_phone'] ?: '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Cashier:</span>
                            <span>{{ $receiptData['cashier_name'] }}</span>
                        </div>
                    </div>

                    <!-- Reserved Item Details -->
                    <div class="space-y-1 border-b border-dashed border-slate-300 pb-3">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase">Reserved Products</div>
                        @foreach($receiptData['items'] as $item)
                            <div class="flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-medium">{{ $item['name'] }}</span>
                                    <span class="text-[10px] text-slate-400 block">&times; {{ $item['quantity'] }} @ ₱{{ number_format($item['unit_price'], 2) }}</span>
                                </div>
                                <span class="font-mono font-semibold">₱{{ number_format($item['subtotal'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Payment & Ledger Figures -->
                    <div class="space-y-1.5 text-xs pt-1 border-b border-dashed border-slate-300 pb-3">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Contract Total:</span>
                            <span class="font-mono font-semibold">₱{{ number_format($receiptData['total_order_amount'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-700 font-bold">
                            <span>Amount Received Now:</span>
                            <span class="font-mono">₱{{ number_format($receiptData['payment_amount'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] text-slate-500">
                            <span>Payment Method:</span>
                            <span>{{ $receiptData['payment_method'] }}</span>
                        </div>
                        @if($receiptData['payment_reference'] !== '—')
                            <div class="flex justify-between text-[11px] text-slate-500">
                                <span>Reference #:</span>
                                <span class="font-mono">{{ $receiptData['payment_reference'] }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-1 font-bold {{ $receiptData['is_fully_paid'] ? 'text-emerald-700' : 'text-rose-600' }}">
                            <span>{{ $receiptData['is_fully_paid'] ? 'Settlement Status:' : 'Remaining Balance:' }}</span>
                            <span class="font-mono">{{ $receiptData['is_fully_paid'] ? 'PAID IN FULL' : '₱' . number_format($receiptData['remaining_balance'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Footer Notice -->
                    <div class="text-center text-[10px] text-slate-400 pt-1">
                        <p>Thank you for your payment!</p>
                        <p class="mt-0.5">Please keep this official receipt for claiming your reserved furniture upon full settlement.</p>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between gap-2">
                    <button type="button" wire:click="closeReceiptModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer">
                        Done / Close
                    </button>
                    <button type="button" onclick="window.print()"
                            class="px-4 py-2 bg-[#142259] hover:bg-[#0e1840] text-white text-xs font-semibold rounded flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Print Receipt</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
