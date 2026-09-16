<?php

namespace App\Http\Middleware;

use App\Models\PerformanceMetric;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackOctanePerformance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = hrtime(true);
        $startMemory = memory_get_usage(true);

        $response = null;
        $exception = null;

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            $exception = $e;

            throw $e;
        } finally {
            $endTime = hrtime(true);

            $durationMs = ($endTime - $startTime) / 1_000_000;

            $memoryUsage = memory_get_usage(true);
            $peakMemoryUsage = memory_get_peak_usage(true);

            $statusCode = $response?->getStatusCode() ?? 500;

            $slowThreshold = (float) env(
                'OCTANE_SLOW_REQUEST_MS',
                500
            );

            $isSlow = $durationMs >= $slowThreshold;

            $isError = $exception !== null || $statusCode >= 500;

            try {
                PerformanceMetric::create([
                    'method' => $request->method(),
                    'path' => '/' . ltrim($request->path(), '/'),
                    'status_code' => $statusCode,
                    'duration_ms' => round($durationMs, 3),
                    'memory_usage_bytes' => max(
                        0,
                        $memoryUsage - $startMemory
                    ),
                    'peak_memory_usage_bytes' => $peakMemoryUsage,
                    'is_slow' => $isSlow,
                    'is_error' => $isError,
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (Throwable $loggingException) {
                report($loggingException);
            }
        }

        return $response;
    }
}