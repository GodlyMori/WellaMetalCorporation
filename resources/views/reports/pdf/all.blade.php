<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Comprehensive Master Audit Report</title>
    <style>
        @page {
            margin: 26pt 28pt 32pt 28pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8.5pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 7pt;
            margin-bottom: 10pt;
        }
        .company-title {
            font-size: 15pt;
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
            font-size: 7.5pt;
            color: #64748b;
            line-height: 1.3;
        }

        /* Executive Master Summary Strip */
        .summary-strip {
            width: 100%;
            border: 1pt solid #cbd5e1;
            background-color: #f8fafc;
            border-collapse: collapse;
            margin-bottom: 14pt;
        }
        .summary-cell {
            padding: 6pt 8pt;
            border-right: 1pt solid #e2e8f0;
            vertical-align: top;
        }
        .summary-cell:last-child {
            border-right: none;
        }
        .summary-label {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            color: #64748b;
            margin-bottom: 2pt;
        }
        .summary-value {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
        }

        /* Section Headings */
        .section-heading {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            color: #0f172a;
            border-left: 3pt solid #142259;
            padding-left: 6pt;
            margin-top: 14pt;
            margin-bottom: 6pt;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 12pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            border-top: 1pt solid #cbd5e1;
            border-bottom: 1.5pt solid #0f172a;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            padding: 4pt 4pt;
            text-align: left;
        }
        .data-table td {
            padding: 4pt 4pt;
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

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 1pt 3.5pt;
            border-radius: 2pt;
            font-size: 6pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
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
        .badge-overdue {
            background-color: #fff1f2;
            color: #9f1239;
            border: 0.5pt solid #fecdd3;
        }
        .badge-neardue {
            background-color: #fffbeb;
            color: #92400e;
            border: 0.5pt solid #fde68a;
        }
        .badge-cancelled {
            background-color: #f1f5f9;
            color: #475569;
            border: 0.5pt solid #cbd5e1;
        }
        .page-break {
            page-break-before: always;
        }

        /* Signature block */
        .signature-table {
            width: 100%;
            margin-top: 18pt;
            border-top: 1pt solid #e2e8f0;
            padding-top: 12pt;
        }
        .sig-box {
            width: 30%;
            vertical-align: top;
        }
        .sig-line {
            border-bottom: 1pt solid #0f172a;
            height: 20pt;
            margin-bottom: 4pt;
        }
        .sig-title {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }
        .sig-sub {
            font-size: 6.5pt;
            color: #94a3b8;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 3pt;
        }
    </style>
</head>
<body>
    <!-- Corporate Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="company-title">Wella Metal Corporation</div>
                <div class="report-subtitle">Comprehensive Master Operations & Financial Audit</div>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <div class="meta-text"><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div class="meta-text"><strong>Audit Scope:</strong> Sales, Inventory & Layaway Receivables</div>
                <div class="meta-text"><strong>Classification:</strong> Official Confidential Audit</div>
            </td>
        </tr>
    </table>

    <!-- Master Executive Summary Strip -->
    <table class="summary-strip" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Total Realized Revenue</div>
                <div class="summary-value">₱{{ number_format($totalSalesRevenue, 2) }}</div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 1pt;">{{ $completedSalesCount }} completed orders</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Total Inventory Valuation</div>
                <div class="summary-value">₱{{ number_format($totalValuation, 2) }}</div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 1pt;">{{ number_format($totalStockUnits) }} total stock units</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Outstanding Receivables</div>
                <div class="summary-value">₱{{ number_format($totalLayawayReceivables, 2) }}</div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 1pt;">{{ $activeLayawaysCount }} active accounts</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Realized Installment Cash</div>
                <div class="summary-value">₱{{ number_format($totalLayawayCollected, 2) }}</div>
                <div style="font-size: 6.5pt; color: #64748b; margin-top: 1pt;">{{ $overdueLayawaysCount }} overdue accounts</div>
            </td>
        </tr>
    </table>

    <!-- ============================================== -->
    <!-- SECTION 1: SALES & ORDERS AUDIT LOG            -->
    <!-- ============================================== -->
    <div class="section-heading">Section I: Sales & Commercial Orders Performance</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 14%;">Order #</th>
                <th style="width: 13%;">Date</th>
                <th style="width: 24%;">Customer</th>
                <th style="width: 13%;" class="text-right">Total (₱)</th>
                <th style="width: 13%;" class="text-right">Paid (₱)</th>
                <th style="width: 12%;" class="text-right">Discount</th>
                <th style="width: 11%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales->take(30) as $sale)
                <tr>
                    <td class="mono font-bold">{{ $sale->sale_number }}</td>
                    <td>{{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : '-' }}</td>
                    <td>
                        <strong>{{ $sale->customer_name }}</strong>
                        @if($sale->customer_phone)
                            <span style="color: #64748b;">({{ $sale->customer_phone }})</span>
                        @endif
                    </td>
                    <td class="text-right mono">₱{{ number_format($sale->amount, 2) }}</td>
                    <td class="text-right mono">₱{{ number_format($sale->amount_paid, 2) }}</td>
                    <td class="text-right mono">
                        {{ $sale->discount_amount > 0 ? '-₱' . number_format($sale->discount_amount, 2) : '—' }}
                    </td>
                    <td class="text-center">
                        @if($sale->status === 'completed')
                            <span class="badge badge-completed">Settled</span>
                        @elseif($sale->status === 'layaway')
                            <span class="badge badge-layaway">Layaway</span>
                        @elseif($sale->status === 'cancelled')
                            <span class="badge badge-cancelled">Cancelled</span>
                        @else
                            <span class="badge badge-neardue">{{ ucfirst($sale->status) }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12pt; color: #94a3b8;">No sales records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ============================================== -->
    <!-- SECTION 2: INVENTORY VALUATION & STOCK STATUS -->
    <!-- ============================================== -->
    <div class="section-heading">Section II: Inventory Valuation & Physical Stock Status</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 12%;">SKU / Code</th>
                <th style="width: 32%;">Product Name & Specifications</th>
                <th style="width: 16%;">Category</th>
                <th style="width: 13%;" class="text-right">Unit Price</th>
                <th style="width: 12%;" class="text-center">Stock Units</th>
                <th style="width: 15%;" class="text-right">Total Valuation</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td class="mono">#{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <strong>{{ $p->name }}</strong>
                        @if($p->description)
                            <br><span style="font-size: 6.5pt; color: #64748b;">{{ Str::limit($p->description, 50) }}</span>
                        @endif
                    </td>
                    <td>{{ $p->category }}</td>
                    <td class="text-right mono">₱{{ number_format($p->tagged_price, 2) }}</td>
                    <td class="text-center mono font-bold">
                        @if($p->quantity_in_stock <= 0)
                            <span style="color: #be123c;">0</span>
                        @elseif($p->quantity_in_stock <= 4)
                            <span style="color: #b45309;">{{ $p->quantity_in_stock }}</span>
                        @else
                            <span style="color: #047857;">{{ $p->quantity_in_stock }}</span>
                        @endif
                    </td>
                    <td class="text-right mono font-bold">
                        ₱{{ number_format($p->tagged_price * $p->quantity_in_stock, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 12pt; color: #94a3b8;">No inventory items recorded.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ============================================== -->
    <!-- SECTION 3: LAYAWAY PORTFOLIO & RECEIVABLES     -->
    <!-- ============================================== -->
    <div class="section-heading">Section III: Layaway Portfolio & Receivables Aging</div>
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 13%;">Contract #</th>
                <th style="width: 22%;">Customer</th>
                <th style="width: 15%;" class="text-right">Total (₱)</th>
                <th style="width: 15%;" class="text-right">Paid (₱)</th>
                <th style="width: 15%;" class="text-right">Outstanding (₱)</th>
                <th style="width: 10%;">Due Date</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($layaways as $c)
                @php
                    $isOverdue = $c->layaway_expires_at && $c->layaway_expires_at->isPast();
                    $isNearDue = $c->layaway_expires_at && !$isOverdue && $c->layaway_expires_at->diffInDays(now()) <= 14;
                @endphp
                <tr>
                    <td class="mono font-bold">{{ $c->sale_number }}</td>
                    <td>
                        <strong>{{ $c->customer_name }}</strong>
                        @if($c->customer_phone)
                            <br><span style="font-size: 6.5pt; color: #64748b;">{{ $c->customer_phone }}</span>
                        @endif
                    </td>
                    <td class="text-right mono">₱{{ number_format($c->amount, 2) }}</td>
                    <td class="text-right mono">₱{{ number_format($c->amount_paid, 2) }}</td>
                    <td class="text-right mono font-bold" style="color: #0f172a;">
                        ₱{{ number_format($c->remaining_balance, 2) }}
                    </td>
                    <td>{{ $c->layaway_expires_at ? $c->layaway_expires_at->format('M d, Y') : '—' }}</td>
                    <td class="text-center">
                        @if($isOverdue)
                            <span class="badge badge-overdue">Overdue</span>
                        @elseif($isNearDue)
                            <span class="badge badge-neardue">Near Due</span>
                        @else
                            <span class="badge badge-layaway">Active</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 12pt; color: #94a3b8;">No active layaway accounts.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Executive Sign-off Block -->
    <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Prepared By</div>
                <div class="sig-sub">Finance Officer / Cashier</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Audited & Verified</div>
                <div class="sig-sub">Accounting Supervisor</div>
            </td>
            <td style="width: 5%;"></td>
            <td class="sig-box">
                <div class="sig-line"></div>
                <div class="sig-title">Approved By</div>
                <div class="sig-sub">General Manager</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td>Wella Metal Corporation &bull; Master Internal Audit &amp; Reporting Document</td>
                <td class="text-right">Document ID: WMC-MASTER-{{ date('Ymd') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
