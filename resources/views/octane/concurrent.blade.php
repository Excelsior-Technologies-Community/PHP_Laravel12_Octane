<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Octane Concurrent Tasks & Async Engine</title>
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
        .stat-badge { border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
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
                    <a class="nav-link active" href="{{ route('octane.concurrent') }}">⚡ Concurrency</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('octane.cache') }}">🏎️ In-Memory Cache</a>
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
                        OCTANE MULTI-WORKER CONCURRENCY
                    </div>
                    <h2 class="fw-extrabold text-white mb-2">Parallel Execution & Async Background Engine</h2>
                    <p class="text-secondary mb-0">
                        Harness <code class="text-info font-mono">Octane::concurrently([...])</code> to dispatch multiple independent tasks across parallel workers simultaneously. Eliminate I/O wait bottlenecks and reduce total latency by <strong>60% - 80%</strong>.
                    </p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                    <div class="glass-card p-3 d-inline-block text-start">
                        <div class="text-secondary small font-mono">OCTANE RUNTIME</div>
                        <div class="fw-bold text-success fs-6">
                            🟢 {{ $serverEngine }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>SEQUENTIAL TIME</span>
                        <span>🐌</span>
                    </div>
                    <div class="fs-2 fw-bold text-warning mt-2" id="stat-seq-time">-- ms</div>
                    <small class="text-secondary">Standard PHP blocking flow</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>OCTANE CONCURRENT</span>
                        <span>⚡</span>
                    </div>
                    <div class="fs-2 fw-bold text-success mt-2" id="stat-con-time">-- ms</div>
                    <small class="text-secondary">Parallel worker execution</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>SPEEDUP GAIN</span>
                        <span>🚀</span>
                    </div>
                    <div class="fs-2 fw-bold text-info mt-2" id="stat-speedup">--</div>
                    <small class="text-secondary">Performance multiplier</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>TIME SAVED</span>
                        <span>⏱️</span>
                    </div>
                    <div class="fs-2 fw-bold text-light mt-2" id="stat-saved">-- ms</div>
                    <small class="text-secondary">Latency eliminated</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Left Panel: Interactive Actions -->
            <div class="col-lg-5">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <span>🧪</span> Concurrency Control Center
                    </h5>
                    <p class="text-secondary small mb-4">
                        Trigger live benchmark simulations to test 3 heavy parallel tasks: KYC Verification (250ms), Payment Gateway (350ms), and Global Analytics (450ms).
                    </p>

                    <!-- Run Concurrency Benchmark -->
                    <button id="btn-run-benchmark" onclick="runConcurrencyBenchmark()" class="btn btn-primary w-full py-3 mb-3 fw-bold rounded-3 d-flex align-items-center justify-content-between shadow">
                        <span class="d-flex align-items-center gap-2">
                            <span>⚡</span>
                            <span>Run Octane Concurrency Benchmark</span>
                        </span>
                        <span class="badge bg-dark font-mono">3 Tasks</span>
                    </button>

                    <!-- Fire and Forget Background Task -->
                    <div class="p-3 glass-card bg-slate-900 border-secondary mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-light small">🚀 Fire-and-Forget Background Task</span>
                            <span class="badge bg-success bg-opacity-25 text-success">Async Worker</span>
                        </div>
                        <p class="text-secondary" style="font-size: 11px;">
                            Returns an immediate HTTP 200 to user in <strong class="text-white">~1ms</strong> while async worker executes report exports & audit logging in background.
                        </p>
                        <button onclick="triggerBackgroundTask()" id="btn-background" class="btn btn-outline-info w-100 btn-sm font-mono">
                            Dispatch Async Background Task
                        </button>
                    </div>

                    <!-- Architecture Info -->
                    <div class="p-3 glass-card bg-dark bg-opacity-50">
                        <div class="fw-bold text-white small mb-1">💡 How Octane Concurrency Works:</div>
                        <ul class="text-secondary small mb-0 ps-3">
                            <li>Tasks are dispatched simultaneously to separate Octane workers.</li>
                            <li>Total response time = <code class="text-info">Max(Task A, Task B, Task C)</code> rather than <code class="text-warning">Sum(A + B + C)</code>.</li>
                            <li>Zero thread blocking, ultra-low memory overhead.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Visual Comparison Chart & Tasks Breakdown -->
            <div class="col-lg-7">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white mb-0">Execution Latency Comparison</h5>
                        <span class="badge bg-secondary font-mono" id="chart-status-badge">Awaiting Test</span>
                    </div>

                    <div id="concurrencyChart" style="min-height: 280px;"></div>

                    <!-- Tasks Breakdown Cards -->
                    <div class="row g-2 mt-2" id="task-cards-container">
                        <div class="col-md-4">
                            <div class="p-2.5 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">TASK 1 (KYC API)</div>
                                <div class="fw-bold text-white small mt-1">250 ms delay</div>
                                <span class="badge bg-secondary text-xs mt-1" id="t1-status">Idle</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2.5 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">TASK 2 (PAYMENT GATEWAY)</div>
                                <div class="fw-bold text-white small mt-1">350 ms delay</div>
                                <span class="badge bg-secondary text-xs mt-1" id="t2-status">Idle</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2.5 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">TASK 3 (ANALYTICS ENGINE)</div>
                                <div class="fw-bold text-white small mt-1">450 ms delay</div>
                                <span class="badge bg-secondary text-xs mt-1" id="t3-status">Idle</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Output Console Log -->
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-white mb-0 font-mono">Terminal Output & Telemetry Log</h6>
                <button onclick="document.getElementById('console-output').innerHTML='// Log cleared.'" class="btn btn-sm btn-outline-secondary font-mono text-xs">Clear</button>
            </div>
            <pre id="console-output" class="bg-black p-3 rounded-3 text-success font-mono text-xs mb-0 overflow-auto" style="max-height: 220px;">// Ready. Click 'Run Octane Concurrency Benchmark' above to execute parallel tasks.</pre>
        </div>
    </div>

    <!-- Chart & AJAX Scripts -->
    <script>
        let chart = null;

        function initChart(seqTime = 1050, conTime = 460) {
            const options = {
                series: [{
                    name: 'Execution Duration (ms)',
                    data: [seqTime, conTime]
                }],
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
                        borderRadius: 8,
                        columnWidth: '45%',
                        distributed: true,
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) { return val + " ms"; },
                    offsetY: -20,
                    style: { fontSize: '12px', colors: ["#fff"], fontFamily: 'JetBrains Mono' }
                },
                xaxis: {
                    categories: ['Sequential (Standard PHP)', 'Octane Concurrently (Parallel)'],
                    labels: { style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 } }
                },
                yaxis: {
                    title: { text: 'Duration (Milliseconds)', style: { color: '#94a3b8' } },
                    labels: { style: { colors: '#94a3b8' } }
                },
                legend: { show: false },
                grid: { borderColor: '#334155', strokeDashArray: 4 }
            };

            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(document.querySelector("#concurrencyChart"), options);
                chart.render();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initChart(1050, 460);
        });

        async function runConcurrencyBenchmark() {
            const btn = document.getElementById('btn-run-benchmark');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> <span>Running Concurrently...</span>`;
            btn.disabled = true;

            logConsole("// ⚡ Dispatching concurrent requests to /octane/concurrent/run...");

            try {
                const res = await fetch('/octane/concurrent/run', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.status) {
                    const s = data.summary;
                    document.getElementById('stat-seq-time').textContent = s.sequential_time_ms + ' ms';
                    document.getElementById('stat-con-time').textContent = s.concurrent_time_ms + ' ms';
                    document.getElementById('stat-speedup').textContent = s.speedup_multiplier;
                    document.getElementById('stat-saved').textContent = s.time_saved_ms + ' ms';

                    document.getElementById('chart-status-badge').textContent = s.speedup_multiplier;
                    document.getElementById('chart-status-badge').className = 'badge bg-success font-mono';

                    document.getElementById('t1-status').textContent = 'Completed (250ms)';
                    document.getElementById('t1-status').className = 'badge bg-success text-xs mt-1';
                    document.getElementById('t2-status').textContent = 'Completed (350ms)';
                    document.getElementById('t2-status').className = 'badge bg-success text-xs mt-1';
                    document.getElementById('t3-status').textContent = 'Completed (450ms)';
                    document.getElementById('t3-status').className = 'badge bg-success text-xs mt-1';

                    initChart(s.sequential_time_ms, s.concurrent_time_ms);
                    logConsole(JSON.stringify(data, null, 2));
                }
            } catch (err) {
                logConsole("// ❌ Error: " + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function triggerBackgroundTask() {
            const btn = document.getElementById('btn-background');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Dispatching...';

            try {
                const res = await fetch('/octane/background/run', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ task_name: 'Audit Log & PDF Export Engine' })
                });
                const data = await res.json();
                logConsole(JSON.stringify(data, null, 2));
            } catch (err) {
                logConsole("// ❌ Background Task Error: " + err.message);
            } finally {
                btn.innerHTML = originalText;
            }
        }

        function logConsole(text) {
            const el = document.getElementById('console-output');
            el.innerHTML = text + "\n\n" + el.innerHTML;
        }
    </script>
</body>
</html>
