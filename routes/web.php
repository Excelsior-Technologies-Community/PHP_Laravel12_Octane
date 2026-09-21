<?php

use App\Http\Controllers\OctaneBenchmarkController;
use App\Http\Controllers\OctaneCacheController;
use App\Http\Controllers\OctaneConcurrentController;
use App\Http\Controllers\OctaneMonitorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('octane.monitor');
});

/*
|--------------------------------------------------------------------------
| Octane Performance Monitor Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/octane-monitor', [OctaneMonitorController::class, 'index'])->name('octane.monitor');
Route::get('/octane-monitor/export', [OctaneMonitorController::class, 'export'])->name('octane.monitor.export');
Route::post('/octane-monitor/bulk-delete', [OctaneMonitorController::class, 'bulkDelete'])->name('octane.monitor.bulk-delete');
Route::delete('/octane-monitor/delete-all', [OctaneMonitorController::class, 'deleteAll'])->name('octane.monitor.delete-all');

/*
|--------------------------------------------------------------------------
| Octane Concurrency & Async Background Engine
|--------------------------------------------------------------------------
*/
Route::prefix('octane')->name('octane.')->group(function () {
    // 1. Concurrent Tasks & Async Engine
    Route::get('/concurrent', [OctaneConcurrentController::class, 'index'])->name('concurrent');
    Route::get('/concurrent/run', [OctaneConcurrentController::class, 'runBenchmark'])->name('concurrent.run');
    Route::post('/background/run', [OctaneConcurrentController::class, 'runBackgroundTask'])->name('background.run');

    // 2. In-Memory Cache & State Warmup
    Route::get('/cache', [OctaneCacheController::class, 'index'])->name('cache');
    Route::post('/cache/warmup', [OctaneCacheController::class, 'triggerWarmup'])->name('cache.warmup');
    Route::post('/cache/benchmark', [OctaneCacheController::class, 'runBenchmark'])->name('cache.benchmark');
    Route::post('/cache/flush', [OctaneCacheController::class, 'flushCache'])->name('cache.flush');

    // 3. Live Stress Test & Benchmarking Suite
    Route::get('/benchmark', [OctaneBenchmarkController::class, 'index'])->name('benchmark');
    Route::post('/benchmark/run', [OctaneBenchmarkController::class, 'runStressTest'])->name('benchmark.run');
    Route::get('/ping', [OctaneBenchmarkController::class, 'ping'])->name('ping');
});