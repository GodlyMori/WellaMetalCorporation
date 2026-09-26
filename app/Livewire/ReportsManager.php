<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsManager extends Component
{
    use WithPagination;

    public string $activeTab = 'sales'; // 'sales', 'inventory', 'transfers'

    // Sales filters
    public string $datePreset = 'this_month'; // 'all', 'this_month', 'this_week', 'today', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $salesStatus = 'ALL';
    public string $salesSearch = '';

    // Inventory filters
    public string $inventoryCategory = 'ALL';
    public string $stockFilter = 'all'; // 'all', 'in_stock', 'low_stock', 'out_of_stock'
    public string $inventorySearch = '';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedDatePreset($preset)
    {
        if ($preset === 'today') {
            $this->startDate = now()->toDateString();
            $this->endDate = now()->toDateString();
        } elseif ($preset === 'this_week') {
            $this->startDate = now()->startOfWeek()->toDateString();
            $this->endDate = now()->endOfWeek()->toDateString();
        } elseif ($preset === 'this_month') {
            $this->startDate = now()->startOfMonth()->toDateString();
            $this->endDate = now()->endOfMonth()->toDateString();
        } else {
            $this->startDate = null;
            $this->endDate = null;
        }
        $this->resetPage();
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        // 1. Sales Report Query & Metrics
        $salesQuery = Sale::query()->where('is_archived', false);

        if ($this->startDate) {
            $salesQuery->whereDate('sale_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $salesQuery->whereDate('sale_date', '<=', $this->endDate);
        }
        if ($this->salesStatus !== 'ALL') {
            $salesQuery->where('status', $this->salesStatus);
        }
        if (!empty($this->salesSearch)) {
            $salesQuery->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->salesSearch . '%')
                  ->orWhere('product_name', 'like', '%' . $this->salesSearch . '%')
                  ->orWhere('sale_number', 'like', '%' . $this->salesSearch . '%');
            });
        }

        $allFilteredSales = (clone $salesQuery)->get();
        $totalSalesRevenue = $allFilteredSales->where('status', 'completed')->sum('amount') 
            + $allFilteredSales->where('status', 'layaway')->sum('amount_paid');
        $completedTransactionsCount = $allFilteredSales->where('status', 'completed')->count();
        $totalTransactionsCount = $allFilteredSales->count();
        $averageOrderValue = $completedTransactionsCount > 0 ? ($totalSalesRevenue / $completedTransactionsCount) : 0;

        $paginatedSales = $salesQuery->orderBy('sale_date', 'desc')->paginate(10, ['*'], 'salesPage');

        // 2. Inventory Report Query & Metrics
        $invQuery = Product::where('status', 'active');

        if ($this->inventoryCategory !== 'ALL') {
            $invQuery->where('category', $this->inventoryCategory);
        }

        if ($this->stockFilter === 'low_stock') {
            $invQuery->where('quantity_in_stock', '>', 0)->where('quantity_in_stock', '<=', 4);
        } elseif ($this->stockFilter === 'out_of_stock') {
            $invQuery->where('quantity_in_stock', '<=', 0);
        } elseif ($this->stockFilter === 'in_stock') {
            $invQuery->where('quantity_in_stock', '>', 4);
        }

        if (!empty($this->inventorySearch)) {
            $invQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->inventorySearch . '%')
                  ->orWhere('description', 'like', '%' . $this->inventorySearch . '%');
            });
        }

        $allFilteredProducts = (clone $invQuery)->get();
        $totalStockUnits = $allFilteredProducts->sum('quantity_in_stock');
        $totalInventoryValuation = $allFilteredProducts->sum(fn($p) => $p->tagged_price * $p->quantity_in_stock);
        $totalLowStockItems = Product::where('status', 'active')->where('quantity_in_stock', '<=', 4)->count();

        $paginatedProducts = $invQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'invPage');

        // 3. Transfers / Restock Report Query
        $transfers = StockTransfer::with(['items.product', 'receiver'])
            ->orderBy('date_received', 'desc')
            ->paginate(10, ['*'], 'transferPage');

        return view('livewire.reports-manager', [
            'sales' => $paginatedSales,
            'totalSalesRevenue' => $totalSalesRevenue,
            'completedTransactionsCount' => $completedTransactionsCount,
            'totalTransactionsCount' => $totalTransactionsCount,
            'averageOrderValue' => $averageOrderValue,

            'products' => $paginatedProducts,
            'totalStockUnits' => $totalStockUnits,
            'totalInventoryValuation' => $totalInventoryValuation,
            'totalLowStockItems' => $totalLowStockItems,

            'transfers' => $transfers,
        ]);
    }
}
