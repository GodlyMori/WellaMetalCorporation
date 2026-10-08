<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
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
        $totalRevenue = (float)$sales->where('status', 'completed')->sum('amount') + (float)$sales->where('status', 'layaway')->sum('amount_paid');
        $completedCount = $sales->where('status', 'completed')->count();
        $layawayCount = $sales->where('status', 'layaway')->count();
        $totalDiscounts = (float)$sales->sum('discount_amount');
        $revenueTransactionsCount = $sales->whereIn('status', ['completed', 'layaway'])->count();
        $averageOrderValue = $revenueTransactionsCount > 0 ? ($totalRevenue / $revenueTransactionsCount) : 0.0;

        $pdf = Pdf::loadView('reports.pdf.sales', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'completedCount' => $completedCount,
            'layawayCount' => $layawayCount,
            'totalDiscounts' => $totalDiscounts,
            'averageOrderValue' => $averageOrderValue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statusFilter' => $status ?: 'ALL',
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
        $totalStock = (int)$products->sum('quantity_in_stock');
        $totalValuation = (float)$products->sum(fn($p) => $p->tagged_price * $p->quantity_in_stock);
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
