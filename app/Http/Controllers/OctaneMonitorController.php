<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OctaneMonitorController extends Controller
{
    /**
     * Display the Octane performance dashboard.
     */
    public function index(Request $request)
    {
        $baseQuery = $this->filteredQuery($request);

        /*
        |--------------------------------------------------------------------------
        | Main Statistics
        |--------------------------------------------------------------------------
        */

        $totalRequests = (clone $baseQuery)->count();

        $averageResponseTime =
            (clone $baseQuery)->avg('duration_ms') ?? 0;

        $slowRequests =
            (clone $baseQuery)->where('is_slow', true)->count();

        $errorRequests =
            (clone $baseQuery)->where('is_error', true)->count();

        $maxResponseTime =
            (clone $baseQuery)->max('duration_ms') ?? 0;

        $averageMemory =
            (clone $baseQuery)->avg('memory_usage_bytes') ?? 0;

        $peakMemory =
            (clone $baseQuery)->max('peak_memory_usage_bytes') ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Performance Analytics
        |--------------------------------------------------------------------------
        */

        $successRequests =
            (clone $baseQuery)
                ->whereBetween('status_code', [200, 399])
                ->count();

        $errorCount =
            (clone $baseQuery)
                ->whereBetween('status_code', [400, 599])
                ->count();

        $successRate = $totalRequests > 0
            ? ($successRequests / $totalRequests) * 100
            : 0;

        $errorRate = $totalRequests > 0
            ? ($errorCount / $totalRequests) * 100
            : 0;

        $slowRate = $totalRequests > 0
            ? ($slowRequests / $totalRequests) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Request History
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input('per_page', 15);

        if (! in_array($perPage, [5, 15, 30, 50, 100], true)) {
            $perPage = 5;
        }

        $recentMetrics = (clone $baseQuery)
            ->with('user')
            ->latest('created_at')
            ->when(
                $request->filled('sort'),
                function ($query) use ($request) {
                    $query->reorder();

                    match ($request->sort) {
                        'duration' => $query->orderByDesc('duration_ms'),
                        'memory' => $query->orderByDesc('memory_usage_bytes'),
                        'status' => $query->orderByDesc('status_code'),
                        default => $query->latest('created_at'),
                    };
                }
            )
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | HTTP Method Statistics
        |--------------------------------------------------------------------------
        */

        $methodStats = (clone $baseQuery)
            ->select(
                'method',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(duration_ms) as average_duration'),
                DB::raw('MAX(duration_ms) as maximum_duration')
            )
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Requested Paths
        |--------------------------------------------------------------------------
        */

        $pathStats = (clone $baseQuery)
            ->select(
                'path',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(duration_ms) as average_duration'),
                DB::raw('MAX(duration_ms) as maximum_duration')
            )
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Slowest Requests
        |--------------------------------------------------------------------------
        */

        $slowestRequests = (clone $baseQuery)
            ->with('user')
            ->orderByDesc('duration_ms')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Runtime Information
        |--------------------------------------------------------------------------
        */

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
            'slowestRequests',
            'successRequests',
            'successRate',
            'errorRate',
            'slowRate',
            'runtime',
            'perPage'
        ));
    }

    /**
     * Build the performance query using all dashboard filters.
     */
    private function filteredQuery(Request $request)
    {
        return PerformanceMetric::query()

            /*
            |--------------------------------------------------------------------------
            | Date Range
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('date_range'),
                function ($query) use ($request) {
                    $range = $request->input('date_range');

                    match ($range) {
                        'today' => $query->whereDate(
                            'created_at',
                            now()->toDateString()
                        ),

                        '7_days' => $query->where(
                            'created_at',
                            '>=',
                            now()->subDays(7)
                        ),

                        '30_days' => $query->where(
                            'created_at',
                            '>=',
                            now()->subDays(30)
                        ),

                        'custom' => $this->applyCustomDateFilter(
                            $query,
                            $request
                        ),

                        default => null,
                    };
                }
            )

            /*
            |--------------------------------------------------------------------------
            | HTTP Method
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('method'),
                fn ($query) =>
                    $query->where(
                        'method',
                        $request->input('method')
                    )
            )

            /*
            |--------------------------------------------------------------------------
            | Exact Status
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status_code',
                        $request->input('status')
                    )
            )

            /*
            |--------------------------------------------------------------------------
            | Status Category
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('status_category'),
                function ($query) use ($request) {
                    match ($request->input('status_category')) {
                        '2xx' => $query->whereBetween(
                            'status_code',
                            [200, 299]
                        ),

                        '3xx' => $query->whereBetween(
                            'status_code',
                            [300, 399]
                        ),

                        '4xx' => $query->whereBetween(
                            'status_code',
                            [400, 499]
                        ),

                        '5xx' => $query->whereBetween(
                            'status_code',
                            [500, 599]
                        ),

                        default => null,
                    };
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Response Time
            |--------------------------------------------------------------------------
            |
            | Fast   = < 100 ms
            | Normal = 100 - 500 ms
            | Slow   = >= 500 ms
            |
            */

            ->when(
                $request->filled('response_time'),
                function ($query) use ($request) {
                    match ($request->input('response_time')) {
                        'fast' => $query->where(
                            'duration_ms',
                            '<',
                            100
                        ),

                        'normal' => $query->whereBetween(
                            'duration_ms',
                            [100, 499.999]
                        ),

                        'slow' => $query->where(
                            'duration_ms',
                            '>=',
                            500
                        ),

                        default => null,
                    };
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim($request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where(
                            'path',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'method',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'ip_address',
                            'like',
                            "%{$search}%"
                        );
                    });
                }
            );
    }

    /**
     * Apply custom date range.
     */
    private function applyCustomDateFilter($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('start_date')
            );
        }

        if ($request->filled('end_date')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('end_date')
            );
        }

        return $query;
    }

    /**
     * Export currently filtered performance logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $metrics = $this->filteredQuery($request)
            ->latest('created_at')
            ->get();

        $filename = 'octane-performance-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($metrics) {
                $handle = fopen('php://output', 'w');

                fputcsv($handle, [
                    'ID',
                    'Date',
                    'Method',
                    'Path',
                    'Status',
                    'Duration (ms)',
                    'Memory (MB)',
                    'Peak Memory (MB)',
                    'Slow',
                    'Error',
                    'IP Address',
                    'User Agent',
                ]);

                foreach ($metrics as $metric) {
                    fputcsv($handle, [
                        $metric->id,
                        optional($metric->created_at)
                            ->format('Y-m-d H:i:s'),
                        $metric->method,
                        $metric->path,
                        $metric->status_code,
                        $metric->duration_ms,
                        round(
                            $metric->memory_usage_bytes /
                            1024 /
                            1024,
                            2
                        ),
                        round(
                            $metric->peak_memory_usage_bytes /
                            1024 /
                            1024,
                            2
                        ),
                        $metric->is_slow ? 'Yes' : 'No',
                        $metric->is_error ? 'Yes' : 'No',
                        $metric->ip_address,
                        $metric->user_agent,
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Bulk delete selected performance logs.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $deleted = PerformanceMetric::whereIn(
            'id',
            $validated['ids']
        )->delete();

        return redirect()
            ->route('octane.monitor', $request->except('ids'))
            ->with(
                'success',
                "{$deleted} performance log(s) deleted successfully."
            );
    }

    /**
     * Delete all performance logs.
     */
    public function deleteAll(Request $request)
    {
        $deleted = PerformanceMetric::query()->delete();

        return redirect()
            ->route('octane.monitor')
            ->with(
                'success',
                "{$deleted} performance log(s) deleted successfully."
            );
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
                ? (bool) (
                    $opcache['opcache_statistics']
                    ['num_cached_scripts'] ?? 0
                )
                : false,

            'opcache_cached_scripts' => $opcache
                ? (
                    $opcache['opcache_statistics']
                    ['num_cached_scripts'] ?? 0
                )
                : 0,

            'environment' => app()->environment(),

            'debug_mode' => config('app.debug'),
        ];
    }
}