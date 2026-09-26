<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wella Metal Corporation - Inventory Valuation Report</title>
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
        .badge-instock { background-color: #dcfce7; color: #15803d; }
        .badge-lowstock { background-color: #fef3c7; color: #b45309; }
        .badge-outstock { background-color: #fee2e2; color: #b91c1c; }
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
                <div class="subtitle">Official Inventory Stock Valuation & Asset Report</div>
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
            <td class="text-right"><strong>Category Filter:</strong> {{ $category }}</td>
        </tr>
    </table>

    <table class="cards-table">
        <tr>
            <td class="card" width="33%">
                <div class="card-num">₱{{ number_format($totalValuation, 2) }}</div>
                <div class="card-label">Total Asset Valuation</div>
            </td>
            <td class="card" width="33%">
                <div class="card-num">{{ $totalStock }} units</div>
                <div class="card-label">Physical Units On Hand</div>
            </td>
            <td class="card" width="33%">
                <div class="card-num" style="color: {{ $lowStockCount > 0 ? '#d97706' : '#142259' }}">{{ $lowStockCount }} items</div>
                <div class="card-label">Items Low On Stock</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="26%">Product Name</th>
                <th width="16%">Category</th>
                <th width="16%" class="text-right">Unit Price</th>
                <th width="12%" class="text-right">Units</th>
                <th width="20%" class="text-right">Asset Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->formatted_id }}</strong></td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td class="text-right">₱{{ number_format($product->tagged_price, 2) }}</td>
                    <td class="text-right">
                        <strong>{{ $product->quantity_in_stock }}</strong>
                    </td>
                    <td class="text-right"><strong>₱{{ number_format($product->tagged_price * $product->quantity_in_stock, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No products found matching this criteria.
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
