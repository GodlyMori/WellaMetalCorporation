<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SalesReceiptController extends Controller
{
    /**
     * Display a standalone, print-friendly digital receipt for a sale.
     */
    public function show(Request $request, string $sale)
    {
        $saleRecord = is_numeric($sale)
            ? Sale::withTrashed()->with(['customer', 'items.product', 'payments.recordedBy', 'promotion', 'creator'])->find($sale)
            : Sale::withTrashed()->with(['customer', 'items.product', 'payments.recordedBy', 'promotion', 'creator'])->where('sale_number', $sale)->first();

        if (! $saleRecord) {
            return redirect()->route('sales')->with('error', "Sale order '{$sale}' was not found.");
        }

        $receiptItems = [];
        foreach ($saleRecord->items as $it) {
            $receiptItems[] = [
                'name' => $it->product?->name ?? 'Custom Item',
                'quantity' => (int) $it->quantity,
                'unit_price' => (float) $it->unit_price,
                'subtotal' => (float) $it->subtotal,
            ];
        }

        $tendered = (float) ($saleRecord->payments->sum('amount') ?: $saleRecord->amount_paid);

        $receipt = [
            'order_number' => $saleRecord->sale_number ?? ('ORD-'.$saleRecord->id),
            'sale_date' => $saleRecord->sale_date ? Carbon::parse($saleRecord->sale_date)->format('M d, Y h:i A') : $saleRecord->created_at->format('M d, Y h:i A'),
            'cashier' => $saleRecord->creator?->name ?? 'Staff',
            'customer_name' => $saleRecord->customer_name ?: ($saleRecord->customer?->name ?? 'Valued Customer'),
            'customer_email' => $saleRecord->customer_email ?: ($saleRecord->customer?->email ?? null),
            'customer_phone' => $saleRecord->customer_phone ?: ($saleRecord->customer?->phone ?? '—'),
            'customer_address' => $saleRecord->customer_address ?: ($saleRecord->customer?->address ?? '—'),
            'items' => $receiptItems,
            'total_amount' => (float) ($saleRecord->original_amount ?? $saleRecord->amount),
            'discount_amount' => (float) ($saleRecord->discount_amount ?? 0),
            'promo_name' => $saleRecord->promo_name,
            'net_amount' => (float) $saleRecord->amount,
            'payment_type' => $saleRecord->payment_type,
            'cash_tendered' => $tendered,
            'change_due' => 0.0,
            'downpayment_amount' => (float) $saleRecord->downpayment_amount,
            'remaining_balance' => (float) $saleRecord->remaining_balance,
            'expiry_date' => $saleRecord->layaway_expires_at ? Carbon::parse($saleRecord->layaway_expires_at)->format('M d, Y') : null,
            'notes' => $saleRecord->notes,
        ];

        return view('receipts.show', [
            'sale' => $saleRecord,
            'receipt' => $receipt,
        ]);
    }
}
