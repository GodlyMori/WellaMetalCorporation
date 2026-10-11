<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
            if (!\Illuminate\Support\Facades\Schema::hasTable('stock_requests') || !\Illuminate\Support\Facades\Schema::hasColumn('sales', 'customer_email')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Silently fall back if database connection is not yet initialized
        }

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $pendingCount = 0;
            $pendingList = collect();

            if ($user && ($user->hasRole('admin') || $user->hasRole('manager'))) {
                try {
                    if (\Illuminate\Support\Facades\Schema::hasTable('stock_requests')) {
                        $pendingCount = \App\Models\StockRequest::where('status', 'pending')->count();
                        $pendingList = \App\Models\StockRequest::with(['product', 'requester'])
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
