<?php

namespace App\Console\Commands;

use App\Models\HaosInstance;
use App\Services\HaosApiService;
use Illuminate\Console\Command;

class SyncHaosData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haos:sync {--instance=* : Specific HAOS instance IDs to sync} {--devices : Sync device data only} {--system : Sync system data only} {--power-outage : Sync power outage devices only} {--smoke-sensor : Sync smoke sensor devices only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync device and system data from HAOS instances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting HAOS data synchronization...');

        // Get HAOS instances to sync
        $instances = $this->getInstancesToSync();

        if ($instances->isEmpty()) {
            $this->warn('No HAOS instances found to sync.');
            return Command::FAILURE;
        }

        $totalProcessed = 0;
        $totalErrors = 0;

        foreach ($instances as $instance) {
            $this->info("Syncing data for HAOS instance: {$instance->name} ({$instance->ip_address})");

            $service = new HaosApiService($instance);

            // Test connection first
            if (!$service->testConnection()) {
                $this->error("Failed to connect to HAOS instance: {$instance->name}");
                $totalErrors++;
                continue;
            }

            // Sync device data
            if (!$this->option('system')) {
                if ($this->option('power-outage')) {
                    $this->info('Syncing power outage device data...');
                    $deviceResult = $service->syncPowerOutageDeviceData();
                } elseif ($this->option('smoke-sensor')) {
                    $this->info('Syncing smoke sensor device data...');
                    $deviceResult = $service->syncSmokeSensorDeviceData();
                } else {
                    $this->info('Syncing device data...');
                    $deviceResult = $service->syncDeviceData();
                }
                
                if ($deviceResult['success']) {
                    $processed = $deviceResult['processed'];
                    $total = $deviceResult['total'];
                    $this->info("Device data: {$processed}/{$total} records processed");
                    $totalProcessed += $processed;
                    
                    if (!empty($deviceResult['errors'])) {
                        foreach ($deviceResult['errors'] as $error) {
                            $this->warn($error);
                        }
                    }
                } else {
                    $this->error("Device sync failed: " . $deviceResult['error']);
                    $totalErrors++;
                }
            }

            // Sync system data (skip if power-outage or smoke-sensor option is used)
            if (!$this->option('devices') && !$this->option('power-outage') && !$this->option('smoke-sensor')) {
                $this->info('Syncing system data...');
                $systemResult = $service->syncSystemData();
                
                if ($systemResult['success']) {
                    $processed = $systemResult['processed'];
                    $total = $systemResult['total'];
                    $this->info("System data: {$processed}/{$total} records processed");
                    $totalProcessed += $processed;
                    
                    if (!empty($systemResult['errors'])) {
                        foreach ($systemResult['errors'] as $error) {
                            $this->warn($error);
                        }
                    }
                } else {
                    $this->error("System sync failed: " . $systemResult['error']);
                    $totalErrors++;
                }
            }

            $this->line('---');
        }

        $this->info("Synchronization completed!");
        $this->info("Total records processed: {$totalProcessed}");
        
        if ($totalErrors > 0) {
            $this->warn("Total errors: {$totalErrors}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Get HAOS instances to sync based on options
     */
    protected function getInstancesToSync()
    {
        $instanceIds = $this->option('instance');

        if (!empty($instanceIds)) {
            return HaosInstance::whereIn('id', $instanceIds)->get();
        }

        return HaosInstance::where('is_active', true)->get();
    }
}
