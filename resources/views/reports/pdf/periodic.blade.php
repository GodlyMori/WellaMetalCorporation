<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Periodic Business Report</title>
    <style>
        @page {
            margin: 22pt 26pt 26pt 26pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 7.5pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Clean Executive Header */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 5pt;
            margin-bottom: 8pt;
        }
        .company-name {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin: 0;
        }
        .report-title {
            font-size: 9pt;
            font-weight: 700;
            color: #334155;
            margin-top: 1pt;
        }
        .report-subtitle {
            font-size: 6.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }
        .meta-info {
            font-size: 7pt;
            color: #64748b;
            text-align: right;
            line-height: 1.3;
        }

        /* Executive KPI Cards */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
            background-color: #f8fafc;
            border: 1pt solid #cbd5e1;
        }
        .kpi-cell {
            padding: 6pt 8pt;
            border-right: 1pt solid #e2e8f0;
            vertical-align: top;
        }
        .kpi-cell:last-child {
            border-right: none;
        }
        .kpi-label {
            font-size: 6pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3pt;
            margin-bottom: 1pt;
        }
        .kpi-value {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
        }
        .kpi-sub {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 1pt;
        }

        /* Section Title */
        .section-header {
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            color: #0f172a;
            border-bottom: 1.5pt solid #cbd5e1;
            padding-bottom: 2pt;
            margin-top: 10pt;
            margin-bottom: 5pt;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-bottom: 8pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
            padding: 4pt 5pt;
            border-top: 1pt solid #cbd5e1;
            border-bottom: 1pt solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 3.5pt 5pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fbfcfd;
        }
        .data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: 700;
            border-top: 1.5pt solid #0f172a;
            border-bottom: 1.5pt solid #0f172a;
            padding: 4pt 5pt;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .mono { font-family: 'Courier New', Courier, monospace; }

        .page-break {
            page-break-before: always;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1pt 4pt;
            border-radius: 2pt;
            font-size: 5.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-completed { background-color: #dcfce7; color: #166534; }
        .badge-layaway   { background-color: #e0f2fe; color: #075985; }
        .badge-cancelled { background-color: #fee2e2; color: #991b1b; }
        .badge-high      { background-color: #dcfce7; color: #166534; }
        .badge-moderate  { background-color: #e0f2fe; color: #075985; }
        .badge-slow      { background-color: #fef3c7; color: #92400e; }
        .badge-stagnant  { background-color: #f1f5f9; color: #64748b; }

        /* Sign-off Box */
        .sign-table {
            width: 100%;
            margin-top: 14pt;
            border-top: 1pt solid #e2e8f0;
            padding-top: 8pt;
        }
        .sign-box {
            width: 31%;
            vertical-align: top;
            padding-right: 15pt;
        }
        .sign-line {
            border-bottom: 1pt solid #0f172a;
            height: 22pt;
            margin-bottom: 3pt;
        }
        .sign-title {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
        }
        .sign-sub {
            font-size: 6pt;
            color: #64748b;
        }

        .footer {
            margin-top: 8pt;
            font-size: 6pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 3pt;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="company-name">Wella Metal Corporation</div>
                <div class="report-title">Executive Periodic Business & Financial Audit Report</div>
                <div class="report-subtitle">Automated Periodic Reporting, Stock Turnover & Transaction Traceability</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</div>
                <div><strong>Cadence:</strong> {{ ucfirst($periodicity) }} Periodic Compilation</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Auditor:</strong> {{ $generatedBy }}</div>
            </td>
        </tr>
    </table>

    <!-- 4 Key Executive Performance Indicators -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Realized Cash Revenue</div>
                <div class="kpi-value">₱{{ number_format($periodic['totals']['realized_cash'], 2) }}</div>
                <div class="kpi-sub">Completed sales + layaway deposits</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Gross Sales Volume</div>
                <div class="kpi-value">₱{{ number_format($periodic['totals']['gross_sales_volume'], 2) }}</div>
                <div class="kpi-sub">{{ $periodic['totals']['completed_count'] + $periodic['totals']['layaway_count'] }} active orders (₱{{ number_format($periodic['totals']['average_ticket'], 2) }} AOV)</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Stock Turnover Volume</div>
                <div class="kpi-value">{{ number_format($movements['total_units_sold']) }} units</div>
                <div class="kpi-sub">{{ $movements['overall_turnover_rate'] }}% turnover rate ({{ $movements['total_restocked'] }} added)</div>
            </td>
            <td class="kpi-cell" style="width: 25%;">
                <div class="kpi-label">Outstanding Receivables</div>
                <div class="kpi-value">₱{{ number_format($periodic['totals']['outstanding_receivables'], 2) }}</div>
                <div class="kpi-sub">{{ $periodic['totals']['layaway_count'] }} layaway portfolio accounts</div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: AUTOMATED PERIODIC SALES LEDGER -->
    <div class="section-header">
        1. Automated Periodic Sales & Revenue Ledger ({{ ucfirst($periodicity) }} Aggregation)
    </div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 22%;">Periodic Interval</th>
                <th style="width: 10%; text-align: center;">Orders</th>
                <th style="width: 14%; text-align: right;">Completed (₱)</th>
                <th style="width: 14%; text-align: right;">Layaway Inflow (₱)</th>
                <th style="width: 12%; text-align: right;">Discounts (₱)</th>
                <th style="width: 14%; text-align: right;">Realized Cash (₱)</th>
                <th style="width: 14%; text-align: right;">Receivables Bal (₱)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($periodic['buckets'] as $b)
                <tr>
                    <td class="font-bold">{{ $b['label'] }}</td>
                    <td class="text-center mono">{{ $b['completed_count'] + $b['layaway_count'] }} <span style="color: #94a3b8; font-size: 6pt;">({{ $b['total_transactions'] }} tot)</span></td>
                    <td class="text-right mono">₱{{ number_format($b['completed_revenue'], 2) }}</td>
                    <td class="text-right mono">₱{{ number_format($b['layaway_deposits'], 2) }}</td>
                    <td class="text-right mono" style="color: #64748b;">-₱{{ number_format($b['total_discounts'], 2) }}</td>
                    <td class="text-right mono font-bold" style="color: #0f172a;">₱{{ number_format($b['realized_cash'], 2) }}</td>
                    <td class="text-right mono" style="color: #991b1b;">₱{{ number_format($b['outstanding_balance'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10pt; color: #94a3b8;">No sales activity recorded for the specified periodic window.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>AGGREGATE TOTALS</td>
                <td class="text-center mono">{{ $periodic['totals']['completed_count'] + $periodic['totals']['layaway_count'] }}</td>
                <td class="text-right mono">₱{{ number_format($periodic['totals']['completed_revenue'], 2) }}</td>
                <td class="text-right mono">₱{{ number_format($periodic['totals']['layaway_deposits'], 2) }}</td>
                <td class="text-right mono">-₱{{ number_format($periodic['totals']['total_discounts'], 2) }}</td>
                <td class="text-right mono">₱{{ number_format($periodic['totals']['realized_cash'], 2) }}</td>
                <td class="text-right mono">₱{{ number_format($periodic['totals']['outstanding_receivables'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- SECTION 2: STOCK TURNOVER & INVENTORY MOVEMENTS -->
    <div class="section-header">
        2. Stock Turnover & Inventory Movement Ledger
    </div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 10%;">Product SKU</th>
                <th style="width: 26%;">Product Name</th>
                <th style="width: 16%;">Category</th>
                <th style="width: 12%; text-align: right;">Unit Price</th>
                <th style="width: 10%; text-align: center;">Stock In Hand</th>
                <th style="width: 8%; text-align: center;">Sold</th>
                <th style="width: 8%; text-align: center;">Inflow</th>
                <th style="width: 10%; text-align: center;">Velocity</th>
            </tr>
        </thead>
        <tbody>
            @forelse(array_slice($movements['items'], 0, 15) as $item)
                <tr>
                    <td class="mono font-bold">{{ $item['formatted_id'] }}</td>
                    <td class="font-bold">{{ $item['name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td class="text-right mono">₱{{ number_format($item['tagged_price'], 2) }}</td>
                    <td class="text-center mono">{{ $item['current_stock'] }}</td>
                    <td class="text-center mono font-bold" style="color: #0f172a;">{{ $item['units_sold'] }}</td>
                    <td class="text-center mono" style="color: #166534;">+{{ $item['units_added'] }}</td>
                    <td class="text-center">
                        @if($item['velocity'] === 'High Velocity')
                            <span class="badge badge-high">High</span>
                        @elseif($item['velocity'] === 'Moderate')
                            <span class="badge badge-moderate">Moderate</span>
                        @elseif($item['velocity'] === 'Slow-moving')
                            <span class="badge badge-slow">Slow</span>
                        @else
                            <span class="badge badge-stagnant">Stagnant</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10pt; color: #94a3b8;">No inventory movement logged for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- SECTION 3: TRANSACTION TRACEABILITY LEDGER -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="company-name">Wella Metal Corporation</div>
                <div class="report-title">Transaction Traceability Ledger (Underlying Receipts Audit)</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div>Cross-referencing Underlying Receipts against Periodic Totals</div>
                <div><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</div>
            </td>
        </tr>
    </table>

    <div class="section-header">
        3. Transaction Traceability Ledger (Individual Underlying Sales Receipts)
    </div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 13%;">Receipt #</th>
                <th style="width: 11%;">Date</th>
                <th style="width: 18%;">Customer</th>
                <th style="width: 25%;">Item Description</th>
                <th style="width: 11%; text-align: right;">Total Amount</th>
                <th style="width: 11%; text-align: right;">Cash Paid</th>
                <th style="width: 11%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($traceableSales as $sale)
                <tr>
                    <td class="mono font-bold">{{ $sale->sale_number }}</td>
                    <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                    <td>
                        <div class="font-bold">{{ $sale->customer_name }}</div>
                        <div style="font-size: 6pt; color: #64748b;">{{ $sale->customer_phone ?: 'No phone' }}</div>
                    </td>
                    <td>
                        @if($sale->items->isNotEmpty())
                            @foreach($sale->items as $it)
                                <div>{{ $it->product?->name ?? 'Item' }} <span style="color: #64748b;">&times; {{ $it->quantity }}</span></div>
                            @endforeach
                        @else
                            <span>{{ $sale->product_name ?: 'Custom order' }}</span>
                        @endif
                    </td>
                    <td class="text-right mono font-bold">₱{{ number_format($sale->amount, 2) }}</td>
                    <td class="text-right mono" style="color: #166534;">₱{{ number_format($sale->amount_paid, 2) }}</td>
                    <td class="text-center">
                        @if($sale->status === 'completed')
                            <span class="badge badge-completed">Completed</span>
                        @elseif($sale->status === 'layaway')
                            <span class="badge badge-layaway">Lay-away</span>
                        @else
                            <span class="badge badge-cancelled">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10pt; color: #94a3b8;">No sales receipts recorded in this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- EXECUTIVE SIGN-OFF BLOCK -->
    <table class="sign-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sign-box">
                <div class="sign-title">Prepared by:</div>
                <div class="sign-line"></div>
                <div class="sign-sub">{{ $generatedBy }} (Sales Secretary / Cashier)</div>
                <div class="sign-sub">Date: ________________________</div>
            </td>
            <td class="sign-box">
                <div class="sign-title">Reviewed & Verified by:</div>
                <div class="sign-line"></div>
                <div class="sign-sub">Operations General Manager</div>
                <div class="sign-sub">Date: ________________________</div>
            </td>
            <td class="sign-box">
                <div class="sign-title">Executive Approval:</div>
                <div class="sign-line"></div>
                <div class="sign-sub">Chief Executive Officer (CEO)</div>
                <div class="sign-sub">Date: ________________________</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Wella Metal Corporation &bull; Internal Periodic Operations & Business Audit &bull; Generated {{ $generatedAt }}
    </div>

</body>
</html>
