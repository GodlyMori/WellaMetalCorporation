<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesReceiptController;
use App\Livewire\Actions\Logout;
use App\Livewire\Admin\BusinessIntelligence;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('admin/dashboard', 'dashboard')->name('admin.dashboard');
    Route::view('secretary/workspace', 'dashboard')->name('secretary.workspace');

    Route::view('inventory', 'inventory')->name('inventory');
    Route::view('inventory/add', 'inventory-add')->name('inventory.add');
    Route::post('inventory/batch', [InventoryController::class, 'storeBatch'])->name('inventory.batch');
    Route::put('inventory/products/{product}', [InventoryController::class, 'update'])->name('inventory.products.update');
    Route::view('sales', 'sales')->name('sales');
    Route::get('sales/receipt/{sale?}', [SalesReceiptController::class, 'show'])->name('sales.receipt');
    Route::get('receipt/{sale?}', [SalesReceiptController::class, 'show'])->name('receipt.show');
    Route::get('diagnostics/last-error', function () {
        $logPath = storage_path('logs/last_500.json');
        if (file_exists($logPath)) {
            return response(file_get_contents($logPath), 200, ['Content-Type' => 'application/json']);
        }

        return response()->json(['status' => 'clean', 'message' => 'No 500 errors logged.']);
    })->name('diagnostics.last-error');
    Route::view('layaways', 'layaways')->name('layaways');
    Route::view('customers', 'customers')->name('customers');
    Route::view('categories', 'categories')->name('categories');
    Route::view('promotions', 'promotions')->name('promotions');

    // Safe handling for GET /livewire/update to avoid 404s on browser re-fetch or reverse proxy redirect
    Route::get('livewire/update', function () {
        return redirect()->route('dashboard');
    });

    Route::view('reports', 'reports')->name('reports');
    Route::get('reports/export/periodic-pdf', [ReportController::class, 'exportPeriodicPdf'])->name('reports.export.periodic-pdf');
    Route::get('reports/export/sales-pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.export.sales-pdf');
    Route::get('reports/export/inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('reports.export.inventory-pdf');
    Route::get('reports/export/layaways-pdf', [ReportController::class, 'exportLayawaysPdf'])->name('reports.export.layaways-pdf');
    Route::get('reports/export/all-pdf', [ReportController::class, 'exportAllPdf'])->name('reports.export.all-pdf');

    Route::view('backup', 'backup')->name('backup');
    Route::get('backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('admin/business-intelligence', BusinessIntelligence::class)->name('admin.bi');
    });

    Route::post('logout', function (Logout $logout) {
        $logout();

        return redirect()->route('login');
    })->name('logout');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
