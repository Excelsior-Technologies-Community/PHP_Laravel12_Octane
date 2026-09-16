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