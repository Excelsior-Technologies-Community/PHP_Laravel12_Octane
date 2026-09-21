<?php

namespace App\Console\Commands;

use App\Services\OctaneWarmupService;
use Illuminate\Console\Command;

class OctaneWarmupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'octane:warmup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preload application state, configuration, and frequent database queries into Octane in-memory cache';

    /**
     * Execute the console command.
     */
    public function handle(OctaneWarmupService $warmupService): int
    {
        $this->info('⚡ Starting Laravel 12 Octane State & Cache Warmup...');

        $result = $warmupService->warmup();

        $tableData = [];
        foreach ($result['warmed_modules'] as $module => $info) {
            $tableData[] = [
                ucwords(str_replace('_', ' ', $module)),
                $info['key'],
                $info['items_warmed'],
                $info['status'],
            ];
        }

        $this->table(['Module', 'Cache Key', 'Items Count', 'Status'], $tableData);

        $this->newLine();
        $this->info("✓ Warmup completed in {$result['duration_ms']} ms! (Memory allocated: {$result['memory_consumed_formatted']})");

        return Command::SUCCESS;
    }
}
