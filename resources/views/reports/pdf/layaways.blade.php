<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Layaway Report</title>
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
        .status-active    { background-color: #e0f2fe; color: #075985; }
        .status-settled   { background-color: #dcfce7; color: #166534; }
        .status-neardue   { background-color: #fef3c7; color: #92400e; }
        .status-overdue   { background-color: #fee2e2; color: #991b1b; }
        .status-cancelled { background-color: #f1f5f9; color: #475569; }

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
                <div class="report-title">Layaway Receivables & Aging Report</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div><strong>Status Filter:</strong> {{ strtoupper($statusFilter ?? 'ACTIVE') }}</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Total Accounts:</strong> {{ $contracts->count() }} records</div>
            </td>
        </tr>
    </table>

    <!-- 4 Key Metrics Strip -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-cell" style="width: 28%;">
                <div class="kpi-label">Outstanding Receivables</div>
                <div class="kpi-value">₱{{ number_format($totalReceivables, 2) }}</div>
                <div class="kpi-sub">Remaining balance across active accounts</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Cash Collected</div>
                <div class="kpi-value">₱{{ number_format($totalPaid, 2) }}</div>
                <div class="kpi-sub">Deposits & installments received</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Active Contracts</div>
                <div class="kpi-value">{{ number_format($activeCount) }}</div>
                <div class="kpi-sub">Open layaway accounts</div>
            </td>
            <td class="kpi-cell" style="width: 24%;">
                <div class="kpi-label">Total Contract Value</div>
                <div class="kpi-value">₱{{ number_format($totalContractValue, 2) }}</div>
                <div class="kpi-sub">Full portfolio purchase volume</div>
            </td>
        </tr>
    </table>

    <!-- Clean Layaway Data Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 14%;">Contract #</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 22%;">Customer</th>
                <th style="width: 13%;" class="text-right">Total (₱)</th>
                <th style="width: 13%;" class="text-right">Paid (₱)</th>
                <th style="width: 13%;" class="text-right">Balance (₱)</th>
                <th style="width: 13%;" class="text-center">Due Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
                @php
                    $isOverdue = $contract->status === 'layaway' && $contract->layaway_expires_at && $contract->layaway_expires_at->isPast();
                    $isNearDue = $contract->status === 'layaway' && $contract->layaway_expires_at && !$isOverdue && $contract->layaway_expires_at->diffInDays(now()) <= 14;
                @endphp
                <tr>
                    <td class="mono font-bold">{{ $contract->sale_number }}</td>
                    <td class="mono text-center">{{ $contract->sale_date ? $contract->sale_date->format('Y-m-d') : '-' }}</td>
                    <td>
                        <strong>{{ $contract->customer_name }}</strong>
                        @if($contract->customer_phone)
                            <br><span style="font-size: 7pt; color: #64748b;">{{ $contract->customer_phone }}</span>
                        @endif
                    </td>
                    <td class="text-right mono">₱{{ number_format($contract->amount, 2) }}</td>
                    <td class="text-right mono" style="color: #047857;">₱{{ number_format($contract->amount_paid, 2) }}</td>
                    <td class="text-right mono font-bold">
                        @if($contract->status === 'completed')
                            <span style="color: #047857;">₱0.00</span>
                        @else
                            ₱{{ number_format($contract->remaining_balance, 2) }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($contract->status === 'completed')
                            <span class="status-pill status-settled">Settled</span>
                        @elseif($contract->status === 'cancelled')
                            <span class="status-pill status-cancelled">Cancelled</span>
                        @elseif($contract->layaway_expires_at)
                            <span class="mono">{{ $contract->layaway_expires_at->format('Y-m-d') }}</span>
                            @if($isOverdue)
                                <br><span class="status-pill status-overdue">Overdue</span>
                            @elseif($isNearDue)
                                <br><span class="status-pill status-neardue">Near Due</span>
                            @endif
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16pt; color: #94a3b8;">
                        No layaway contract records found matching current criteria.
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
                <div class="sign-title">Prepared By (Credit Officer)</div>
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
                <td>Wella Metal Corporation &bull; Internal Layaway Receivables Audit</td>
                <td style="text-align: right;">Total Contracts: {{ $contracts->count() }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
