<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Inventory Report</title>
    <style>
        @page {
            margin: 24pt 28pt 30pt 28pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
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
        .mono { font-family: 'Courier New', Courier, monospace; }

        /* Minimal Status Pills */
        .status-pill {
            display: inline-block;
            padding: 1.5pt 5pt;
            border-radius: 2pt;
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-instock  { background-color: #dcfce7; color: #166534; }
        .status-lowstock { background-color: #fef3c7; color: #92400e; }
        .status-outstock { background-color: #fee2e2; color: #991b1b; }

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
                <div class="report-title">Inventory Stock & Valuation Report</div>
            </td>
            <td class="meta-info" style="vertical-align: bottom;">
                <div><strong>Category Filter:</strong> {{ strtoupper($category ?? 'ALL') }}</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Tracked Products:</strong> {{ $products->count() }} SKUs</div>
            </td>
        </tr>
    </table>

    <!-- 3 Key Metrics Strip -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-cell" style="width: 38%;">
                <div class="kpi-label">Total Inventory Valuation</div>
                <div class="kpi-value">₱{{ number_format($totalValuation, 2) }}</div>
                <div class="kpi-sub">Total asset valuation at tagged prices</div>
            </td>
            <td class="kpi-cell" style="width: 32%;">
                <div class="kpi-label">Total Stock On Hand</div>
                <div class="kpi-value">{{ number_format($totalStock) }} units</div>
                <div class="kpi-sub">Physical quantity on yard/warehouse</div>
            </td>
            <td class="kpi-cell" style="width: 30%;">
                <div class="kpi-label">Low Stock Attention</div>
                <div class="kpi-value" style="color: {{ $lowStockCount > 0 ? '#b45309' : '#0f172a' }};">
                    {{ $lowStockCount }} items
                </div>
                <div class="kpi-sub">&le; 4 units remaining</div>
            </td>
        </tr>
    </table>

    <!-- Clean Inventory Data Table -->
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 12%;">Code</th>
                <th style="width: 32%;">Product Name</th>
                <th style="width: 18%;">Category</th>
                <th style="width: 13%;" class="text-right">Unit Price</th>
                <th style="width: 10%;" class="text-center">Stock</th>
                <th style="width: 15%;" class="text-right">Total Value (₱)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td class="mono font-bold">#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <div style="font-size: 7pt; color: #64748b;">{{ Str::limit($product->description, 50) }}</div>
                        @endif
                    </td>
                    <td>{{ $product->category }}</td>
                    <td class="text-right mono">₱{{ number_format($product->tagged_price, 2) }}</td>
                    <td class="text-center mono font-bold">
                        @if($product->quantity_in_stock <= 0)
                            <span style="color: #be123c;">0</span>
                        @elseif($product->quantity_in_stock <= 4)
                            <span style="color: #b45309;">{{ $product->quantity_in_stock }}</span>
                        @else
                            <span style="color: #047857;">{{ $product->quantity_in_stock }}</span>
                        @endif
                    </td>
                    <td class="text-right mono font-bold">
                        ₱{{ number_format($product->tagged_price * $product->quantity_in_stock, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 16pt; color: #94a3b8;">
                        No inventory products recorded matching the selected filter.
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
                <div class="sign-title">Prepared By (Inventory Custodian)</div>
            </td>
            <td style="width: 10%;"></td>
            <td class="sign-box">
                <div class="sign-line"></div>
                <div class="sign-title">Verified By (Manager)</div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td>Wella Metal Corporation &bull; Internal Inventory Audit</td>
                <td style="text-align: right;">Total Products: {{ $products->count() }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
