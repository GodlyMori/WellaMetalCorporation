<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Sales Audit Report</title>
    <style>
        @page {
            margin: 28pt 32pt 36pt 32pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 9pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        /* Minimalist Corporate Header */
        .header-table {
            width: 100%;
            border-bottom: 1.5pt solid #0f172a;
            padding-bottom: 8pt;
            margin-bottom: 10pt;
        }
        .company-title {
            font-size: 14pt;
            font-weight: 800;
            letter-spacing: 0.5pt;
            color: #0f172a;
            text-transform: uppercase;
            margin: 0;
        }
        .report-subtitle {
            font-size: 8.5pt;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            margin-top: 2pt;
        }
        .meta-text {
            font-size: 8pt;
            color: #64748b;
            line-height: 1.3;
        }

        /* Minimal Executive Summary Strip */
        .summary-strip {
            width: 100%;
            border: 1pt solid #cbd5e1;
            background-color: #f8fafc;
            border-collapse: collapse;
            margin-bottom: 12pt;
        }
        .summary-cell {
            padding: 7pt 10pt;
            border-right: 1pt solid #e2e8f0;
            vertical-align: top;
        }
        .summary-cell:last-child {
            border-right: none;
        }
        .summary-label {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            color: #64748b;
            margin-bottom: 2pt;
        }
        .summary-value {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            border-top: 1pt solid #cbd5e1;
            border-bottom: 1.5pt solid #0f172a;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            padding: 5pt 5pt;
            text-align: left;
        }
        .data-table td {
            padding: 5pt 5pt;
            border-bottom: 0.5pt solid #e2e8f0;
            vertical-align: middle;
            color: #1e293b;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }

        /* Clean Minimal Badges */
        .badge {
            display: inline-block;
            padding: 1.5pt 4pt;
            border-radius: 2pt;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }
        .badge-completed {
            background-color: #ecfdf5;
            color: #065f46;
            border: 0.5pt solid #a7f3d0;
        }
        .badge-layaway {
            background-color: #eff6ff;
            color: #1e40af;
            border: 0.5pt solid #bfdbfe;
        }
        .badge-pending {
            background-color: #fffbeb;
            color: #92400e;
            border: 0.5pt solid #fde68a;
        }
        .badge-cancelled {
            background-color: #fef2f2;
            color: #991b1b;
            border: 0.5pt solid #fecaca;
        }

        /* Footer */
        .footer {
            margin-top: 15pt;
            border-top: 0.5pt solid #cbd5e1;
            padding-top: 5pt;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <h1 class="company-title">Wella Metal Corporation</h1>
                <div class="report-subtitle">Official Sales & Revenue Audit Ledger</div>
            </td>
            <td style="text-align: right; vertical-align: middle;" class="meta-text">
                <div><strong>Period:</strong> {{ $startDate ? date('M j, Y', strtotime($startDate)) : 'All Time' }} – {{ $endDate ? date('M j, Y', strtotime($endDate)) : 'Present' }}</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Filter Status:</strong> {{ strtoupper($statusFilter ?? 'ALL') }}</div>
            </td>
        </tr>
    </table>

    <!-- Concise Top Summary Strip -->
    <table class="summary-strip">
        <tr>
            <td class="summary-cell" width="28%">
                <div class="summary-label">Realized Cash Revenue</div>
                <div class="summary-value">₱{{ number_format($totalRevenue, 2) }}</div>
            </td>
            <td class="summary-cell" width="24%">
                <div class="summary-label">Completed Orders</div>
                <div class="summary-value">{{ $completedCount }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">settled</span></div>
            </td>
            <td class="summary-cell" width="24%">
                <div class="summary-label">Active Layaways</div>
                <div class="summary-value">{{ $layawayCount ?? 0 }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">accounts</span></div>
            </td>
            <td class="summary-cell" width="24%">
                <div class="summary-label">Average Order Value</div>
                <div class="summary-value">₱{{ number_format($averageOrderValue ?? 0, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- Detailed Ledger Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="14%">Order #</th>
                <th width="20%">Customer Name</th>
                <th width="26%">Product Description</th>
                <th width="10%" class="text-right">Discount</th>
                <th width="12%" class="text-right">Amount (PHP)</th>
                <th width="10%" class="text-center">Date</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td class="mono"><strong>{{ $sale->sale_number }}</strong></td>
                    <td>{{ $sale->customer_name }}</td>
                    <td>
                        {{ $sale->product_name }}
                        @if($sale->promo_name)
                            <span style="font-size: 6.5pt; color: #6b21a8; font-weight: bold;">({{ $sale->promo_name }})</span>
                        @endif
                    </td>
                    <td class="text-right" style="color: {{ $sale->discount_amount > 0 ? '#047857' : '#64748b' }};">
                        {{ $sale->discount_amount > 0 ? '-₱' . number_format($sale->discount_amount, 2) : '—' }}
                    </td>
                    <td class="text-right mono">
                        <strong>₱{{ number_format($sale->amount, 2) }}</strong>
                    </td>
                    <td class="text-center">{{ $sale->sale_date->format('Y-m-d') }}</td>
                    <td class="text-center">
                        @if($sale->status === 'completed')
                            <span class="badge badge-completed">Settled</span>
                        @elseif($sale->status === 'layaway')
                            <span class="badge badge-layaway">Lay-Away</span>
                        @elseif($sale->status === 'pending')
                            <span class="badge badge-pending">Pending</span>
                        @else
                            <span class="badge badge-cancelled">Cancelled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16pt; color: #94a3b8;">
                        No sales transactions recorded matching the selected filter criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Minimal Confidential Footer -->
    <div class="footer">
        Confidential Internal Document &bull; Wella Metal Corporation Management Information System &bull; Total Transactions: {{ $sales->count() }}
    </div>

</body>
</html>
