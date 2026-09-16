<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Octane Performance Monitor</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .stat-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            height: 100%;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }

        .section-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .status-badge {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 20px;
        }

        .runtime-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .runtime-item:last-child {
            border-bottom: 0;
        }

        .table {
            vertical-align: middle;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">
            ⚡ Laravel Octane Monitor
        </span>

        <span class="text-white">
            FrankenPHP
        </span>
    </div>
</nav>

<div class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Performance Dashboard
            </h2>

            <p class="text-muted mb-0">
                Laravel Octane & FrankenPHP runtime monitoring
            </p>
        </div>

        <a
            href="{{ route('octane.monitor') }}"
            class="btn btn-dark"
        >
            ↻ Refresh
        </a>
    </div>


    {{-- Statistics --}}

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="stat-label">
                    Total Requests
                </div>

                <div class="stat-value">
                    {{ number_format($totalRequests) }}
                </div>

                <small class="text-muted">
                    Tracked application requests
                </small>
            </div>
        </div>


        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="stat-label">
                    Average Response
                </div>

                <div class="stat-value">
                    {{ number_format($averageResponseTime, 2) }} ms
                </div>

                <small class="text-muted">
                    Average request duration
                </small>
            </div>
        </div>


        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="stat-label">
                    Slow Requests
                </div>

                <div class="stat-value text-warning">
                    {{ number_format($slowRequests) }}
                </div>

                <small class="text-muted">
                    Threshold:
                    {{ env('OCTANE_SLOW_REQUEST_MS', 500) }} ms
                </small>
            </div>
        </div>


        <div class="col-md-6 col-lg-3">
            <div class="card stat-card p-3">
                <div class="stat-label">
                    Server Errors
                </div>

                <div class="stat-value text-danger">
                    {{ number_format($errorRequests) }}
                </div>

                <small class="text-muted">
                    HTTP 500+ responses
                </small>
            </div>
        </div>

    </div>


    {{-- Additional Statistics --}}

    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card stat-card p-3">

                <div class="stat-label">
                    Maximum Response
                </div>

                <div class="stat-value">
                    {{ number_format($maxResponseTime, 2) }} ms
                </div>

            </div>
        </div>


        <div class="col-md-4">
            <div class="card stat-card p-3">

                <div class="stat-label">
                    Average Memory
                </div>

                <div class="stat-value">
                    {{ number_format($averageMemory / 1024 / 1024, 2) }} MB
                </div>

            </div>
        </div>


        <div class="col-md-4">
            <div class="card stat-card p-3">

                <div class="stat-label">
                    Peak Memory
                </div>

                <div class="stat-value">
                    {{ number_format($peakMemory / 1024 / 1024, 2) }} MB
                </div>

            </div>
        </div>

    </div>


    {{-- Runtime Information --}}

    <div class="card section-card mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold">
                ⚡ Octane Runtime & Server Health
            </h5>
        </div>

        <div class="card-body px-4">

            <div class="row">

                <div class="col-md-6">

                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Laravel</strong>

                        <span>
                            {{ $runtime['laravel_version'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>PHP</strong>

                        <span>
                            {{ $runtime['php_version'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Octane Server</strong>

                        <span class="badge bg-dark status-badge">
                            {{ $runtime['octane_server'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Environment</strong>

                        <span>
                            {{ $runtime['environment'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Debug Mode</strong>

                        @if ($runtime['debug_mode'])
                            <span class="badge bg-warning text-dark">
                                Enabled
                            </span>
                        @else
                            <span class="badge bg-success">
                                Disabled
                            </span>
                        @endif

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Workers</strong>

                        <span>
                            {{ $runtime['octane_workers'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Task Workers</strong>

                        <span>
                            {{ $runtime['task_workers'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Memory Limit</strong>

                        <span>
                            {{ $runtime['memory_limit'] }}
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Max Execution Time</strong>

                        <span>
                            {{ $runtime['max_execution_time'] }} sec
                        </span>
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>OPcache</strong>

                        @if ($runtime['opcache_enabled'])
                            <span class="badge bg-success">
                                Enabled
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Not Available
                            </span>
                        @endif
                    </div>


                    <div class="runtime-item d-flex justify-content-between">
                        <strong>Cached Scripts</strong>

                        <span>
                            {{ number_format($runtime['opcache_cached_scripts']) }}
                        </span>
                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- Method Statistics --}}

    <div class="card section-card mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold">
                📊 HTTP Method Performance
            </h5>
        </div>

        <div class="card-body px-4">

            <div class="table-responsive">

                <table class="table">

                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Total Requests</th>
                            <th>Average Duration</th>
                            <th>Maximum Duration</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($methodStats as $stat)

                            <tr>

                                <td>
                                    <span class="badge bg-dark">
                                        {{ $stat->method }}
                                    </span>
                                </td>

                                <td>
                                    {{ number_format($stat->total) }}
                                </td>

                                <td>
                                    {{ number_format($stat->average_duration, 2) }}
                                    ms
                                </td>

                                <td>
                                    {{ number_format($stat->maximum_duration, 2) }}
                                    ms
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No request metrics available yet.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Top Paths --}}

    <div class="card section-card mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold">
                🔥 Most Requested Routes
            </h5>
        </div>

        <div class="card-body px-4">

            <div class="table-responsive">

                <table class="table">

                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Total</th>
                            <th>Average</th>
                            <th>Maximum</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pathStats as $stat)

                            <tr>

                                <td>
                                    <code>
                                        {{ $stat->path }}
                                    </code>
                                </td>

                                <td>
                                    {{ number_format($stat->total) }}
                                </td>

                                <td>
                                    {{ number_format($stat->average_duration, 2) }}
                                    ms
                                </td>

                                <td>
                                    {{ number_format($stat->maximum_duration, 2) }}
                                    ms
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No route statistics available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Filters --}}

    <div class="card section-card mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold">
                🔎 Request History & Filtering
            </h5>
        </div>

        <div class="card-body px-4">

            <form
                method="GET"
                action="{{ route('octane.monitor') }}"
            >

                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Search Route
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="/dashboard"
                        >

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Method
                        </label>

                        <select
                            name="method"
                            class="form-select"
                        >
                            <option value="">
                                All
                            </option>

                            <option
                                value="GET"
                                @selected(request('method') === 'GET')
                            >
                                GET
                            </option>

                            <option
                                value="POST"
                                @selected(request('method') === 'POST')
                            >
                                POST
                            </option>

                            <option
                                value="PUT"
                                @selected(request('method') === 'PUT')
                            >
                                PUT
                            </option>

                            <option
                                value="DELETE"
                                @selected(request('method') === 'DELETE')
                            >
                                DELETE
                            </option>
                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="200"
                                @selected(request('status') == '200')
                            >
                                200
                            </option>

                            <option
                                value="404"
                                @selected(request('status') == '404')
                            >
                                404
                            </option>

                            <option
                                value="500"
                                @selected(request('status') == '500')
                            >
                                500
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Type
                        </label>

                        <select
                            name="slow"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="1"
                                @selected(request('slow') == '1')
                            >
                                Slow Only
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 d-flex align-items-end gap-2">

                        <button class="btn btn-dark">
                            Filter
                        </button>

                        <a
                            href="{{ route('octane.monitor') }}"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Request Metrics --}}

    <div class="card section-card">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold">
                📋 Request Performance Log
            </h5>
        </div>

        <div class="card-body px-4">

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>

                        <tr>
                            <th>Time</th>
                            <th>Method</th>
                            <th>Path</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Memory</th>
                            <th>Result</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($recentMetrics as $metric)

                            <tr>

                                <td>
                                    <small>
                                        {{ $metric->created_at->format('d M Y H:i:s') }}
                                    </small>
                                </td>


                                <td>
                                    <span class="badge bg-dark">
                                        {{ $metric->method }}
                                    </span>
                                </td>


                                <td>
                                    <code>
                                        {{ $metric->path }}
                                    </code>
                                </td>


                                <td>

                                    @if ($metric->status_code >= 500)

                                        <span class="badge bg-danger">
                                            {{ $metric->status_code }}
                                        </span>

                                    @elseif ($metric->status_code >= 400)

                                        <span class="badge bg-warning text-dark">
                                            {{ $metric->status_code }}
                                        </span>

                                    @else

                                        <span class="badge bg-success">
                                            {{ $metric->status_code }}
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ number_format($metric->duration_ms, 2) }}
                                    ms
                                </td>


                                <td>
                                    {{ number_format($metric->memory_usage_bytes / 1024 / 1024, 2) }}
                                    MB
                                </td>


                                <td>

                                    @if ($metric->is_slow)

                                        <span class="badge bg-warning text-dark">
                                            Slow
                                        </span>

                                    @elseif ($metric->is_error)

                                        <span class="badge bg-danger">
                                            Error
                                        </span>

                                    @else

                                        <span class="badge bg-success">
                                            Normal
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >
                                    No performance data found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $recentMetrics->links() }}

            </div>

        </div>

    </div>

</div>

</body>

</html>