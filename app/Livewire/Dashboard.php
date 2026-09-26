<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;

class Dashboard extends Component
{
    public bool $revealRevenue = false;
    public bool $showStockModal = false;

    public function toggleRevenue()
    {
        $this->revealRevenue = !$this->revealRevenue;
    }

    public function openStockModal()
    {
        $this->showStockModal = true;
    }

    public function closeStockModal()
    {
        $this->showStockModal = false;
    }

    public function render()
    {
        $activeProducts = Product::where('status', 'active');
        $totalProducts = (clone $activeProducts)->count();
        $categoryCount = (clone $activeProducts)->distinct('category')->count('category');
        $stockUnits = (clone $activeProducts)->sum('quantity_in_stock');

        // Revenue calculations
        $completedSales = Sale::where('status', 'completed');
        $monthlyRevenue = (clone $completedSales)->sum('amount');
        $completedCount = (clone $completedSales)->count();
        $avgOrder = $completedCount > 0 ? ($monthlyRevenue / $completedCount) : 0;

        // Recent 6 sales
        $recentSales = Sale::orderBy('sale_date', 'desc')->take(6)->get();

        // Top 5 Products
        $topProducts = [
            ['rank' => '#1', 'name' => 'Milano Sectional Sofa', 'amount' => 5697, 'pct' => 90, 'color' => '#3b82f6'],
            ['rank' => '#2', 'name' => 'Hampton Walk-In Closet', 'amount' => 3200, 'pct' => 60, 'color' => '#10b981'],
            ['rank' => '#3', 'name' => 'Parma Dining Table', 'amount' => 3150, 'pct' => 58, 'color' => '#ea580c'],
            ['rank' => '#4', 'name' => 'Como 2-Door Armoire', 'amount' => 2850, 'pct' => 52, 'color' => '#15803d'],
            ['rank' => '#5', 'name' => 'Luxe Recliner Sofa', 'amount' => 2199, 'pct' => 45, 'color' => '#7c3aed'],
        ];

        // Stock by Category
        $categories = [
            ['name' => 'Sofa', 'count' => Product::where('status', 'active')->where('category', 'Sofa')->sum('quantity_in_stock'), 'color' => '#3b82f6'],
            ['name' => 'Dining Table', 'count' => Product::where('status', 'active')->where('category', 'Dining Table')->sum('quantity_in_stock'), 'color' => '#ea580c'],
            ['name' => 'Closet', 'count' => Product::where('status', 'active')->where('category', 'Closet')->sum('quantity_in_stock'), 'color' => '#10b981'],
        ];

        // Stock Alerts (Low Stock <= 4)
        $stockAlerts = Product::where('status', 'active')
            ->where('quantity_in_stock', '<=', 4)
            ->orderBy('quantity_in_stock', 'asc')
            ->get();

        return view('livewire.dashboard', [
            'totalProducts' => $totalProducts,
            'categoryCount' => $categoryCount,
            'stockUnits' => $stockUnits,
            'monthlyRevenue' => $monthlyRevenue,
            'avgOrder' => $avgOrder,
            'recentSales' => $recentSales,
            'topProducts' => $topProducts,
            'categories' => $categories,
            'stockAlerts' => $stockAlerts,
        ]);
    }
}
