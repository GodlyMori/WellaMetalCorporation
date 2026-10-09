<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\LayawayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ReportsManager extends Component
{
    use WithPagination;

    public string $activeTab = 'sales'; // 'sales', 'inventory', 'layaways'

    // Sales filters
    public string $datePreset = 'this_month'; // 'all', 'this_month', 'this_week', 'today', 'custom'
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $salesStatus = 'ALL'; // ALL, completed, layaway, pending, cancelled, ARCHIVED
    public string $salesSearch = '';

    // Layaway filters
    public string $layawayStatus = 'active'; // 'active', 'near_due', 'overdue', 'settled', 'cancelled', 'all'
    public string $layawaySearch = '';

    // Secretary Archive Authorization Modal
    public bool $showAuthModal = false;
    public ?int $pendingArchiveId = null;
    public ?int $authApproverId = null;
    public string $authPassword = '';
    public string $authReason = '';
    public string $authError = '';

    public string $successMessage = '';

    // Inventory filters
    public string $inventoryCategory = 'ALL';
    public string $stockFilter = 'all'; // 'all', 'in_stock', 'low_stock', 'out_of_stock'
    public string $inventorySearch = '';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage('salesPage');
        $this->resetPage('invPage');
        $this->resetPage('layawayPage');
    }

    public function setLayawayStatus(string $status)
    {
        $this->layawayStatus = $status;
        $this->resetPage('layawayPage');
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
        $this->resetPage('salesPage');
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function setSalesStatus(string $status)
    {
        $this->salesStatus = $status;
        $this->resetPage('salesPage');
    }


    public function processExpiredLayaways()
    {
        $service = new LayawayService();
        $processed = $service->processExpiredLayaways(Auth::id());

        if ($processed > 0) {
            $this->successMessage = "Processed {$processed} expired lay-away order(s). Reserved inventory has been restored.";
        } else {
            $this->successMessage = "No expired lay-away orders found.";
        }
    }

    public function archiveSale(int $id)
    {
        $user = Auth::user();
        $sale = Sale::findOrFail($id);
        $sale->update([
            'is_archived' => true,
            'archived_by' => $user?->id,
            'archive_approved_by' => $user?->id,
            'archive_reason' => 'Direct archive by ' . ($user?->name ?? 'User'),
        ]);
        $this->successMessage = "Sale record {$sale->sale_number} has been archived.";
    }

    public function restoreSale(int $id)
    {
        $sale = Sale::findOrFail($id);
        $sale->update(['is_archived' => false]);
        $this->successMessage = "Sale record {$sale->sale_number} has been restored.";
    }

    public function render()
    {
        // 1. Sales Report Query & Metrics
        $salesQuery = Sale::query()->with(['items.product', 'customer']);

        if ($this->salesStatus === 'ARCHIVED') {
            $salesQuery->where('is_archived', true);
        } else {
            $salesQuery->where('is_archived', false);
            if ($this->salesStatus !== 'ALL') {
                $salesQuery->where('status', $this->salesStatus);
            }
        }

        if ($this->startDate) {
            $salesQuery->whereDate('sale_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $salesQuery->whereDate('sale_date', '<=', $this->endDate);
        }
        if (!empty($this->salesSearch)) {
            $salesQuery->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->salesSearch . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->salesSearch . '%')
                  ->orWhere('product_name', 'like', '%' . $this->salesSearch . '%')
                  ->orWhere('sale_number', 'like', '%' . $this->salesSearch . '%');
            });
        }

        $allFilteredSales = (clone $salesQuery)->get();
        $validSalesForRevenue = $allFilteredSales->where('status', '!=', 'cancelled');
        $totalSalesRevenue = (float)$validSalesForRevenue->where('status', 'completed')->sum('amount') 
            + (float)$validSalesForRevenue->where('status', 'layaway')->sum('amount_paid');
        $completedTransactionsCount = $validSalesForRevenue->where('status', 'completed')->count();
        $totalTransactionsCount = $allFilteredSales->count();
        $revenueTransactionsCount = $validSalesForRevenue->whereIn('status', ['completed', 'layaway'])->count();
        $averageOrderValue = $revenueTransactionsCount > 0 ? ($totalSalesRevenue / $revenueTransactionsCount) : 0.0;

        $paginatedSales = $salesQuery->orderBy('sale_date', 'desc')->paginate(15, ['*'], 'salesPage');

        // Status counts
        $counts = [
            'all' => Sale::where('is_archived', false)->count(),
            'completed' => Sale::where('is_archived', false)->where('status', 'completed')->count(),
            'layaway' => Sale::where('is_archived', false)->where('status', 'layaway')->count(),
            'pending' => Sale::where('is_archived', false)->where('status', 'pending')->count(),
            'cancelled' => Sale::where('is_archived', false)->where('status', 'cancelled')->count(),
            'archived' => Sale::where('is_archived', true)->count(),
        ];

        // 2. Inventory Report Query & Metrics
        $invQuery = Product::where('status', 'active');

        if ($this->inventoryCategory !== 'ALL') {
            $invQuery->where('category', $this->inventoryCategory);
        }

        if ($this->stockFilter === 'low_stock') {
            $invQuery->where('quantity_in_stock', '>', 0)->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold');
        } elseif ($this->stockFilter === 'out_of_stock') {
            $invQuery->where('quantity_in_stock', '<=', 0);
        } elseif ($this->stockFilter === 'in_stock') {
            $invQuery->whereColumn('quantity_in_stock', '>', 'low_stock_threshold');
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
        $totalLowStockItems = Product::where('status', 'active')->where('quantity_in_stock', '>', 0)->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold')->count();

        $paginatedProducts = $invQuery->orderBy('id', 'asc')->paginate(10, ['*'], 'invPage');

        // 3. Layaway Report Query & Metrics
        $layawayQuery = Sale::query()->with(['items.product', 'customer', 'creator'])->where('is_archived', false);

        if ($this->layawayStatus === 'active') {
            $layawayQuery->where('status', 'layaway');
        } elseif ($this->layawayStatus === 'near_due') {
            $layawayQuery->where('status', 'layaway')
                ->whereNotNull('layaway_expires_at')
                ->whereDate('layaway_expires_at', '<=', now()->addDays(14))
                ->whereDate('layaway_expires_at', '>=', now());
        } elseif ($this->layawayStatus === 'overdue') {
            $layawayQuery->where('status', 'layaway')
                ->whereNotNull('layaway_expires_at')
                ->whereDate('layaway_expires_at', '<', now());
        } elseif ($this->layawayStatus === 'settled') {
            $layawayQuery->where('status', 'completed')
                ->where('initial_deposit', '>', 0);
        } elseif ($this->layawayStatus === 'cancelled') {
            $layawayQuery->where('status', 'cancelled')
                ->where('initial_deposit', '>', 0);
        } elseif ($this->layawayStatus !== 'all') {
            $layawayQuery->where('status', $this->layawayStatus);
        }

        if (!empty($this->layawaySearch)) {
            $layawayQuery->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->layawaySearch . '%')
                  ->orWhere('customer_phone', 'like', '%' . $this->layawaySearch . '%')
                  ->orWhere('sale_number', 'like', '%' . $this->layawaySearch . '%');
            });
        }

        $paginatedLayaways = $layawayQuery->orderBy('sale_date', 'desc')->paginate(10, ['*'], 'layawayPage');

        $approvers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();

        $totalLayawayBalance = (float)Sale::where('is_archived', false)->where('status', 'layaway')->sum('remaining_balance');
        $layawayActiveCount = Sale::where('is_archived', false)->where('status', 'layaway')->count();
        $layawayOverdueCount = Sale::where('is_archived', false)->where('status', 'layaway')
            ->whereNotNull('layaway_expires_at')
            ->whereDate('layaway_expires_at', '<', now())
            ->count();

        $loadedCategories = Category::where('is_active', true)->orderBy('name')->pluck('name')->toArray();
        if (empty($loadedCategories)) {
            $dbCategories = Product::where('status', 'active')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->toArray();
            $existingCategories = array_values(array_unique(array_merge(Product::CATEGORIES, $dbCategories)));
        } else {
            $existingCategories = $loadedCategories;
        }

        return view('livewire.reports-manager', [
            'sales' => $paginatedSales,
            'totalSalesRevenue' => $totalSalesRevenue,
            'completedTransactionsCount' => $completedTransactionsCount,
            'totalTransactionsCount' => $totalTransactionsCount,
            'averageOrderValue' => $averageOrderValue,
            'totalLayawayBalance' => $totalLayawayBalance,
            'counts' => $counts,
            'approvers' => $approvers,

            'products' => $paginatedProducts,
            'totalStockUnits' => $totalStockUnits,
            'totalInventoryValuation' => $totalInventoryValuation,
            'totalLowStockItems' => $totalLowStockItems,
            'existingCategories' => $existingCategories,

            'layaways' => $paginatedLayaways,
            'layawayActiveCount' => $layawayActiveCount,
            'layawayOverdueCount' => $layawayOverdueCount,
        ]);
    }
}
