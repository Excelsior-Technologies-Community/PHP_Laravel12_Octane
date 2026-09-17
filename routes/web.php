<?php

use App\Http\Controllers\OctaneMonitorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/octane-monitor', [
    OctaneMonitorController::class,
    'index',
])->name('octane.monitor');

/*
|--------------------------------------------------------------------------
| Export
|--------------------------------------------------------------------------
*/

Route::get('/octane-monitor/export', [
    OctaneMonitorController::class,
    'export',
])->name('octane.monitor.export');

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

Route::post('/octane-monitor/bulk-delete', [
    OctaneMonitorController::class,
    'bulkDelete',
])->name('octane.monitor.bulk-delete');

/*
|--------------------------------------------------------------------------
| Delete All
|--------------------------------------------------------------------------
*/

Route::delete('/octane-monitor/delete-all', [
    OctaneMonitorController::class,
    'deleteAll',
])->name('octane.monitor.delete-all');