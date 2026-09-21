<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Octane In-Memory Cache & State Warmup</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .glass-card { background: rgba(30, 41, 59, 0.7); border: 1px solid rgba(51, 65, 85, 0.8); border-radius: 16px; backdrop-filter: blur(12px); }
        .nav-pills .nav-link { color: #94a3b8; font-weight: 600; border-radius: 10px; padding: 8px 18px; }
        .nav-pills .nav-link.active { background-color: #6366f1; color: #ffffff; }
        .nav-pills .nav-link:hover:not(.active) { color: #ffffff; background: rgba(255,255,255,0.05); }
    </style>
</head>
<body class="py-4">

    <!-- Global Top Navigation -->
    <div class="container mb-4">
        <div class="glass-card p-3 d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-lg">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">⚡</span>
                <div>
                    <h5 class="mb-0 fw-bold text-white">Laravel 12 Octane Engine</h5>
                    <small class="text-secondary font-mono">High-Performance Async & Concurrency Suite</small>
                </div>
            </div>

            <ul class="nav nav-pills gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('octane.monitor') }}">📊 Monitor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('octane.concurrent') }}">⚡ Concurrency</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('octane.cache') }}">🏎️ In-Memory Cache</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('octane.benchmark') }}">📈 Stress Benchmark</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="container">
        <!-- Hero Header -->
        <div class="glass-card p-4 p-md-5 mb-4 position-relative overflow-hidden shadow-lg border-primary">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-primary bg-opacity-25 border border-primary border-opacity-50 text-primary-emphasis mb-3 font-mono text-xs">
                        <span class="spinner-grow spinner-grow-sm text-info" role="status"></span>
                        MICROSECOND RAM CACHE & WARMUP
                    </div>
                    <h2 class="fw-extrabold text-white mb-2">Super-Fast In-Memory Cache & Preload Warmup</h2>
                    <p class="text-secondary mb-0">
                        Octane stores cache items directly in RAM with <strong>microsecond (μs)</strong> access latency, avoiding disk and network overhead. Preload system configs, routes, and frequent queries instantly.
                    </p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                    <div class="glass-card p-3 d-inline-block text-start">
                        <div class="text-secondary small font-mono">DEFAULT CACHE DRIVER</div>
                        <div class="fw-bold text-warning fs-6">
                            💾 {{ strtoupper($cacheDriver) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warmup Status Cards Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>WARMUP STATE</span>
                        <span>🔥</span>
                    </div>
                    <div class="fs-4 fw-bold mt-2 {{ $warmupStatus['is_warmed'] ? 'text-success' : 'text-warning' }}" id="stat-warmup-status">
                        {{ $warmupStatus['is_warmed'] ? '🟢 WARMED' : '🟡 PARTIAL / COLD' }}
                    </div>
                    <small class="text-secondary" id="stat-last-warmed">
                        {{ $warmupStatus['last_warmup_time'] ? 'Last: ' . $warmupStatus['last_warmup_time'] : 'Not warmed recently' }}
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>ROUTES PRELOADED</span>
                        <span>🛣️</span>
                    </div>
                    <div class="fs-2 fw-bold text-info mt-2" id="stat-routes-count">{{ $warmupStatus['routes_cached_count'] }}</div>
                    <small class="text-secondary">App route registry in RAM</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>USERS CACHED</span>
                        <span>👥</span>
                    </div>
                    <div class="fs-2 fw-bold text-light mt-2" id="stat-users-count">{{ $warmupStatus['users_cached_count'] }}</div>
                    <small class="text-secondary">Preloaded user records</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>RAM SPEEDUP</span>
                        <span>🚀</span>
                    </div>
                    <div class="fs-2 fw-bold text-success mt-2" id="stat-cache-speedup">~20x - 50x</div>
                    <small class="text-secondary">RAM vs Disk Cache speed</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Left Panel: Warmup & Benchmark Controls -->
            <div class="col-lg-5">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <span>⚙️</span> Warmup & Cache Actions
                    </h5>
                    <p class="text-secondary small mb-4">
                        Preload configurations and models into memory, or execute microsecond read/write benchmarks on 1,000 cache items.
                    </p>

                    <!-- Trigger Warmup Button -->
                    <button id="btn-trigger-warmup" onclick="triggerWarmup()" class="btn btn-primary w-100 py-3 mb-3 fw-bold rounded-3 d-flex align-items-center justify-content-between shadow">
                        <span class="d-flex align-items-center gap-2">
                            <span>🔥</span>
                            <span>Preload & Warmup Application State</span>
                        </span>
                        <span class="badge bg-dark font-mono">php artisan octane:warmup</span>
                    </button>

                    <!-- Run Cache Read/Write Benchmark -->
                    <button id="btn-run-benchmark" onclick="runCacheBenchmark()" class="btn btn-outline-info w-100 py-2.5 mb-3 fw-bold rounded-3 d-flex align-items-center justify-content-between">
                        <span class="d-flex align-items-center gap-2">
                            <span>🏎️</span>
                            <span>Run 1,000 Operations Benchmark</span>
                        </span>
                        <span class="badge bg-info bg-opacity-25 text-info font-mono">RAM vs Disk</span>
                    </button>

                    <!-- Flush In-Memory Cache Button -->
                    <button onclick="flushCache()" class="btn btn-outline-danger w-100 btn-sm font-mono mb-4">
                        🗑️ Flush Warmed Cache
                    </button>

                    <!-- CLI Command Info Box -->
                    <div class="p-3 glass-card bg-dark bg-opacity-50">
                        <div class="fw-bold text-white small mb-1">💻 Artisan CLI Command:</div>
                        <code class="text-info font-mono d-block p-2 bg-black rounded mb-2">php artisan octane:warmup</code>
                        <small class="text-secondary">Run this command automatically upon server boot or deployment for zero-cold-start performance.</small>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Visual Read/Write Benchmark Chart -->
            <div class="col-lg-7">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white mb-0">1,000 Ops Latency: Disk vs Octane RAM</h5>
                        <span class="badge bg-success font-mono" id="chart-gain-badge">RAM Microsecond Speed</span>
                    </div>

                    <div id="cacheChart" style="min-height: 280px;"></div>

                    <!-- Metrics Breakdown -->
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <div class="p-3 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">STANDARD CACHE (DISK/FILE)</div>
                                <div class="fw-bold text-warning fs-5 mt-1" id="stat-std-total">~45.00 ms</div>
                                <small class="text-secondary" id="stat-std-ops">~44,000 ops/sec</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">OCTANE IN-MEMORY (RAM)</div>
                                <div class="fw-bold text-success fs-5 mt-1" id="stat-mem-total">~1.20 ms</div>
                                <small class="text-secondary" id="stat-mem-ops">~1,600,000 ops/sec</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Output Console Log -->
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-white mb-0 font-mono">Terminal Output & Cache Telemetry Log</h6>
                <button onclick="document.getElementById('console-output').innerHTML='// Log cleared.'" class="btn btn-sm btn-outline-secondary font-mono text-xs">Clear</button>
            </div>
            <pre id="console-output" class="bg-black p-3 rounded-3 text-success font-mono text-xs mb-0 overflow-auto" style="max-height: 220px;">// Ready. Click 'Preload & Warmup Application State' or 'Run 1,000 Operations Benchmark'.</pre>
        </div>
    </div>

    <!-- Chart & AJAX Scripts -->
    <script>
        let cacheChart = null;

        function initCacheChart(stdWrite = 24.5, stdRead = 20.5, memWrite = 0.8, memRead = 0.4) {
            const options = {
                series: [
                    { name: 'Write (1,000 items)', data: [stdWrite, memWrite] },
                    { name: 'Read (1,000 items)', data: [stdRead, memRead] }
                ],
                chart: {
                    type: 'bar',
                    height: 260,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                theme: { mode: 'dark' },
                colors: ['#f59e0b', '#10b981'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '40%',
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return val + " ms"; },
                    offsetY: -20,
                    style: { fontSize: '11px', colors: ["#fff"], fontFamily: 'JetBrains Mono' }
                },
                xaxis: {
                    categories: ['Standard File/DB Cache', 'Octane In-Memory RAM'],
                    labels: { style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 } }
                },
                yaxis: {
                    title: { text: 'Duration (Milliseconds)', style: { color: '#94a3b8' } },
                    labels: { style: { colors: '#94a3b8' } }
                },
                legend: { position: 'top', labels: { colors: '#94a3b8' } },
                grid: { borderColor: '#334155', strokeDashArray: 4 }
            };

            if (cacheChart) {
                cacheChart.updateOptions(options);
            } else {
                cacheChart = new ApexCharts(document.querySelector("#cacheChart"), options);
                cacheChart.render();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCacheChart();
        });

        async function triggerWarmup() {
            const btn = document.getElementById('btn-trigger-warmup');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> <span>Warming up State...</span>`;
            btn.disabled = true;

            try {
                const res = await fetch('/octane/cache/warmup', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();

                if (data.status) {
                    document.getElementById('stat-warmup-status').textContent = '🟢 WARMED';
                    document.getElementById('stat-warmup-status').className = 'fs-4 fw-bold mt-2 text-success';
                    document.getElementById('stat-last-warmed').textContent = 'Last: ' + data.telemetry.timestamp;

                    logConsole(JSON.stringify(data, null, 2));
                }
            } catch (err) {
                logConsole("// ❌ Warmup Error: " + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function runCacheBenchmark() {
            const btn = document.getElementById('btn-run-benchmark');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> <span>Benchmarking...</span>`;
            btn.disabled = true;

            try {
                const res = await fetch('/octane/cache/benchmark', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: 1000 })
                });
                const data = await res.json();

                if (data.status) {
                    const std = data.standard_cache;
                    const mem = data.octane_memory_cache;
                    const gain = data.performance_gain;

                    document.getElementById('stat-std-total').textContent = std.total_time_ms + ' ms';
                    document.getElementById('stat-std-ops').textContent = std.ops_per_second.toLocaleString() + ' ops/sec';

                    document.getElementById('stat-mem-total').textContent = mem.total_time_ms + ' ms';
                    document.getElementById('stat-mem-ops').textContent = mem.ops_per_second.toLocaleString() + ' ops/sec';

                    document.getElementById('stat-cache-speedup').textContent = gain.speedup_multiplier;
                    document.getElementById('chart-gain-badge').textContent = gain.speedup_multiplier + ' Gain';

                    initCacheChart(std.write_time_ms, std.read_time_ms, mem.write_time_ms, mem.read_time_ms);
                    logConsole(JSON.stringify(data, null, 2));
                }
            } catch (err) {
                logConsole("// ❌ Benchmark Error: " + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function flushCache() {
            if (!confirm('Flush all warmed Octane in-memory cache keys?')) return;
            try {
                const res = await fetch('/octane/cache/flush', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                document.getElementById('stat-warmup-status').textContent = '🟡 COLD / FLUSHED';
                document.getElementById('stat-warmup-status').className = 'fs-4 fw-bold mt-2 text-warning';
                logConsole(JSON.stringify(data, null, 2));
            } catch (err) {
                logConsole("// ❌ Error: " + err.message);
            }
        }

        function logConsole(text) {
            const el = document.getElementById('console-output');
            el.innerHTML = text + "\n\n" + el.innerHTML;
        }
    </script>
</body>
</html>
