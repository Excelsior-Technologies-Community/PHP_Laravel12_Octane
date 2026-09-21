<?php

namespace Tests\Feature;

use Tests\TestCase;

class OctaneFeatureTest extends TestCase
{
    /**
     * Test Octane Monitor Dashboard loads successfully.
     */
    public function test_octane_monitor_dashboard_loads(): void
    {
        $response = $this->get('/octane-monitor');
        $response->assertStatus(200);
    }

    /**
     * Test Octane Concurrent Tasks view & benchmark execution.
     */
    public function test_octane_concurrent_view_and_benchmark(): void
    {
        $viewResponse = $this->get('/octane/concurrent');
        $viewResponse->assertStatus(200);

        $benchmarkResponse = $this->getJson('/octane/concurrent/run');
        $benchmarkResponse->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'summary' => [
                    'sequential_time_ms',
                    'concurrent_time_ms',
                    'speedup_multiplier',
                ],
                'tasks',
            ]);
    }

    /**
     * Test Octane Fire-and-Forget background dispatch.
     */
    public function test_octane_background_async_dispatch(): void
    {
        $response = $this->postJson('/octane/background/run', [
            'task_name' => 'Automated Test Background Task',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => true]);
    }

    /**
     * Test Octane In-Memory Cache view, warmup, and benchmark.
     */
    public function test_octane_cache_warmup_and_benchmark(): void
    {
        $viewResponse = $this->get('/octane/cache');
        $viewResponse->assertStatus(200);

        $warmupResponse = $this->postJson('/octane/cache/warmup');
        $warmupResponse->assertStatus(200)
            ->assertJson(['status' => true]);

        $benchmarkResponse = $this->postJson('/octane/cache/benchmark', ['items' => 100]);
        $benchmarkResponse->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'standard_cache',
                'octane_memory_cache',
                'performance_gain',
            ]);
    }

    /**
     * Test Octane Stress Test Benchmarking Suite.
     */
    public function test_octane_stress_benchmark(): void
    {
        $viewResponse = $this->get('/octane/benchmark');
        $viewResponse->assertStatus(200);

        $stressResponse = $this->postJson('/octane/benchmark/run', [
            'requests' => 50,
            'concurrency' => 5,
            'type' => 'json_endpoint',
        ]);

        $stressResponse->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'octane_results' => [
                    'requests_per_second',
                    'avg_latency_ms',
                ],
                'fpm_comparison',
                'performance_differential',
            ]);
    }

    /**
     * Test Artisan octane:warmup command.
     */
    public function test_artisan_octane_warmup_command(): void
    {
        $this->artisan('octane:warmup')
            ->assertSuccessful();
    }
}
