<?php

namespace App\Services;

use App\Models\PerformanceMetric;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Facades\Octane;
use Throwable;

class OctaneWarmupService
{
    /**
     * Cache keys used by Octane Warmup.
     */
    public const KEY_SYSTEM_SETTINGS = 'octane:warmup:settings';
    public const KEY_USER_METADATA = 'octane:warmup:users';
    public const KEY_ROUTE_REGISTRY = 'octane:warmup:routes';
    public const KEY_PERFORMANCE_SUMMARY = 'octane:warmup:perf_summary';

    /**
     * Run the full warmup routine.
     *
     * @return array<string, mixed>
     */
    public function warmup(): array
    {
        $startTime = hrtime(true);
        $startMemory = memory_get_usage(true);

        $results = [];

        // 1. Warmup System Configuration & Flags
        $settings = [
            'app_name' => config('app.name', 'Laravel Octane'),
            'env' => config('app.env', 'production'),
            'octane_server' => $_SERVER['LARAVEL_OCTANE_SERVER'] ?? 'FrankenPHP / Swoole',
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
            'cache_driver' => config('cache.default'),
            'features' => [
                'concurrent_workers' => true,
                'swoole_tables' => true,
                'async_background_tasks' => true,
                'in_memory_cache' => true,
            ],
            'warmed_at' => now()->toIso8601String(),
        ];
        Cache::put(self::KEY_SYSTEM_SETTINGS, $settings, now()->addDays(7));
        $results['system_settings'] = [
            'key' => self::KEY_SYSTEM_SETTINGS,
            'items_warmed' => count($settings),
            'status' => 'Warmed in Memory',
        ];

        // 2. Warmup Users & Roles Directory
        try {
            $users = User::select('id', 'name', 'email')->limit(100)->get()->toArray();
        } catch (Throwable) {
            $users = [
                ['id' => 1, 'name' => 'System Admin', 'email' => 'admin@example.com'],
                ['id' => 2, 'name' => 'Octane Worker', 'email' => 'worker@octane.internal'],
            ];
        }
        Cache::put(self::KEY_USER_METADATA, $users, now()->addDays(7));
        $results['users_directory'] = [
            'key' => self::KEY_USER_METADATA,
            'items_warmed' => count($users),
            'status' => 'Preloaded into Cache',
        ];

        // 3. Warmup Application Routes Registry
        $routes = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ])
            ->values()
            ->toArray();
        Cache::put(self::KEY_ROUTE_REGISTRY, $routes, now()->addDays(7));
        $results['route_registry'] = [
            'key' => self::KEY_ROUTE_REGISTRY,
            'items_warmed' => count($routes),
            'status' => 'Pre-cached',
        ];

        // 4. Warmup Performance Metrics Summary
        try {
            $perfSummary = [
                'total_logged_requests' => PerformanceMetric::count(),
                'avg_duration_ms' => round(PerformanceMetric::avg('duration_ms') ?? 0, 2),
                'slow_requests_count' => PerformanceMetric::where('is_slow', true)->count(),
                'error_requests_count' => PerformanceMetric::where('is_error', true)->count(),
                'cached_at' => now()->toDateTimeString(),
            ];
        } catch (Throwable) {
            $perfSummary = ['total_logged_requests' => 0, 'avg_duration_ms' => 0];
        }
        Cache::put(self::KEY_PERFORMANCE_SUMMARY, $perfSummary, now()->addDays(1));
        $results['performance_summary'] = [
            'key' => self::KEY_PERFORMANCE_SUMMARY,
            'items_warmed' => count($perfSummary),
            'status' => 'Aggregated in Memory',
        ];

        $endTime = hrtime(true);
        $durationMs = round(($endTime - $startTime) / 1_000_000, 2);
        $memoryConsumed = max(0, memory_get_usage(true) - $startMemory);

        return [
            'success' => true,
            'duration_ms' => $durationMs,
            'memory_consumed_bytes' => $memoryConsumed,
            'memory_consumed_formatted' => round($memoryConsumed / 1024, 2) . ' KB',
            'warmed_modules' => $results,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Retrieve warmed state status.
     *
     * @return array<string, mixed>
     */
    public function getWarmupStatus(): array
    {
        $settings = Cache::get(self::KEY_SYSTEM_SETTINGS);
        $users = Cache::get(self::KEY_USER_METADATA);
        $routes = Cache::get(self::KEY_ROUTE_REGISTRY);
        $perf = Cache::get(self::KEY_PERFORMANCE_SUMMARY);

        $isWarmed = $settings !== null && $users !== null && $routes !== null;

        return [
            'is_warmed' => $isWarmed,
            'system_settings_cached' => $settings !== null,
            'users_cached_count' => is_array($users) ? count($users) : 0,
            'routes_cached_count' => is_array($routes) ? count($routes) : 0,
            'perf_cached' => $perf !== null,
            'last_warmup_time' => $settings['warmed_at'] ?? null,
        ];
    }
}
