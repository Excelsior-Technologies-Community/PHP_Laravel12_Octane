<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OctaneMonitorController extends Controller
{
    /**
     * Display the Octane performance dashboard.
     */
    public function index(Request $request)
    {
        $totalRequests = PerformanceMetric::count();

        $averageResponseTime = PerformanceMetric::avg('duration_ms') ?? 0;

        $slowRequests = PerformanceMetric::where('is_slow', true)->count();

        $errorRequests = PerformanceMetric::where('is_error', true)->count();

        $maxResponseTime = PerformanceMetric::max('duration_ms') ?? 0;

        $averageMemory = PerformanceMetric::avg('memory_usage_bytes') ?? 0;

        $peakMemory = PerformanceMetric::max('peak_memory_usage_bytes') ?? 0;

        $recentMetrics = PerformanceMetric::query()
            ->with('user')
            ->when(
                $request->filled('method'),
                fn ($query) =>
                    $query->where('method', $request->method)
            )
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where('status_code', $request->status)
            )
            ->when(
                $request->filled('slow'),
                fn ($query) =>
                    $query->where('is_slow', true)
            )
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request->search;

                    $query->where(function ($q) use ($search) {
                        $q->where('path', 'like', "%{$search}%")
                            ->orWhere('method', 'like', "%{$search}%");
                    });
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $methodStats = PerformanceMetric::query()
            ->select(
                'method',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(duration_ms) as average_duration'),
                DB::raw('MAX(duration_ms) as maximum_duration')
            )
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $pathStats = PerformanceMetric::query()
            ->select(
                'path',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(duration_ms) as average_duration'),
                DB::raw('MAX(duration_ms) as maximum_duration')
            )
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $runtime = $this->runtimeInformation();

        return view('octane.dashboard', compact(
            'totalRequests',
            'averageResponseTime',
            'slowRequests',
            'errorRequests',
            'maxResponseTime',
            'averageMemory',
            'peakMemory',
            'recentMetrics',
            'methodStats',
            'pathStats',
            'runtime'
        ));
    }

    /**
     * Display runtime and Octane health information.
     */
    private function runtimeInformation(): array
    {
        $opcache = function_exists('opcache_get_status')
            ? @opcache_get_status(false)
            : false;

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),

            'octane_server' => config(
                'octane.server',
                env('OCTANE_SERVER', 'frankenphp')
            ),

            'octane_workers' => config(
                'octane.workers',
                'auto'
            ),

            'task_workers' => config(
                'octane.task_workers',
                0
            ),

            'max_execution_time' => ini_get(
                'max_execution_time'
            ),

            'memory_limit' => ini_get(
                'memory_limit'
            ),

            'current_memory' => memory_get_usage(true),

            'peak_memory' => memory_get_peak_usage(true),

            'opcache_enabled' => $opcache
                ? (bool) ($opcache['opcache_statistics']['num_cached_scripts'] ?? 0)
                : false,

            'opcache_cached_scripts' => $opcache
                ? ($opcache['opcache_statistics']['num_cached_scripts'] ?? 0)
                : 0,

            'environment' => app()->environment(),

            'debug_mode' => config('app.debug'),
        ];
    }
}