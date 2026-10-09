<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Layaway Receivables & Aging Report</title>
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
        /* Corporate Header */
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

        /* Executive Summary Strip */
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

        /* Clean Badges */
        .badge {
            display: inline-block;
            padding: 1.5pt 4pt;
            border-radius: 2pt;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }
        .badge-active {
            background-color: #eff6ff;
            color: #1e40af;
            border: 0.5pt solid #bfdbfe;
        }
        .badge-completed {
            background-color: #ecfdf5;
            color: #065f46;
            border: 0.5pt solid #a7f3d0;
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

        /* Signature block */
        .signature-table {
            width: 100%;
            margin-top: 24pt;
            border-top: 1pt solid #e2e8f0;
            padding-top: 16pt;
        }
        .sig-box {
            width: 30%;
            vertical-align: top;
        }
        .sig-line {
            border-bottom: 1pt solid #0f172a;
            height: 24pt;
            margin-bottom: 4pt;
        }
        .sig-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }
        .sig-sub {
            font-size: 7pt;
            color: #94a3b8;
        }

        /* Page Numbering */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 4pt;
        }
    </style>
</head>
<body>
    <!-- Corporate Header -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: bottom;">
                <div class="company-title">Wella Metal Corporation</div>
                <div class="report-subtitle">Layaway Portfolio & Receivables Aging Audit</div>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <div class="meta-text"><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div class="meta-text"><strong>Status Filter:</strong> {{ strtoupper($statusFilter) }}</div>
                <div class="meta-text"><strong>Document Type:</strong> Confidential Audit Report</div>
            </td>
        </tr>
    </table>

    <!-- Executive Summary Strip -->
    <table class="summary-strip" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Outstanding Receivables</div>
                <div class="summary-value">₱{{ number_format($totalReceivables, 2) }}</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Realized Collections</div>
                <div class="summary-value">₱{{ number_format($totalPaid, 2) }}</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Active Contracts</div>
                <div class="summary-value">{{ number_format($activeCount) }}</div>
            </td>
            <td class="summary-cell" style="width: 25%;">
                <div class="summary-label">Total Portfolio Value</div>
                <div class="summary-value">₱{{ number_format($totalContractValue, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 13%;">Contract #</th>
                <th style="width: 14%;">Date</th>
                <th style="width: 20%;">Customer</th>
                <th style="width: 13%;" class="text-right">Total (₱)</th>
                <th style="width: 13%;" class="text-right">Paid (₱)</th>
                <th style="width: 13%;" class="text-right">Balance (₱)</th>
                <th style="width: 14%;">Expiry / Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
                @php
                    $isOverdue = $contract->status === 'layaway' && $contract->layaway_expires_at && $contract->layaway_expires_at->isPast();
                    $isNearDue = $contract->status === 'layaway' && $contract->layaway_expires_at && !$isOverdue && $contract->layaway_expires_at->diffInDays(now()) <= 14;
                @endphp
                <tr>
                    <td class="mono">{{ $contract->sale_number }}</td>
                    <td>{{ $contract->sale_date ? $contract->sale_date->format('M d, Y') : '-' }}</td>
                    <td>
                        <strong>{{ $contract->customer_name }}</strong>
                        @if($contract->customer_phone)
                            <br><span style="font-size: 7pt; color: #64748b;">{{ $contract->customer_phone }}</span>
                        @endif
                    </td>
                    <td class="text-right mono">₱{{ number_format($contract->amount, 2) }}</td>
                    <td class="text-right mono">₱{{ number_format($contract->amount_paid, 2) }}</td>
                    <td class="text-right mono font-bold">
                        @if($contract->status === 'completed')
                            <span style="color: #059669;">₱0.00</span>
                        @else
                            ₱{{ number_format($contract->remaining_balance, 2) }}
                        @endif
                    </td>
                    <td>
                        @if($contract->status === 'completed')
                            <span class="badge badge-completed">Settled</span>
                        @elseif($contract->status === 'cancelled')
                            <span class="badge badge-cancelled">Cancelled</span>
                        @elseif($contract->layaway_expires_at)
                            {{ $contract->layaway_expires_at->format('M d, Y') }}
                            @if($isOverdue)
                                <br><span class="badge badge-overdue">Overdue</span>
                            @elseif($isNearDue)
                                <br><span class="badge badge-neardue">Near Due</span>
                            @else
                                <br><span class="badge badge-active">Active</span>
                            @endif
                        @else
                            <span style="color: #94a3b8;">No date</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 18pt; color: #94a3b8;">
                        No layaway contract records found matching the specified criteria.
                    </td>
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
                <td>Wella Metal Corporation &bull; Internal Audit & Accounting System</td>
                <td class="text-right">Report Classification: CONFIDENTIAL</td>
            </tr>
        </table>
    </div>
</body>
</html>
