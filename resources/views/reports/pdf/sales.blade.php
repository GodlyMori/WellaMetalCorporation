<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Sales Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            border-bottom: 2px solid #142259;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 18px;
            font-weight: 900;
            color: #142259;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            font-size: 10px;
            color: #64748b;
        }
        .cards-table {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: left;
        }
        .card-num {
            font-size: 16px;
            font-weight: bold;
            color: #142259;
        }
        .card-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-top: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #142259;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
        }
        .data-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-completed { background-color: #dcfce7; color: #15803d; }
        .badge-pending { background-color: #fef3c7; color: #b45309; }
        .badge-cancelled { background-color: #fee2e2; color: #b91c1c; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <table width="100%" style="border-bottom: 2px solid #142259; padding-bottom: 12px; margin-bottom: 15px;">
        <tr>
            <td valign="middle">
                <h1 class="title">Wella Metal Corporation</h1>
                <div class="subtitle">Official Sales & Revenue Transaction Audit Report</div>
            </td>
            <td align="right" valign="middle">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo.png'))) }}" style="height: 52px; width: auto;">
                @endif
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td><strong>Generated On:</strong> {{ $generatedAt }}</td>
            <td class="text-right"><strong>Report Period:</strong> {{ $startDate ?? 'Beginning' }} to {{ $endDate ?? 'Present' }}</td>
        </tr>
    </table>

    <table class="cards-table">
        <tr>
            <td class="card" width="25%">
                <div class="card-num">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="card-label">Total Realized Revenue</div>
            </td>
            <td class="card" width="25%">
                <div class="card-num">{{ $completedCount }}</div>
                <div class="card-label">Completed Transactions</div>
            </td>
            <td class="card" width="25%">
                <div class="card-num">₱{{ number_format($totalDiscounts ?? 0, 2) }}</div>
                <div class="card-label">Event Promo Discounts</div>
            </td>
            <td class="card" width="25%">
                <div class="card-num">₱{{ $completedCount > 0 ? number_format($totalRevenue / $completedCount, 2) : '0.00' }}</div>
                <div class="card-label">Average Order Size</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="15%">Order #</th>
                <th width="22%">Customer</th>
                <th width="23%">Product / Promo</th>
                <th width="12%" class="text-right">Discount</th>
                <th width="14%" class="text-right">Amount (PHP)</th>
                <th width="7%">Date</th>
                <th width="7%" class="text-right">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td><strong>{{ $sale->sale_number }}</strong></td>
                    <td>{{ $sale->customer_name }}</td>
                    <td>
                        {{ $sale->product_name }}
                        @if($sale->promo_name)
                            <div style="font-size: 8px; color: #6b21a8; font-weight: bold; margin-top: 1px;">🏷️ {{ $sale->promo_name }}</div>
                        @endif
                    </td>
                    <td class="text-right" style="color: #047857; font-weight: bold;">
                        {{ $sale->discount_amount > 0 ? '-₱' . number_format($sale->discount_amount, 2) : '—' }}
                    </td>
                    <td class="text-right"><strong>₱{{ number_format($sale->amount, 2) }}</strong></td>
                    <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                    <td class="text-right">
                        @if($sale->status === 'completed')
                            <span class="badge badge-completed">Completed</span>
                        @elseif($sale->status === 'layaway')
                            <span class="badge" style="background-color: #e0e7ff; color: #4338ca;">Lay-Away</span>
                        @elseif($sale->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @else
                            <span class="badge badge-cancelled">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No sales transactions found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential — Wella Metal Corporation Management Information System &copy; {{ date('Y') }}
    </div>

</body>
</html>
