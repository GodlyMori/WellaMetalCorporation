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
            if (!\Illuminate\Support\Facades\Schema::hasTable('stock_requests')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Silently fall back if database connection is not yet initialized
        }
    }
}
