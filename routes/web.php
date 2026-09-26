<?php

use App\Http\Controllers\ReportController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('admin/dashboard', 'dashboard')->name('admin.dashboard');
    Route::view('secretary/workspace', 'dashboard')->name('secretary.workspace');

    Route::view('inventory', 'inventory')->name('inventory');
    Route::view('sales', 'sales')->name('sales');
    Route::view('promotions', 'promotions')->name('promotions');

    Route::view('reports', 'reports')->name('reports');
    Route::get('reports/export/sales-csv', [ReportController::class, 'exportSalesCsv'])->name('reports.export.sales-csv');
    Route::get('reports/export/inventory-csv', [ReportController::class, 'exportInventoryCsv'])->name('reports.export.inventory-csv');
    Route::get('reports/export/sales-pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.export.sales-pdf');
    Route::get('reports/export/inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('reports.export.inventory-pdf');

    Route::get('analytics', function () {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'Access restricted: Analytics & Business Intelligence is strictly reserved for System Administrators.');
        }
        return view('analytics');
    })->name('analytics');

    Route::post('switch-role/{role}', function ($role) {
        $email = match($role) {
            'admin' => 'admin@wellametal.test',
            'manager' => 'manager@wellametal.test',
            'secretary' => 'secretary@wellametal.test',
            default => 'admin@wellametal.test',
        };

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
        }
        return back();
    })->name('switch-role');

    Route::post('logout', function (\App\Livewire\Actions\Logout $logout) {
        $logout();
        return redirect()->route('login');
    })->name('logout');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';