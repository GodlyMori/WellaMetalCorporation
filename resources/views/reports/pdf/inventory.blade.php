<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Inventory Valuation Report</title>
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
        .badge-instock {
            background-color: #ecfdf5;
            color: #065f46;
            border: 0.5pt solid #a7f3d0;
        }
        .badge-lowstock {
            background-color: #fffbeb;
            color: #92400e;
            border: 0.5pt solid #fde68a;
        }
        .badge-outstock {
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
                <div class="report-subtitle">Official Inventory Valuation & Stock Ledger</div>
            </td>
            <td style="text-align: right; vertical-align: middle;" class="meta-text">
                <div><strong>Category Filter:</strong> {{ strtoupper($category ?? 'ALL') }}</div>
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Active SKUs:</strong> {{ $products->count() }} items</div>
            </td>
        </tr>
    </table>

    <!-- Concise Top Summary Strip -->
    <table class="summary-strip">
        <tr>
            <td class="summary-cell" width="34%">
                <div class="summary-label">Total Inventory Asset Valuation</div>
                <div class="summary-value">₱{{ number_format($totalValuation, 2) }}</div>
            </td>
            <td class="summary-cell" width="33%">
                <div class="summary-label">Physical Units On Hand</div>
                <div class="summary-value">{{ number_format($totalStock) }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">units</span></div>
            </td>
            <td class="summary-cell" width="33%">
                <div class="summary-label">Reorder Attention (&le; 4 Units)</div>
                <div class="summary-value" style="color: {{ $lowStockCount > 0 ? '#b45309' : '#0f172a' }};">
                    {{ $lowStockCount }} <span style="font-size: 8pt; font-weight: normal; color: #64748b;">SKU alerts</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Inventory Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">SKU ID</th>
                <th width="28%">Product Name</th>
                <th width="16%">Category</th>
                <th width="14%" class="text-right">Unit Price (PHP)</th>
                <th width="10%" class="text-center">Stock</th>
                <th width="12%" class="text-center">Status</th>
                <th width="10%" class="text-right">Asset Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td class="mono"><strong>#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->description)
                            <div style="font-size: 6.5pt; color: #64748b;">{{ $product->description }}</div>
                        @endif
                    </td>
                    <td>{{ $product->category }}</td>
                    <td class="text-right mono">₱{{ number_format($product->tagged_price, 2) }}</td>
                    <td class="text-center mono">
                        <strong>{{ $product->quantity_in_stock }}</strong>
                    </td>
                    <td class="text-center">
                        @if($product->quantity_in_stock <= 0)
                            <span class="badge badge-outstock">Out of Stock</span>
                        @elseif($product->quantity_in_stock <= 4)
                            <span class="badge badge-lowstock">Low Stock</span>
                        @else
                            <span class="badge badge-instock">In Stock</span>
                        @endif
                    </td>
                    <td class="text-right mono">
                        <strong>₱{{ number_format($product->tagged_price * $product->quantity_in_stock, 2) }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16pt; color: #94a3b8;">
                        No products found matching the specified category filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Minimal Confidential Footer -->
    <div class="footer">
        Confidential Internal Document &bull; Wella Metal Corporation Management Information System &bull; Total Products Tracked: {{ $products->count() }}
    </div>

</body>
</html>
