<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Comprehensive Master Report</title>
    <style>
        @page {
            margin: 24pt 28pt 30pt 28pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 8pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        /* Clean Header */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 6pt;
            margin-bottom: 10pt;
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
            line-height: 1.35;
        }

        /* Master KPI Cards */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12pt;
            background-color: #f8fafc;
            border: 1pt solid #cbd5e1;
        }
        .kpi-cell {
            padding: 7pt 9pt;
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
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-sub {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 1pt;
        }

        /* Section Title */
        .section-title {
            font-size: 9pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            color: #0f172a;
            border-bottom: 1.5pt solid #cbd5e1;
            padding-bottom: 3pt;
            margin-top: 12pt;
            margin-bottom: 6pt;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 10pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
            padding: 4.5pt 5pt;
            border-top: 1pt solid #cbd5e1;
            border-bottom: 1pt solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 4pt 5pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fbfcfd;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .mono { font-family: 'Courier New', Courier, monospace; }

        .page-break {
            page-break-before: always;
        }

        /* Minimal Status Pills */
        .status-pill {
            display: inline-block;
            padding: 1pt 4pt;
            border-radius: 2pt;
            font-size: 6pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-layaway   { background-color: #e0f2fe; color: #075985; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .status-overdue   { background-color: #fee2e2; color: #991b1b; }

        /* Minimal Sign-off */
        .sign-table {
            width: 100%;
            margin-top: 14pt;
            border-top: 1pt solid #e2e8f0;
            padding-top: 10pt;
        }
        .sign-box {
            width: 45%;
            vertical-align: top;
        }
        .sign-line {
            border-bottom: 1pt solid #0f172a;
            height: 18pt;
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
            margin-top: 10pt;
            font-size: 6.5pt;
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
                <div class="report-title">Comprehensive Master Operations & Financial Audit</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Scope:</strong> Sales, Inventory & Layaway Receivables</div>
                <div><strong>Document:</strong> Official Internal Audit</div>
            </td>
        </tr>
    </table>

    <!-- Master 4 Key Metrics Strip -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Total Realized Revenue</div>
                <div class="kpi-value">₱{{ number_format($totalSalesRevenue, 2) }}</div>
                <div class="kpi-sub">{{ $completedSalesCount }} completed orders</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Inventory Asset Valuation</div>
                <div class="kpi-value">₱{{ number_format($totalValuation, 2) }}</div>
                <div class="kpi-sub">{{ number_format($totalStockUnits) }} total stock units</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Outstanding Receivables</div>
                <div class="kpi-value">₱{{ number_format($totalLayawayReceivables, 2) }}</div>
                <div class="kpi-sub">{{ $activeLayawaysCount }} active accounts</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Layaway Cash Collected</div>
                <div class="kpi-value">₱{{ number_format($totalLayawayCollected, 2) }}</div>
                <div class="kpi-sub">Total installment deposits</div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: SALES & ORDERS -->
    <div class="section-title">1. Sales & Commercial Orders Performance</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 15%;">Order #</th>
                <th style="width: 13%;">Date</th>
                <th style="width: 28%;">Customer</th>
                <th style="width: 16%;" class="text-right">Total Amount</th>
                <th style="width: 14%;" class="text-right">Paid Cash</th>
                <th style="width: 14%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales->take(25) as $sale)
                <tr>
                    <td class="mono font-bold">{{ $sale->sale_number }}</td>
                    <td class="mono text-center">{{ $sale->sale_date ? $sale->sale_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $sale->customer_name }}</td>
                    <td class="text-right mono font-bold">₱{{ number_format($sale->amount, 2) }}</td>
                    <td class="text-right mono">₱{{ number_format($sale->amount_paid, 2) }}</td>
                    <td class="text-center">
                        @if($sale->status === 'completed')
                            <span class="status-pill status-completed">Completed</span>
                        @elseif($sale->status === 'layaway')
                            <span class="status-pill status-layaway">Layaway</span>
                        @elseif($sale->status === 'cancelled')
                            <span class="status-pill status-cancelled">Cancelled</span>
                        @else
                            <span class="status-pill">{{ ucfirst($sale->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 10pt; color: #94a3b8;">No sales records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- SECTION 2: INVENTORY -->
    <div class="section-title">2. Inventory Stock & Asset Valuation</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 12%;">Code</th>
                <th style="width: 36%;">Product Name</th>
                <th style="width: 18%;">Category</th>
                <th style="width: 14%;" class="text-right">Unit Price</th>
                <th style="width: 8%;" class="text-center">Stock</th>
                <th style="width: 12%;" class="text-right">Valuation (₱)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td class="mono font-bold">#{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category }}</td>
                    <td class="text-right mono">₱{{ number_format($p->tagged_price, 2) }}</td>
                    <td class="text-center mono font-bold">{{ $p->quantity_in_stock }}</td>
                    <td class="text-right mono font-bold">₱{{ number_format($p->tagged_price * $p->quantity_in_stock, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 10pt; color: #94a3b8;">No inventory items found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECTION 3: LAYAWAY RECEIVABLES -->
    <div class="section-title">3. Layaway Portfolio & Receivables Aging</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 16%;">Contract #</th>
                <th style="width: 24%;">Customer</th>
                <th style="width: 15%;" class="text-right">Total (₱)</th>
                <th style="width: 15%;" class="text-right">Paid (₱)</th>
                <th style="width: 15%;" class="text-right">Balance (₱)</th>
                <th style="width: 15%;" class="text-center">Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($layaways as $c)
                @php
                    $isOverdue = $c->layaway_expires_at && $c->layaway_expires_at->isPast();
                @endphp
                <tr>
                    <td class="mono font-bold">{{ $c->sale_number }}</td>
                    <td><strong>{{ $c->customer_name }}</strong></td>
                    <td class="text-right mono">₱{{ number_format($c->amount, 2) }}</td>
                    <td class="text-right mono" style="color: #047857;">₱{{ number_format($c->amount_paid, 2) }}</td>
                    <td class="text-right mono font-bold">₱{{ number_format($c->remaining_balance, 2) }}</td>
                    <td class="text-center">
                        <span class="mono">{{ $c->layaway_expires_at ? $c->layaway_expires_at->format('Y-m-d') : '—' }}</span>
                        @if($isOverdue)
                            <span class="status-pill status-overdue" style="margin-left: 2pt;">Overdue</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 10pt; color: #94a3b8;">No active layaway contracts.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Sign-off Block -->
    <table class="sign-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-title">Prepared By (Finance Officer)</div>
            </td>
            <td style="width: 10%;"></td>
            <td class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-title">Approved By (General Manager)</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td>Wella Metal Corporation &bull; Consolidated Internal Audit</td>
                <td style="text-align: right;">Official Audit Report</td>
            </tr>
        </table>
    </div>

</body>
</html>
