<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Octane Performance Monitor</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

        .analytics-progress {
            height: 8px;
            border-radius: 20px;
        }

        .slow-row {
            background: #fff8e1;
        }

        .metric-card {
            border-radius: 16px;
        }

        code {
            word-break: break-word;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('octane.monitor') }}">
                <span>⚡</span>
                <span>Laravel Octane Monitor</span>
            </a>

            <div class="d-flex align-items-center gap-2">
                <ul class="navbar-nav flex-row gap-1">
                    <li class="nav-item">
                        <a class="nav-link px-3 active fw-bold text-white rounded bg-primary" href="{{ route('octane.monitor') }}">📊 Monitor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 text-secondary hover-text-white" href="{{ route('octane.concurrent') }}">⚡ Concurrency</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 text-secondary hover-text-white" href="{{ route('octane.cache') }}">🏎️ In-Memory Cache</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 text-secondary hover-text-white" href="{{ route('octane.benchmark') }}">📈 Stress Benchmark</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container pb-5">


        {{-- Flash Message --}}

        @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        @endif


        {{-- Header --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold mb-1">
                    Performance Dashboard
                </h2>

                <p class="text-muted mb-0">
                    Laravel Octane & FrankenPHP runtime monitoring
                </p>

            </div>

            <div class="d-flex gap-2">

                <a
                    href="{{ route('octane.monitor.export', request()->query()) }}"
                    class="btn btn-success">
                    📥 Export CSV
                </a>

                <a
                    href="{{ route('octane.monitor') }}"
                    class="btn btn-dark">
                    ↻ Refresh
                </a>

            </div>

        </div>


        {{-- Main Statistics --}}

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
                        Current filtered requests
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
                        Average duration
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
                        {{ number_format($slowRate, 2) }}% of requests
                    </small>

                </div>

            </div>


            <div class="col-md-6 col-lg-3">

                <div class="card stat-card p-3">

                    <div class="stat-label">
                        Error Requests
                    </div>

                    <div class="stat-value text-danger">
                        {{ number_format($errorRequests) }}
                    </div>

                    <small class="text-muted">
                        {{ number_format($errorRate, 2) }}% error rate
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


        {{-- Performance Analytics --}}

        <div class="card section-card mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <h5 class="fw-bold">
                    📈 Performance Analytics
                </h5>

            </div>

            <div class="card-body px-4">

                <div class="row g-4">

                    <div class="col-md-4">

                        <div class="p-3 border rounded metric-card">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    Success Rate
                                </strong>

                                <span class="text-success fw-bold">
                                    {{ number_format($successRate, 2) }}%
                                </span>

                            </div>

                            <div class="progress analytics-progress mt-3">

                                <div
                                    class="progress-bar bg-success"
                                    style="width: {{ min($successRate, 100) }}%"></div>

                            </div>

                            <small class="text-muted">
                                {{ number_format($successRequests) }} successful requests
                            </small>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="p-3 border rounded metric-card">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    Error Rate
                                </strong>

                                <span class="text-danger fw-bold">
                                    {{ number_format($errorRate, 2) }}%
                                </span>

                            </div>

                            <div class="progress analytics-progress mt-3">

                                <div
                                    class="progress-bar bg-danger"
                                    style="width: {{ min($errorRate, 100) }}%"></div>

                            </div>

                            <small class="text-muted">
                                {{ number_format($errorRequests) }} errors
                            </small>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="p-3 border rounded metric-card">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    Slow Rate
                                </strong>

                                <span class="text-warning fw-bold">
                                    {{ number_format($slowRate, 2) }}%
                                </span>

                            </div>

                            <div class="progress analytics-progress mt-3">

                                <div
                                    class="progress-bar bg-warning"
                                    style="width: {{ min($slowRate, 100) }}%"></div>

                            </div>

                            <small class="text-muted">
                                {{ number_format($slowRequests) }} slow requests
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Runtime --}}

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
                            <span>{{ $runtime['laravel_version'] }}</span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>PHP</strong>
                            <span>{{ $runtime['php_version'] }}</span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>Octane Server</strong>

                            <span class="badge bg-dark">
                                {{ $runtime['octane_server'] }}
                            </span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>Environment</strong>
                            <span>{{ $runtime['environment'] }}</span>
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
                            <span>{{ $runtime['octane_workers'] }}</span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>Task Workers</strong>
                            <span>{{ $runtime['task_workers'] }}</span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>Memory Limit</strong>
                            <span>{{ $runtime['memory_limit'] }}</span>
                        </div>

                        <div class="runtime-item d-flex justify-content-between">
                            <strong>Max Execution Time</strong>
                            <span>{{ $runtime['max_execution_time'] }} sec</span>
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

                                <td
                                    colspan="4"
                                    class="text-center text-muted">
                                    No request metrics available.
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
                    🔎 Request Filters
                </h5>

            </div>


            <div class="card-body px-4">

                <form
                    method="GET"
                    action="{{ route('octane.monitor') }}">

                    <div class="row g-3">


                        {{-- Search --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="Route, method or IP">

                        </div>


                        {{-- Date Range --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                📅 Date Range
                            </label>

                            <select
                                name="date_range"
                                class="form-select"
                                onchange="toggleCustomDates(this.value)">

                                <option value="">
                                    All Time
                                </option>

                                <option
                                    value="today"
                                    @selected(request('date_range')==='today' )>
                                    Today
                                </option>

                                <option
                                    value="7_days"
                                    @selected(request('date_range')==='7_days' )>
                                    Last 7 Days
                                </option>

                                <option
                                    value="30_days"
                                    @selected(request('date_range')==='30_days' )>
                                    Last 30 Days
                                </option>

                                <option
                                    value="custom"
                                    @selected(request('date_range')==='custom' )>
                                    Custom
                                </option>

                            </select>

                        </div>


                        {{-- Method --}}

                        <div class="col-md-4">

                            <label class="form-label">
                                HTTP Method
                            </label>

                            <select
                                name="method"
                                class="form-select">

                                <option value="">
                                    All Methods
                                </option>

                                @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)

                                <option
                                    value="{{ $method }}"
                                    @selected(request('method')===$method)>
                                    {{ $method }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Custom Start Date --}}

                        <div
                            class="col-md-3"
                            id="customStartDate"
                            style="{{ request('date_range') === 'custom' ? '' : 'display:none;' }}">

                            <label class="form-label">
                                Start Date
                            </label>

                            <input
                                type="date"
                                name="start_date"
                                class="form-control"
                                value="{{ request('start_date') }}">

                        </div>


                        {{-- Custom End Date --}}

                        <div
                            class="col-md-3"
                            id="customEndDate"
                            style="{{ request('date_range') === 'custom' ? '' : 'display:none;' }}">

                            <label class="form-label">
                                End Date
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                class="form-control"
                                value="{{ request('end_date') }}">

                        </div>


                        {{-- Status Category --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                🎯 Status Category
                            </label>

                            <select
                                name="status_category"
                                class="form-select">

                                <option value="">
                                    All Statuses
                                </option>

                                <option
                                    value="2xx"
                                    @selected(request('status_category')==='2xx' )>
                                    2xx Success
                                </option>

                                <option
                                    value="3xx"
                                    @selected(request('status_category')==='3xx' )>
                                    3xx Redirect
                                </option>

                                <option
                                    value="4xx"
                                    @selected(request('status_category')==='4xx' )>
                                    4xx Client Error
                                </option>

                                <option
                                    value="5xx"
                                    @selected(request('status_category')==='5xx' )>
                                    5xx Server Error
                                </option>

                            </select>

                        </div>


                        {{-- Response Time --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                ⏱️ Response Time
                            </label>

                            <select
                                name="response_time"
                                class="form-select">

                                <option value="">
                                    All
                                </option>

                                <option
                                    value="fast"
                                    @selected(request('response_time')==='fast' )>
                                    Fast (&lt; 100ms)
                                </option>

                                <option
                                    value="normal"
                                    @selected(request('response_time')==='normal' )>
                                    Normal (100-500ms)
                                </option>

                                <option
                                    value="slow"
                                    @selected(request('response_time')==='slow' )>
                                    Slow (500ms+)
                                </option>

                            </select>

                        </div>


                        {{-- Exact Status --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                Exact Status
                            </label>

                            <select
                                name="status"
                                class="form-select">

                                <option value="">
                                    All
                                </option>

                                @foreach ([200, 201, 204, 301, 302, 400, 401, 403, 404, 422, 429, 500, 502, 503] as $status)

                                <option
                                    value="{{ $status }}"
                                    @selected(request('status')==$status)>
                                    {{ $status }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Sort --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                ↕️ Sort
                            </label>

                            <select
                                name="sort"
                                class="form-select">

                                <option value="">
                                    Newest
                                </option>

                                <option
                                    value="duration"
                                    @selected(request('sort')==='duration' )>
                                    Duration
                                </option>

                                <option
                                    value="memory"
                                    @selected(request('sort')==='memory' )>
                                    Memory
                                </option>

                                <option
                                    value="status"
                                    @selected(request('sort')==='status' )>
                                    Status
                                </option>

                            </select>

                        </div>


                        {{-- Per Page --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                🔢 Per Page
                            </label>

                            <select
                                name="per_page"
                                class="form-select">

                                @foreach ([5, 15, 30, 50, 100] as $size)

                                <option
                                    value="{{ $size }}"
                                    @selected($perPage==$size)>
                                    {{ $size }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Buttons --}}

                        <div class="col-md-6 d-flex align-items-end gap-2">

                            <button
                                type="submit"
                                class="btn btn-dark">
                                🔎 Apply Filters
                            </button>

                            <a
                                href="{{ route('octane.monitor') }}"
                                class="btn btn-outline-secondary">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>




        {{-- Slowest Requests --}}

        <div class="card section-card mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <h5 class="fw-bold">
                    🐌 Slowest Requests
                </h5>

            </div>

            <div class="card-body px-4">

                <div class="table-responsive">

                    <table class="table">

                        <thead>

                            <tr>
                                <th>Path</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Memory</th>
                                <th>Time</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($slowestRequests as $metric)

                            <tr class="slow-row">

                                <td>
                                    <code>
                                        {{ $metric->path }}
                                    </code>
                                </td>

                                <td>
                                    <span class="badge bg-dark">
                                        {{ $metric->method }}
                                    </span>
                                </td>

                                <td>
                                    {{ $metric->status_code }}
                                </td>

                                <td class="fw-bold text-danger">
                                    {{ number_format($metric->duration_ms, 2) }}
                                    ms
                                </td>

                                <td>
                                    {{ number_format($metric->memory_usage_bytes / 1024 / 1024, 2) }}
                                    MB
                                </td>

                                <td>
                                    {{ $metric->created_at->format('d M H:i:s') }}
                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-muted">
                                    No performance data available.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Request History --}}

        <div class="card section-card">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="fw-bold mb-0">
                        📋 Request Performance Log
                    </h5>

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            form="bulkDeleteForm"
                            class="btn btn-danger btn-sm"
                            onclick="return confirmBulkDelete()">
                            🗑️ Delete Selected
                        </button>

                        <form
                            method="POST"
                            action="{{ route('octane.monitor.delete-all') }}"
                            onsubmit="return confirmDeleteAll()">

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline-danger btn-sm">
                                Delete All
                            </button>

                        </form>

                    </div>

                </div>

            </div>


            <div class="card-body px-4">

                <form
                    id="bulkDeleteForm"
                    method="POST"
                    action="{{ route('octane.monitor.bulk-delete') }}">

                    @csrf

                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>

                                    <th width="40">

                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="selectAll">

                                    </th>

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

                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $metric->id }}"
                                            class="form-check-input metric-checkbox">

                                    </td>


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

                                        @elseif ($metric->status_code >= 300)

                                        <span class="badge bg-info text-dark">
                                            {{ $metric->status_code }}
                                        </span>

                                        @else

                                        <span class="badge bg-success">
                                            {{ $metric->status_code }}
                                        </span>

                                        @endif

                                    </td>


                                    <td>

                                        <strong>
                                            {{ number_format($metric->duration_ms, 2) }}
                                        </strong>

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
                                        colspan="8"
                                        class="text-center text-muted py-4">
                                        No performance data found.
                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </form>


                {{-- Pagination --}}

                <div class="mt-3">

                    {{ $recentMetrics->links() }}

                </div>

            </div>

        </div>

    </div>


    <script>
        function toggleCustomDates(value) {

            const start = document.getElementById('customStartDate');
            const end = document.getElementById('customEndDate');

            if (value === 'custom') {

                start.style.display = '';
                end.style.display = '';

            } else {

                start.style.display = 'none';
                end.style.display = 'none';

            }

        }


        document
            .getElementById('selectAll')
            .addEventListener('change', function() {

                document
                    .querySelectorAll('.metric-checkbox')
                    .forEach(function(checkbox) {

                        checkbox.checked = this.checked;

                    }, this);

            });


        function confirmBulkDelete() {

            const selected =
                document.querySelectorAll(
                    '.metric-checkbox:checked'
                ).length;

            if (selected === 0) {

                alert('Please select at least one performance log.');

                return false;

            }

            return confirm(
                'Are you sure you want to delete ' +
                selected +
                ' selected performance log(s)?'
            );

        }


        function confirmDeleteAll() {

            return confirm(
                'Are you sure you want to delete ALL performance logs? This action cannot be undone.'
            );

        }
    </script>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>