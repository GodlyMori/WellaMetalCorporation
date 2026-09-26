<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransfer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Export Sales to CSV
     */
    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');

        $query = Sale::with('creator')->where('is_archived', false);

        if ($startDate) {
            $query->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sale_date', '<=', $endDate);
        }
        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wella-metal-sales-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            
            // CSV BOM for UTF-8 Excel support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Title & Metadata
            fputcsv($handle, ['WELLA METAL CORPORATION - SALES REPORT']);
            fputcsv($handle, ['Exported Date', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Total Records', $sales->count()]);
            fputcsv($handle, ['Total Cash Collected (PHP)', $sales->where('status', 'completed')->sum('amount') + $sales->where('status', 'layaway')->sum('amount_paid')]);
            fputcsv($handle, []);

            // Column Headers
            fputcsv($handle, [
                'Order #',
                'Customer Name',
                'Customer Contact',
                'Customer Address',
                'Product / Items',
                'Promo Event',
                'Discount (PHP)',
                'Final Amount (PHP)',
                'Amount Paid (PHP)',
                'Remaining Balance (PHP)',
                'Lay-Away Expiry',
                'Sale Date',
                'Status',
                'Recorded By',
            ]);

            // Data Rows
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->sale_number,
                    $sale->customer_name,
                    $sale->customer_phone ?: '—',
                    $sale->customer_address ?: '—',
                    $sale->product_name,
                    $sale->promo_name ?: '—',
                    number_format($sale->discount_amount ?? 0, 2, '.', ''),
                    number_format($sale->amount, 2, '.', ''),
                    number_format($sale->amount_paid, 2, '.', ''),
                    number_format($sale->remaining_balance, 2, '.', ''),
                    $sale->layaway_expires_at ? $sale->layaway_expires_at->format('Y-m-d') : '—',
                    $sale->sale_date->format('Y-m-d'),
                    ucfirst($sale->status),
                    $sale->creator?->name ?? 'System',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Inventory to CSV
     */
    public function exportInventoryCsv(Request $request): StreamedResponse
    {
        $category = $request->query('category');
        $query = Product::where('status', 'active');

        if ($category && $category !== 'ALL') {
            $query->where('category', $category);
        }

        $products = $query->orderBy('id', 'asc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wella-metal-inventory-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');
            
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['WELLA METAL CORPORATION - INVENTORY VALUATION REPORT']);
            fputcsv($handle, ['Exported Date', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Total Active Products', $products->count()]);
            fputcsv($handle, ['Total Stock In Hand', $products->sum('quantity_in_stock')]);
            fputcsv($handle, ['Total Asset Valuation (PHP)', $products->sum(fn($p) => $p->tagged_price * $p->quantity_in_stock)]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Item ID',
                'Product Name',
                'Category',
                'Material / Description',
                'Tagged Price (PHP)',
                'Stock Count',
                'Asset Valuation (PHP)',
                'Stock Status',
            ]);

            foreach ($products as $p) {
                $valuation = $p->tagged_price * $p->quantity_in_stock;
                fputcsv($handle, [
                    $p->formatted_id,
                    $p->name,
                    $p->category,
                    $p->description,
                    number_format($p->tagged_price, 2, '.', ''),
                    $p->quantity_in_stock,
                    number_format($valuation, 2, '.', ''),
                    $p->stock_status,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Sales to PDF
     */
    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');

        $query = Sale::with('creator')->where('is_archived', false);

        if ($startDate) {
            $query->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sale_date', '<=', $endDate);
        }
        if ($status && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $totalRevenue = $sales->where('status', 'completed')->sum('amount') + $sales->where('status', 'layaway')->sum('amount_paid');
        $completedCount = $sales->where('status', 'completed')->count();
        $totalDiscounts = $sales->sum('discount_amount');

        $pdf = Pdf::loadView('reports.pdf.sales', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'completedCount' => $completedCount,
            'totalDiscounts' => $totalDiscounts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now()->format('F j, Y - g:i A'),
        ]);

        return $pdf->download('wella-metal-sales-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Inventory to PDF
     */
    public function exportInventoryPdf(Request $request)
    {
        $category = $request->query('category');
        $query = Product::where('status', 'active');

        if ($category && $category !== 'ALL') {
            $query->where('category', $category);
        }

        $products = $query->orderBy('id', 'asc')->get();
        $totalStock = $products->sum('quantity_in_stock');
        $totalValuation = $products->sum(fn($p) => $p->tagged_price * $p->quantity_in_stock);
        $lowStockCount = $products->where('quantity_in_stock', '<=', 4)->count();

        $pdf = Pdf::loadView('reports.pdf.inventory', [
            'products' => $products,
            'totalStock' => $totalStock,
            'totalValuation' => $totalValuation,
            'lowStockCount' => $lowStockCount,
            'category' => $category ?: 'ALL',
            'generatedAt' => now()->format('F j, Y - g:i A'),
        ]);

        return $pdf->download('wella-metal-inventory-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
