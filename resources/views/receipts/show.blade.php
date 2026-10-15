@section('title', 'Official Digital Receipt - ' . ($receipt['order_number'] ?? 'Receipt'))

<x-app-layout>
    <div class="max-w-2xl mx-auto py-6 px-4">
        <!-- Top Navigation / Action Bar -->
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('sales') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Sales / POS</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Receipt</span>
                </button>
            </div>
        </div>

        <!-- Receipt Card Container -->
        <div class="bg-white rounded-md shadow-sm border border-slate-200 overflow-hidden print:shadow-none print:border-none">
            <!-- Header -->
            <div class="p-6 text-center border-b border-dashed border-slate-300 bg-slate-50">
                <h1 class="text-base font-bold tracking-wider text-slate-900 uppercase">
                    Wella Metal Corporation
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Furniture & Metal Fabrications</p>
                <p class="text-[11px] font-mono text-slate-400 mt-1 uppercase">Official Digital Receipt • Store Pickup</p>
            </div>

            <!-- Receipt Metadata & Customer -->
            <div class="p-5 space-y-3 text-xs border-b border-dashed border-slate-300">
                <div class="flex justify-between">
                    <span class="text-slate-400 uppercase tracking-wider text-[11px]">Order number:</span>
                    <span class="font-mono font-bold text-slate-900 text-xs">{{ $receipt['order_number'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 uppercase tracking-wider text-[11px]">Date / time:</span>
                    <span class="text-slate-700 font-mono">{{ $receipt['sale_date'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 uppercase tracking-wider text-[11px]">Cashier / Staff:</span>
                    <span class="text-slate-700">{{ $receipt['cashier'] }}</span>
                </div>
                <div class="pt-2 border-t border-slate-200 space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Customer:</span>
                        <span class="font-semibold text-slate-900">{{ $receipt['customer_name'] }}</span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-slate-400">Contact:</span>
                        <span class="text-slate-600 font-mono">{{ $receipt['customer_phone'] }}</span>
                    </div>
                    @if(!empty($receipt['customer_email']))
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-400">Email:</span>
                            <span class="text-slate-600 font-mono">{{ $receipt['customer_email'] }}</span>
                        </div>
                    @endif
                    @if(!empty($receipt['customer_address']))
                        <div class="flex justify-between text-[11px]">
                            <span class="text-slate-400">Address:</span>
                            <span class="text-slate-600 text-right">{{ $receipt['customer_address'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Line Items Breakdown -->
            <div class="p-5 space-y-2 text-xs border-b border-dashed border-slate-300">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 pb-1 border-b border-slate-200 flex justify-between">
                    <span>Item description</span>
                    <span>Subtotal</span>
                </div>
                @foreach($receipt['items'] as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <div class="font-semibold text-slate-900">{{ $item['name'] }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $item['quantity'] }} × ₱{{ number_format($item['unit_price'], 2) }}</div>
                        </div>
                        <div class="font-mono font-bold text-slate-900 text-right">
                            ₱{{ number_format($item['subtotal'], 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Financial Breakdown -->
            <div class="p-5 space-y-2 text-xs border-b border-dashed border-slate-300 bg-slate-50">
                <div class="flex justify-between text-slate-500">
                    <span>Items subtotal:</span>
                    <span class="font-mono text-slate-800">₱{{ number_format($receipt['total_amount'], 2) }}</span>
                </div>

                @if(!empty($receipt['discount_amount']) && $receipt['discount_amount'] > 0)
                    <div class="flex justify-between text-amber-700">
                        <span>Promo discount ({{ $receipt['promo_name'] ?? 'Applied' }}):</span>
                        <span class="font-mono font-semibold">-₱{{ number_format($receipt['discount_amount'], 2) }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-sm font-bold text-slate-900 pt-1.5 border-t border-slate-200">
                    <span>Total payable:</span>
                    <span class="font-mono text-base text-[#142259]">₱{{ number_format($receipt['net_amount'], 2) }}</span>
                </div>

                <div class="pt-2 border-t border-slate-200 space-y-1">
                    <div class="flex justify-between text-slate-600 text-[11px]">
                        <span>Settlement method:</span>
                        <span class="font-semibold text-slate-900">
                            {{ $receipt['payment_type'] === 'layaway' ? 'Lay-away reservation' : 'Full cash settlement' }}
                        </span>
                    </div>

                    @if($receipt['payment_type'] === 'full')
                        <div class="flex justify-between text-slate-600">
                            <span>Cash tendered:</span>
                            <span class="font-mono font-bold text-slate-900">₱{{ number_format($receipt['cash_tendered'], 2) }}</span>
                        </div>
                    @else
                        <div class="flex justify-between text-amber-700 font-semibold">
                            <span>Downpayment paid:</span>
                            <span class="font-mono">₱{{ number_format($receipt['downpayment_amount'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-rose-600 font-bold">
                            <span>Remaining balance:</span>
                            <span class="font-mono">₱{{ number_format($receipt['remaining_balance'], 2) }}</span>
                        </div>
                        @if(!empty($receipt['expiry_date']))
                            <div class="flex justify-between text-slate-500 text-[11px]">
                                <span>Reserved until:</span>
                                <span class="font-mono text-amber-600">{{ $receipt['expiry_date'] }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 py-4 text-center bg-white text-[11px] text-slate-400">
                Goods strictly for store pickup • Thank you for choosing Wella Metal Corporation!
            </div>
        </div>
    </div>
</x-app-layout>
