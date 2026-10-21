<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Sales Report</title>
    <style>
        @page {
            margin: 24pt 28pt 30pt 28pt;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 8.5pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        /* Clean Header */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 6pt;
            margin-bottom: 12pt;
        }
        .company-name {
            font-size: 14pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin: 0;
        }
        .report-title {
            font-size: 9.5pt;
            font-weight: 600;
            color: #475569;
            margin-top: 2pt;
        }
        .meta-info {
            font-size: 7.5pt;
            color: #64748b;
            text-align: right;
            line-height: 1.4;
        }

        /* KPI Cards */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14pt;
            background-color: #f8fafc;
            border: 1pt solid #cbd5e1;
        }
        .kpi-cell {
            padding: 8pt 10pt;
            border-right: 1pt solid #e2e8f0;
            vertical-align: top;
        }
        .kpi-cell:last-child {
            border-right: none;
        }
        .kpi-label {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3pt;
            margin-bottom: 2pt;
        }
        .kpi-value {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-sub {
            font-size: 7pt;
            color: #64748b;
            margin-top: 1pt;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 14pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            padding: 6pt 6pt;
            border-top: 1pt solid #cbd5e1;
            border-bottom: 1.5pt solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 5.5pt 6pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fbfcfd;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; }

        /* Minimal Status Pills */
        .status-pill {
            display: inline-block;
            padding: 1.5pt 5pt;
            border-radius: 2pt;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-layaway   { background-color: #e0f2fe; color: #075985; }
        .status-pending   { background-color: #fef3c7; color: #92400e; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }

        /* Minimal Sign-off */
        .sign-table {
            width: 100%;
            margin-top: 18pt;
            border-top: 1pt solid #e2e8f0;
            padding-top: 12pt;
        }
        .sign-box {
            width: 45%;
            vertical-align: top;
        }
        .sign-line {
            border-bottom: 1pt solid #0f172a;
            height: 20pt;
            margin-bottom: 3pt;
        }
        .sign-title {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }

        /* Footer */
        .footer {
            margin-top: 12pt;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 4pt;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="company-name">Wella Metal Corporation</div>
                <div class="report-title">Sales & Orders Report</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div><strong>Period:</strong> {{ $startDate ? date('M d, Y', strtotime($startDate)) : 'All Time' }} – {{ $endDate ? date('M d, Y', strtotime($endDate)) : 'Present' }}</div>
                <div><strong>Status Filter:</strong> {{ strtoupper($statusFilter ?? 'ALL') }}</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
            </td>
        </tr>
    </table>

    <!-- 4 Key Metrics Strip -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-cell" style="width: 28%;">
                <div class="kpi-label">Total Realized Revenue</div>
                <div class="kpi-value">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="kpi-sub">Actual payments collected</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Completed Orders</div>
                <div class="kpi-value">{{ $completedCount }}</div>
                <div class="kpi-sub">Fully settled transactions</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Active Layaways</div>
                <div class="kpi-value">{{ $layawayCount ?? 0 }}</div>
                <div class="kpi-sub">Installment accounts open</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Average Order Value</div>
                <div class="kpi-value">₱{{ number_format($averageOrderValue ?? 0, 2) }}</div>
                <div class="kpi-sub">Per revenue transaction</div>
            </td>
        </tr>
    </table>

    <!-- Clean Data Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 14%;">Order #</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 22%;">Customer</th>
                <th style="width: 22%;">Item Description</th>
                <th style="width: 15%;" class="text-right">Total (₱)</th>
                <th style="width: 15%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td class="mono font-bold">{{ $sale->sale_number }}</td>
                    <td class="mono text-center">{{ $sale->sale_date ? $sale->sale_date->format('Y-m-d') : '-' }}</td>
                    <td>
                        <strong>{{ $sale->customer_name }}</strong>
                        @if($sale->customer_phone)
                            <br><span style="font-size: 7pt; color: #64748b;">{{ $sale->customer_phone }}</span>
                        @endif
                    </td>
                    <td>
                        @if($sale->items && $sale->items->isNotEmpty())
                            @foreach($sale->items as $it)
                                <div>{{ $it->product?->name ?? 'Item' }} <span style="color: #64748b;">&times; {{ $it->quantity }}</span></div>
                            @endforeach
                        @else
                            {{ $sale->product_name ?: 'General Order' }}
                        @endif
                    </td>
                    <td class="text-right mono font-bold">
                        ₱{{ number_format($sale->amount, 2) }}
                        @if($sale->discount_amount > 0)
                            @php
                                $isPromo = !empty($sale->promo_name);
                                $adjustmentLabel = $isPromo ? 'Promo Discount' : 'Negotiated Amount';
                            @endphp
                            <div style="font-size: 6.5pt; color: #64748b; font-weight: normal;">
                                ({{ $adjustmentLabel }}: -₱{{ number_format($sale->discount_amount, 2) }})
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($sale->status === 'completed')
                            <span class="status-pill status-completed">Completed</span>
                            <div style="font-size: 6pt; color: #047857; margin-top: 1pt;">Paid in Full</div>
                        @elseif($sale->status === 'layaway')
                            <span class="status-pill status-layaway">Layaway</span>
                            @if($sale->remaining_balance > 0)
                                <div style="font-size: 6pt; color: #be123c; font-family: 'DejaVu Sans Mono', monospace; font-weight: bold; margin-top: 1pt;">
                                    Bal: ₱{{ number_format($sale->remaining_balance, 2) }}
                                </div>
                                <div style="font-size: 5.5pt; color: #64748b; font-family: 'DejaVu Sans Mono', monospace;">
                                    ₱{{ number_format($sale->amount_paid, 2) }} paid
                                </div>
                            @else
                                <div style="font-size: 6pt; color: #047857; margin-top: 1pt;">Settled</div>
                            @endif
                        @elseif($sale->status === 'cancelled')
                            <span class="status-pill status-danger">Cancelled</span>
                        @else
                            <span class="status-pill status-pending">{{ ucfirst($sale->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 16pt; color: #94a3b8;">
                        No sales transactions found matching criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Minimal Sign-off -->
    <table class="sign-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-title">Prepared By (Cashier / Finance)</div>
            </td>
            <td style="width: 10%;"></td>
            <td class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-title">Approved By (Manager)</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td>Wella Metal Corporation &bull; Internal Sales Audit</td>
                <td style="text-align: right;">Total Records: {{ $sales->count() }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
