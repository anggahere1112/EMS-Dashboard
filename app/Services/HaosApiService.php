<?php

namespace App\Services;

use App\Models\Device;
use App\Models\DeviceUID;
use App\Models\DeviceLog;
use App\Models\HaosInstance;
use App\Models\HaosSystemLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HaosApiService
{
    protected $haosInstance;
    protected $baseUrl;
    protected $bearerToken;

    public function __construct(HaosInstance $haosInstance)
    {
        $this->haosInstance = $haosInstance;
        $this->baseUrl = "http://{$haosInstance->ip_address}:{$haosInstance->port}/api";
        $this->bearerToken = $haosInstance->bearer_token;
    }

    /**
     * Test connection to HAOS API
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(10)->get("{$this->baseUrl}/");

            if ($response->successful()) {
                $this->haosInstance->update([
                    'is_active' => true,
                    'last_connected_at' => now(),
                ]);
                return true;
            }

            $this->haosInstance->update(['is_active' => false]);
            return false;
        } catch (\Exception $e) {
            Log::error("HAOS Connection Error: " . $e->getMessage());
            $this->haosInstance->update(['is_active' => false]);
            return false;
        }
    }

    /**
     * Sync device data from HAOS
     */
    public function syncDeviceData(): array
    {
        try {
            $template = $this->getDeviceDataTemplate();
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/template", [
                'template' => $template
            ]);

            if ($response->successful()) {
                $deviceData = $response->json();
                return $this->processDeviceData($deviceData);
            }

            throw new \Exception("Failed to fetch device data: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Device Data Sync Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Sync power outage device data from HAOS
     */
    public function syncPowerOutageDeviceData(): array
    {
        try {
            $template = $this->getPowerOutageDeviceDataTemplate();
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/template", [
                'template' => $template
            ]);

            if ($response->successful()) {
                $deviceData = $response->json();
                return $this->processDeviceData($deviceData);
            }

            throw new \Exception("Failed to fetch power outage device data: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Power Outage Device Data Sync Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Sync smoke sensor device data from HAOS
     */
    public function syncSmokeSensorDeviceData(): array
    {
        try {
            $template = $this->getSmokeSensorDeviceDataTemplate();
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/template", [
                'template' => $template
            ]);

            if ($response->successful()) {
                $deviceData = $response->json();
                return $this->processDeviceData($deviceData);
            }

            throw new \Exception("Failed to fetch smoke sensor device data: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Smoke Sensor Device Data Sync Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Sync system data from HAOS
     */
    public function syncSystemData(): array
    {
        try {
            $template = $this->getSystemDataTemplate();
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(30)->post("{$this->baseUrl}/template", [
                'template' => $template
            ]);

            if ($response->successful()) {
                $systemData = $response->json();
                return $this->processSystemData($systemData);
            }

            throw new \Exception("Failed to fetch system data: " . $response->body());
        } catch (\Exception $e) {
            Log::error("System Data Sync Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Control device (turn on/off switches)
     */
    public function controlDevice(string $entityId, string $action): array
    {
        try {
            // Find the device UID to get the actual entity ID for control
            $deviceUID = DeviceUID::where('uid', $entityId)
                ->whereHas('device', function ($query) {
                    $query->where('haos_instance_id', $this->haosInstance->id);
                })
                ->with('device')
                ->first();

            if (!$deviceUID || !$deviceUID->device) {
                throw new \Exception("Device with UID {$entityId} not found");
            }

            $service = $action === 'on' ? 'turn_on' : 'turn_off';
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->bearerToken}",
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$this->baseUrl}/services/switch/{$service}", [
                'entity_id' => $entityId
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => "Device {$entityId} turned {$action}"];
            }

            throw new \Exception("Failed to control device: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Device Control Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get device data template for HAOS API
     */
    protected function getDeviceDataTemplate(): string
    {
        return "[{% for s in states if 'ksv' in s.entity_id and 'haos' not in s.entity_id %}{\"uid\": \"{{ s.entity_id }}\", \"name\": \"{{ s.attributes.friendly_name }}\", \"state\": \"{% if '+00:00' in s.state %}{{ (as_timestamp(s.state) + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}{% else %}{{ s.state }}{% endif %}\", \"unit\": \"{{ s.attributes.unit_of_measurement if s.attributes.unit_of_measurement is defined else '' }}\", \"last_changed\": \"{{ (s.last_changed | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\", \"last_reported\": \"{{ (s.last_reported | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\"}{% if not loop.last %}, {% endif %}{% endfor %}]";
    }

    /**
     * Get power outage device data template for HAOS API
     */
    protected function getPowerOutageDeviceDataTemplate(): string
    {
        return "[{% for s in states if 'ksv' in s.entity_id and 'haos' not in s.entity_id and 'local_power_outage' in s.entity_id %}{\"uid\": \"{{ s.entity_id }}\", \"name\": \"{{ s.attributes.friendly_name }}\", \"state\": \"{% if '+00:00' in s.state %}{{ (as_timestamp(s.state) + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}{% else %}{{ s.state }}{% endif %}\", \"unit\": \"{{ s.attributes.unit_of_measurement if s.attributes.unit_of_measurement is defined else '' }}\", \"last_changed\": \"{{ (s.last_changed | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\", \"last_reported\": \"{{ (s.last_reported | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\"}{% if not loop.last %}, {% endif %}{% endfor %}]";
    }

    /**
     * Get smoke sensor device data template for HAOS API
     */
    protected function getSmokeSensorDeviceDataTemplate(): string
    {
        return "[{% for s in states if 'ksv' in s.entity_id and 'haos' not in s.entity_id and 'smoke_sensor' in s.entity_id %}{\"uid\": \"{{ s.entity_id }}\", \"name\": \"{{ s.attributes.friendly_name }}\", \"state\": \"{% if '+00:00' in s.state %}{{ (as_timestamp(s.state) + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}{% else %}{{ s.state }}{% endif %}\", \"unit\": \"{{ s.attributes.unit_of_measurement if s.attributes.unit_of_measurement is defined else '' }}\", \"last_changed\": \"{{ (s.last_changed | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\", \"last_reported\": \"{{ (s.last_reported | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\"}{% if not loop.last %}, {% endif %}{% endfor %}]";
    }

    /**
     * Get system data template for HAOS API
     */
    protected function getSystemDataTemplate(): string
    {
        return "[{% for s in states if 'haos' in s.entity_id and 'ksv' in s.entity_id %}{\"uid\": \"{{ s.entity_id }}\", \"name\": \"{{ s.attributes.friendly_name }}\", \"state\": \"{% if '+00:00' in s.state %}{{ (as_timestamp(s.state) + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}{% else %}{{ s.state }}{% endif %}\", \"unit\": \"{{ s.attributes.unit_of_measurement if s.attributes.unit_of_measurement is defined else '' }}\", \"last_changed\": \"{{ (s.last_changed | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\", \"last_reported\": \"{{ (s.last_reported | as_timestamp + 28800) | timestamp_custom('%Y-%m-%d %H:%M:%S', false) }}\"}{% if not loop.last %}, {% endif %}{% endfor %}]";
    }

    /**
     * Process device data and save to database
     */
    protected function processDeviceData(array $deviceData): array
    {
        $processed = 0;
        $errors = [];

        foreach ($deviceData as $data) {
            try {
                // Find device through DeviceUID relationship
                $deviceUID = DeviceUID::where('uid', $data['uid'])
                    ->whereHas('device', function ($query) {
                        $query->where('haos_instance_id', $this->haosInstance->id);
                    })
                    ->with('device')
                    ->first();

                if ($deviceUID && $deviceUID->device) {
                    $device = $deviceUID->device;
                    
                    // Create comparison hash based only on state and unit
                    $unit = $data['unit'] ?? '';
                    $comparisonHash = md5($data['state'] . $unit);

                    // Check if the latest record has the same state and unit combination
                    $latestLog = DeviceLog::where('device_id', $device->id)
                        ->where('uid', $data['uid'])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $shouldSave = true;
                    if ($latestLog) {
                        $latestUnit = $latestLog->unit ?? '';
                        $latestComparisonHash = md5($latestLog->state . $latestUnit);
                        
                        // Only save if state or unit has changed
                        if ($latestComparisonHash === $comparisonHash) {
                            $shouldSave = false;
                            
                        }
                    }

                    if ($shouldSave) {
                        DeviceLog::create([
                            'device_id' => $device->id,
                            'entity_id' => $device->entity_id,
                            'location_id' => $device->location_id ?? $device->sub_location_id ?? $device->space_id ?? $device->level_id,
                            'uid' => $data['uid'],
                            'state' => $data['state'],
                            'unit' => $unit ?: null,
                            'last_changed' => Carbon::parse($data['last_changed']),
                            'last_reported' => Carbon::parse($data['last_reported']),
                            'comparison_hash' => $comparisonHash,
                        ]);
                        $processed++;
                    }
                } else {
                    // Log warning for unmatched UID
                    Log::warning("No device found for UID: {$data['uid']} in HAOS instance {$this->haosInstance->id}");
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing {$data['uid']}: " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'processed' => $processed,
            'total' => count($deviceData),
            'errors' => $errors
        ];
    }

    /**
     * Process system data and save to database
     */
    protected function processSystemData(array $systemData): array
    {
        $processed = 0;
        $errors = [];

        foreach ($systemData as $data) {
            try {
                // Create comparison hash
                $comparisonHash = md5($data['state'] . $data['last_changed'] . $data['last_reported']);

                // Check if this exact data already exists
                $existingLog = HaosSystemLog::where('haos_instance_id', $this->haosInstance->id)
                    ->where('uid', $data['uid'])
                    ->where('comparison_hash', $comparisonHash)
                    ->first();

                if (!$existingLog) {
                    HaosSystemLog::create([
                        'haos_instance_id' => $this->haosInstance->id,
                        'uid' => $data['uid'],
                        'state' => $data['state'],
                        'unit' => $data['unit'] ?? null,
                        'last_changed' => Carbon::parse($data['last_changed']),
                        'last_reported' => Carbon::parse($data['last_reported']),
                        'comparison_hash' => $comparisonHash,
                    ]);
                    $processed++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing {$data['uid']}: " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'processed' => $processed,
            'total' => count($systemData),
            'errors' => $errors
        ];
    }
}