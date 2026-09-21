<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Octane Live Stress Test & Benchmarking Suite</title>
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
                    <a class="nav-link" href="{{ route('octane.concurrent') }}">⚡ Concurrency</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('octane.cache') }}">🏎️ In-Memory Cache</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('octane.benchmark') }}">📈 Stress Benchmark</a>
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
                        LIVE STRESS TESTER & PHP-FPM COMPARATOR
                    </div>
                    <h2 class="fw-extrabold text-white mb-2">High-Concurrency Benchmarking Suite</h2>
                    <p class="text-secondary mb-0">
                        Test system throughput under heavy concurrency right from your browser. Compare <strong>Requests Per Second (RPS)</strong> and <strong>P99 Latencies</strong> between pre-booted Octane workers and legacy PHP-FPM.
                    </p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                    <div class="glass-card p-3 d-inline-block text-start">
                        <div class="text-secondary small font-mono">SERVER ENGINE</div>
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
                        <span>OCTANE THROUGHPUT</span>
                        <span>⚡</span>
                    </div>
                    <div class="fs-2 fw-bold text-success mt-2" id="stat-octane-rps">-- RPS</div>
                    <small class="text-secondary">Requests processed / second</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>AVG LATENCY</span>
                        <span>⏱️</span>
                    </div>
                    <div class="fs-2 fw-bold text-info mt-2" id="stat-avg-latency">-- ms</div>
                    <small class="text-secondary">Mean execution duration</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>P99 TAIL LATENCY</span>
                        <span>🎯</span>
                    </div>
                    <div class="fs-2 fw-bold text-light mt-2" id="stat-p99-latency">-- ms</div>
                    <small class="text-secondary">99% of requests faster than</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between text-secondary small font-mono">
                        <span>THROUGHPUT GAIN</span>
                        <span>🚀</span>
                    </div>
                    <div class="fs-2 fw-bold text-warning mt-2" id="stat-rps-gain">--</div>
                    <small class="text-secondary">vs Standard PHP-FPM</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Left Panel: Stress Test Parameters Form -->
            <div class="col-lg-5">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <span>🎯</span> Stress Test Configuration
                    </h5>
                    <p class="text-secondary small mb-4">
                        Configure batch volume and target load workload to simulate high-traffic bursts.
                    </p>

                    <!-- Request Count Selector -->
                    <div class="mb-3">
                        <label class="form-label text-secondary small font-mono fw-bold">TOTAL REQUESTS BATCH</label>
                        <select id="select-requests" class="form-select bg-dark border-secondary text-white font-mono">
                            <option value="50">50 Requests (Quick Spike)</option>
                            <option value="100" selected>100 Requests (Standard Load)</option>
                            <option value="250">250 Requests (Heavy Burst)</option>
                            <option value="500">500 Requests (High Stress)</option>
                            <option value="1000">1,000 Requests (Maximum Concurrency)</option>
                        </select>
                    </div>

                    <!-- Concurrency Level -->
                    <div class="mb-3">
                        <label class="form-label text-secondary small font-mono fw-bold">CONCURRENCY WORKERS</label>
                        <select id="select-concurrency" class="form-select bg-dark border-secondary text-white font-mono">
                            <option value="5">5 Parallel Workers</option>
                            <option value="10" selected>10 Parallel Workers</option>
                            <option value="25">25 Parallel Workers</option>
                            <option value="50">50 Parallel Workers</option>
                        </select>
                    </div>

                    <!-- Workload Type -->
                    <div class="mb-4">
                        <label class="form-label text-secondary small font-mono fw-bold">WORKLOAD TARGET TYPE</label>
                        <select id="select-type" class="form-select bg-dark border-secondary text-white font-mono">
                            <option value="json_endpoint" selected>JSON API Payload Serialization</option>
                            <option value="db_query">Database Connection & Ping Query</option>
                            <option value="compute">Cryptographic Compute (SHA-256)</option>
                        </select>
                    </div>

                    <!-- Launch Stress Test Button -->
                    <button id="btn-launch-test" onclick="launchStressTest()" class="btn btn-primary w-100 py-3 fw-bold rounded-3 d-flex align-items-center justify-content-between shadow">
                        <span class="d-flex align-items-center gap-2">
                            <span>🚀</span>
                            <span>Launch Live Stress Test</span>
                        </span>
                        <span class="badge bg-dark font-mono" id="btn-batch-badge">100 Reqs</span>
                    </button>
                </div>
            </div>

            <!-- Right Panel: Side-by-Side Comparison Charts -->
            <div class="col-lg-7">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white mb-0">Throughput (RPS) Comparison</h5>
                        <span class="badge bg-success font-mono" id="chart-rps-badge">Octane Advantage</span>
                    </div>

                    <div id="benchmarkChart" style="min-height: 280px;"></div>

                    <!-- Performance Comparison Row -->
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <div class="p-3 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">STANDARD PHP-FPM</div>
                                <div class="fw-bold text-warning fs-5 mt-1" id="stat-fpm-rps">~120 RPS</div>
                                <small class="text-secondary" id="stat-fpm-latency">Avg Latency: ~18.5 ms (Cold boot)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 glass-card bg-dark bg-opacity-40">
                                <div class="text-secondary text-xs font-mono">LARAVEL 12 OCTANE</div>
                                <div class="fw-bold text-success fs-5 mt-1" id="stat-octane-compare">~1,800+ RPS</div>
                                <small class="text-secondary" id="stat-octane-latency">Avg Latency: ~0.5 ms (RAM pre-boot)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Output Console Log -->
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-white mb-0 font-mono">Terminal Output & Stress Telemetry Log</h6>
                <button onclick="document.getElementById('console-output').innerHTML='// Log cleared.'" class="btn btn-sm btn-outline-secondary font-mono text-xs">Clear</button>
            </div>
            <pre id="console-output" class="bg-black p-3 rounded-3 text-success font-mono text-xs mb-0 overflow-auto" style="max-height: 220px;">// Ready. Click 'Launch Live Stress Test' to benchmark your system.</pre>
        </div>
    </div>

    <!-- Chart & AJAX Scripts -->
    <script>
        let benchmarkChart = null;

        function initBenchmarkChart(fpmRps = 140, octaneRps = 1650) {
            const options = {
                series: [{
                    name: 'Requests Per Second (RPS)',
                    data: [fpmRps, octaneRps]
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
                    formatter: function (val) { return val.toLocaleString() + " RPS"; },
                    offsetY: -20,
                    style: { fontSize: '12px', colors: ["#fff"], fontFamily: 'JetBrains Mono' }
                },
                xaxis: {
                    categories: ['Standard PHP-FPM', 'Laravel 12 Octane'],
                    labels: { style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 } }
                },
                yaxis: {
                    title: { text: 'Throughput (Req / Sec)', style: { color: '#94a3b8' } },
                    labels: { style: { color: '#94a3b8' } }
                },
                legend: { show: false },
                grid: { borderColor: '#334155', strokeDashArray: 4 }
            };

            if (benchmarkChart) {
                benchmarkChart.updateOptions(options);
            } else {
                benchmarkChart = new ApexCharts(document.querySelector("#benchmarkChart"), options);
                benchmarkChart.render();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initBenchmarkChart();

            document.getElementById('select-requests').addEventListener('change', (e) => {
                document.getElementById('btn-batch-badge').textContent = e.target.value + ' Reqs';
            });
        });

        async function launchStressTest() {
            const btn = document.getElementById('btn-launch-test');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> <span>Executing Stress Test...</span>`;
            btn.disabled = true;

            const requests = document.getElementById('select-requests').value;
            const concurrency = document.getElementById('select-concurrency').value;
            const type = document.getElementById('select-type').value;

            logConsole(`// 🚀 Launching stress test: ${requests} requests @ concurrency ${concurrency} (${type})...`);

            try {
                const res = await fetch('/octane/benchmark/run', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ requests, concurrency, type })
                });
                const data = await res.json();

                if (data.status) {
                    const oct = data.octane_results;
                    const fpm = data.fpm_comparison;
                    const diff = data.performance_differential;

                    document.getElementById('stat-octane-rps').textContent = oct.requests_per_second.toLocaleString() + ' RPS';
                    document.getElementById('stat-avg-latency').textContent = oct.avg_latency_ms + ' ms';
                    document.getElementById('stat-p99-latency').textContent = oct.p99_latency_ms + ' ms';
                    document.getElementById('stat-rps-gain').textContent = diff.throughput_multiplier;

                    document.getElementById('stat-fpm-rps').textContent = fpm.requests_per_second.toLocaleString() + ' RPS';
                    document.getElementById('stat-fpm-latency').textContent = 'Avg Latency: ' + fpm.avg_latency_ms + ' ms';

                    document.getElementById('stat-octane-compare').textContent = oct.requests_per_second.toLocaleString() + ' RPS';
                    document.getElementById('stat-octane-latency').textContent = 'Avg Latency: ' + oct.avg_latency_ms + ' ms';

                    document.getElementById('chart-rps-badge').textContent = diff.throughput_multiplier;

                    initBenchmarkChart(fpm.requests_per_second, oct.requests_per_second);
                    logConsole(JSON.stringify(data, null, 2));
                }
            } catch (err) {
                logConsole("// ❌ Stress Test Error: " + err.message);
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function logConsole(text) {
            const el = document.getElementById('console-output');
            el.innerHTML = text + "\n\n" + el.innerHTML;
        }
    </script>
</body>
</html>
