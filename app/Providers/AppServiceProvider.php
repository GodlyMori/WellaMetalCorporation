<?php

namespace App\Providers;

use App\Models\StockRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || app()->environment('production')) {
            URL::forceScheme('https');
        }

        try {
            if (! Schema::hasTable('stock_requests')
                || ! Schema::hasColumn('sales', 'customer_email')
                || ! Schema::hasTable('promotion_redemptions')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Silently fall back if database connection is not yet initialized
        }

        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            $pendingCount = 0;
            $pendingList = collect();

            if ($user && ($user->hasRole('admin') || $user->hasRole('manager'))) {
                try {
                    if (Schema::hasTable('stock_requests')) {
                        $pendingCount = StockRequest::where('status', 'pending')->count();
                        $pendingList = StockRequest::with(['product', 'requester'])
                            ->where('status', 'pending')
                            ->orderBy('id', 'desc')
                            ->take(5)
                            ->get();
                    }
                } catch (\Throwable $e) {
                    // Fail gracefully if DB connection isn't ready
                }
            }

            $view->with([
                'navPendingStockRequestsCount' => $pendingCount,
                'navPendingStockRequestsList' => $pendingList,
            ]);
        });
    }
}
