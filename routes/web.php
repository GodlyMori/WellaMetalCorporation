<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Livewire\Actions\Logout;
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
    Route::post('inventory/batch', [InventoryController::class, 'storeBatch'])->name('inventory.batch');
    Route::put('inventory/products/{product}', [InventoryController::class, 'update'])->name('inventory.products.update');
    Route::view('sales', 'sales')->name('sales');
    Route::view('layaways', 'layaways')->name('layaways');
    Route::view('customers', 'customers')->name('customers');
    Route::view('categories', 'categories')->name('categories');
    Route::view('promotions', 'promotions')->name('promotions');

    Route::view('reports', 'reports')->name('reports');
    Route::get('reports/export/periodic-pdf', [ReportController::class, 'exportPeriodicPdf'])->name('reports.export.periodic-pdf');
    Route::get('reports/export/sales-pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.export.sales-pdf');
    Route::get('reports/export/inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('reports.export.inventory-pdf');
    Route::get('reports/export/layaways-pdf', [ReportController::class, 'exportLayawaysPdf'])->name('reports.export.layaways-pdf');
    Route::get('reports/export/all-pdf', [ReportController::class, 'exportAllPdf'])->name('reports.export.all-pdf');

    Route::view('backup', 'backup')->name('backup');
    Route::get('backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');

    Route::post('switch-role/{role}', function ($role) {
        // 1. Strictly disabled in production
        if (app()->environment('production')) {
            abort(403, 'Role switching is strictly disabled in production environments.');
        }

        $currentUser = Auth::user();
        $isOriginalAdmin = session()->has('original_admin_id') && User::find(session('original_admin_id'))?->hasRole('admin');
        $isAdmin = $currentUser && $currentUser->hasRole('admin');

        // 2. Strict authorization: Only genuine admins or an admin currently testing a subordinate role may switch
        if (! $isAdmin && ! $isOriginalAdmin) {
            abort(403, 'Unauthorized: Role switching is strictly restricted to System Administrators.');
        }

        $email = match ($role) {
            'admin' => 'admin@wellametalcorp.com',
            'manager' => 'manager@wellametalcorp.com',
            'secretary' => 'secretary@wellametalcorp.com',
            default => null,
        };

        if (! $email) {
            return back()->with('error', 'Invalid role requested.');
        }

        $targetUser = User::where('email', $email)->first();
        if ($targetUser) {
            if ($role === 'admin') {
                session()->forget('original_admin_id');
            } elseif ($isAdmin && ! session()->has('original_admin_id')) {
                session(['original_admin_id' => $currentUser->id]);
            }
            Auth::login($targetUser);
        }

        return back();
    })->name('switch-role');

    Route::post('logout', function (Logout $logout) {
        $logout();

        return redirect()->route('login');
    })->name('logout');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
