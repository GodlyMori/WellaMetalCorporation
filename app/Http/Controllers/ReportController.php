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

    /**
     * Export Layaway Portfolio & Receivables to PDF
     */
    public function exportLayawaysPdf(Request $request)
    {
        $status = $request->query('status', 'active');
        $query = Sale::with(['items.product', 'customer', 'creator'])->where('is_archived', false);

        if ($status === 'active') {
            $query->where('status', 'layaway');
        } elseif ($status === 'near_due') {
            $query->where('status', 'layaway')
                  ->whereNotNull('layaway_expires_at')
                  ->whereDate('layaway_expires_at', '<=', now()->addDays(14))
                  ->whereDate('layaway_expires_at', '>=', now());
        } elseif ($status === 'overdue') {
            $query->where('status', 'layaway')
                  ->whereNotNull('layaway_expires_at')
                  ->whereDate('layaway_expires_at', '<', now());
        } elseif ($status === 'settled') {
            $query->where('status', 'completed')
                  ->where('initial_deposit', '>', 0);
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled')
                  ->where('initial_deposit', '>', 0);
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $contracts = $query->orderBy('sale_date', 'desc')->get();

        $totalReceivables = (float)$contracts->where('status', 'layaway')->sum('remaining_balance');
        $totalPaid = (float)$contracts->sum('amount_paid');
        $activeCount = $contracts->where('status', 'layaway')->count();
        $totalContractValue = (float)$contracts->sum('amount');

        $pdf = Pdf::loadView('reports.pdf.layaways', [
            'contracts' => $contracts,
            'totalReceivables' => $totalReceivables,
            'totalPaid' => $totalPaid,
            'activeCount' => $activeCount,
            'totalContractValue' => $totalContractValue,
            'statusFilter' => $status,
            'generatedAt' => now()->format('F j, Y - g:i A'),
        ]);

        return $pdf->download('wella-metal-layaway-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Master Comprehensive Audit Report (Sales, Inventory, Layaways)
     */
    public function exportAllPdf(Request $request)
    {
        // 1. Sales & Orders Data
        $sales = Sale::with(['items.product', 'customer', 'creator'])
            ->where('is_archived', false)
            ->orderBy('sale_date', 'desc')
            ->get();
        $totalSalesRevenue = (float)$sales->where('status', 'completed')->sum('amount') 
            + (float)$sales->where('status', 'layaway')->sum('amount_paid');
        $completedSalesCount = $sales->where('status', 'completed')->count();
        $totalDiscounts = (float)$sales->sum('discount_amount');
        $revenueTransactionsCount = $sales->whereIn('status', ['completed', 'layaway'])->count();
        $averageOrderValue = $revenueTransactionsCount > 0 ? ($totalSalesRevenue / $revenueTransactionsCount) : 0.0;

        // 2. Inventory Valuation Data
        $products = Product::where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();
        $totalStockUnits = (int)$products->sum('quantity_in_stock');
        $totalValuation = (float)$products->sum(fn($p) => $p->tagged_price * $p->quantity_in_stock);
        $lowStockCount = $products->where('quantity_in_stock', '<=', 4)->count();

        // 3. Layaway Receivables Data
        $layaways = Sale::with(['items.product', 'customer', 'creator'])
            ->where('is_archived', false)
            ->where('status', 'layaway')
            ->orderBy('sale_date', 'desc')
            ->get();
        $totalLayawayReceivables = (float)$layaways->sum('remaining_balance');
        $totalLayawayCollected = (float)$layaways->sum('amount_paid');
        $activeLayawaysCount = $layaways->count();
        $overdueLayawaysCount = $layaways->filter(fn($c) => $c->layaway_expires_at && $c->layaway_expires_at->isPast())->count();

        $pdf = Pdf::loadView('reports.pdf.all', [
            // Sales
            'sales' => $sales,
            'totalSalesRevenue' => $totalSalesRevenue,
            'completedSalesCount' => $completedSalesCount,
            'totalDiscounts' => $totalDiscounts,
            'averageOrderValue' => $averageOrderValue,

            // Inventory
            'products' => $products,
            'totalStockUnits' => $totalStockUnits,
            'totalValuation' => $totalValuation,
            'lowStockCount' => $lowStockCount,

            // Layaways
            'layaways' => $layaways,
            'totalLayawayReceivables' => $totalLayawayReceivables,
            'totalLayawayCollected' => $totalLayawayCollected,
            'activeLayawaysCount' => $activeLayawaysCount,
            'overdueLayawaysCount' => $overdueLayawaysCount,

            // Metadata
            'generatedAt' => now()->format('F j, Y - g:i A'),
        ]);

        return $pdf->download('wella-metal-master-audit-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
