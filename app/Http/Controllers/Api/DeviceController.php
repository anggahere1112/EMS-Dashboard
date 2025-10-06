<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceUID;
use App\Models\DeviceLog;
use App\Models\HaosInstance;
use App\Services\HaosApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    /**
     * Display a listing of devices with their latest logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Device::with(['entity', 'haosInstance', 'location', 'latestLog', 'deviceUIDs', 'primaryUID']);

        // Filter by HAOS instance
        if ($request->has('haos_instance_id')) {
            $query->where('haos_instance_id', $request->haos_instance_id);
        }

        // Filter by device type
        if ($request->has('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        // Filter by location
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        // Search by name or UID (now searches in DeviceUID table)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('physical_device_name', 'like', "%{$search}%")
                  ->orWhereHas('deviceUIDs', function($uidQuery) use ($search) {
                      $uidQuery->where('uid', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Display the specified device with its logs
     */
    public function show(string $id): JsonResponse
    {
        $device = Device::with(['entity', 'haosInstance', 'deviceUIDs', 'logs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $device,
        ]);
    }

    /**
     * Get detailed device information for modal display
     */
    public function modalData(string $id): JsonResponse
    {
        $device = Device::with([
            'entity', 
            'haosInstance', 
            'zone', 
            'level', 
            'space', 
            'location', 
            'subLocation',
            'deviceUIDs',
            'primaryUID',
            'latestLog'
        ])->findOrFail($id);

        // Get device values using the same logic as dashboard
        $deviceValues = $this->getDeviceValues($device);
        
        // Build location path
        $locationParts = array_filter([
            $device->entity->name ?? null,
            $device->zone->name ?? null,
            $device->level->name ?? null,
            $device->space->name ?? null,
            $device->location->name ?? null,
            $device->subLocation->name ?? null,
        ]);
        
        $locationPath = !empty($locationParts) ? implode(' > ', $locationParts) : 'Unknown Location';
        
        // Get latest log for additional info
        $latestLog = $device->latestLog;
        $lastSeen = $latestLog ? $latestLog->created_at : null;
        
        // Get all device UIDs with their current values
        $allUIDValues = [];
        $batteryInfo = null;
        
        if ($device->deviceUIDs) {
            foreach ($device->deviceUIDs as $uid) {
                $uidLog = DeviceLog::where('device_id', $device->id)
                    ->where('uid', $uid->uid)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($uidLog) {
                    // Determine display name for UID
                    $displayName = $this->getUIDDisplayName($uid->uid);
                    
                    // Translate the value for better user understanding
                    $translatedValue = $this->translateValue($uid->uid, $uidLog->state, $device->device_type);
                    
                    $uidValue = [
                        'uid' => $uid->uid,
                        'display_name' => $displayName,
                        'value' => $translatedValue,
                        'raw_value' => $uidLog->state, // Keep original value for reference
                        'unit' => $uidLog->unit ?? '',
                        'last_updated' => $uidLog->created_at,
                        'is_available' => $uidLog->state !== 'unavailable'
                    ];
                    
                    $allUIDValues[] = $uidValue;
                    
                    // Keep battery info for backward compatibility
                    if (str_contains($uid->uid, 'battery') && !$batteryInfo) {
                        $batteryInfo = [
                            'value' => $uidLog->state,
                            'unit' => $uidLog->unit ?? '%'
                        ];
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $device->id,
                'name' => $device->name,
                'physical_device_name' => $device->physical_device_name,
                'device_type' => $device->device_type,
                'status' => $deviceValues['status'] ?? 'offline',
                'primary_uid' => $device->primaryUID ? $device->primaryUID->uid : null,
                'location_path' => $locationPath,
                'entity' => $device->entity->name ?? null,
                'zone' => $device->zone->name ?? null,
                'level' => $device->level->name ?? null,
                'space' => $device->space->name ?? null,
                'location' => $device->location->name ?? null,
                'sub_location' => $device->subLocation->name ?? null,
                'haos_instance' => $device->haosInstance->name ?? null,
                'current_value' => $deviceValues['value'] ?? null,
                'current_unit' => $deviceValues['unit'] ?? null,
                'additional_values' => $deviceValues['additional_values'] ?? [],
                'all_uid_values' => $allUIDValues,
                'battery_info' => $batteryInfo,
                'last_seen' => $lastSeen,
                'created_at' => $device->created_at,
                'updated_at' => $device->updated_at,
            ],
        ]);
    }

    /**
     * Get device logs with pagination
     */
    public function logs(string $id, Request $request): JsonResponse
    {
        $device = Device::findOrFail($id);
        
        $query = DeviceLog::where('device_id', $device->id)
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Control device (for switches and controllable devices)
     */
    public function control(string $id, Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:on,off,toggle',
        ]);

        $device = Device::with(['primaryUID', 'deviceUIDs'])->findOrFail($id);
        
        // Check if device is controllable
        if (!in_array($device->device_type, ['switch', 'smart_plug', 'power_outage_switch'])) {
            return response()->json([
                'success' => false,
                'message' => 'Device is not controllable',
            ], 400);
        }

        // Get the primary UID or first switch-type UID for control
        $controlUID = $device->primaryUID;
        if (!$controlUID) {
            // Fallback to first switch-type UID
            $controlUID = $device->deviceUIDs()->where('uid_type', 'switch')->first();
        }

        if (!$controlUID) {
            return response()->json([
                'success' => false,
                'message' => 'No controllable UID found for this device',
            ], 400);
        }

        $haosService = new HaosApiService($device->haosInstance);
        
        $result = $haosService->controlDevice($controlUID->uid, $request->action);

        return response()->json($result);
    }

    /**
     * Sync device data from HAOS
     */
    public function sync(Request $request): JsonResponse
    {
        $haosInstanceId = $request->get('haos_instance_id');
        
        if ($haosInstanceId) {
            $haosInstance = HaosInstance::findOrFail($haosInstanceId);
            $instances = collect([$haosInstance]);
        } else {
            $instances = HaosInstance::where('is_active', true)->get();
        }

        $totalProcessed = 0;
        $errors = [];

        foreach ($instances as $instance) {
            $service = new HaosApiService($instance);
            
            if (!$service->testConnection()) {
                $errors[] = "Failed to connect to {$instance->name}";
                continue;
            }

            $result = $service->syncDeviceData();
            
            if ($result['success']) {
                $totalProcessed += $result['processed'];
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            } else {
                $errors[] = "Sync failed for {$instance->name}: " . $result['error'];
            }
        }

        return response()->json([
            'success' => empty($errors) || $totalProcessed > 0,
            'processed' => $totalProcessed,
            'errors' => $errors,
        ]);
    }

    /**
     * Get device statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_devices' => Device::count(),
            'active_devices' => Device::whereHas('latestLog', function($query) {
                $query->where('created_at', '>=', now()->subHours(24));
            })->count(),
            'by_type' => Device::selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type'),
            'by_haos_instance' => Device::with('haosInstance:id,name')
                ->selectRaw('haos_instance_id, COUNT(*) as count')
                ->groupBy('haos_instance_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->haosInstance->name ?? 'Unknown' => $item->count];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get devices for dashboard with filtering
     */
    public function dashboardDevices(Request $request): JsonResponse
    {
        $query = Device::with(['entity', 'haosInstance', 'zone', 'level', 'space', 'location', 'subLocation', 'latestLog', 'primaryUID']);

        // Apply filters
        if ($request->filled('entity')) {
            $query->whereHas('entity', function($q) use ($request) {
                $q->where('name', $request->entity);
            });
        }

        if ($request->filled('zone')) {
            $query->whereHas('zone', function($q) use ($request) {
                $q->where('name', $request->zone);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('level', function($q) use ($request) {
                $q->where('name', $request->level);
            });
        }

        if ($request->filled('space')) {
            $query->whereHas('space', function($q) use ($request) {
                $q->where('name', $request->space);
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('name', $request->location);
            });
        }

        if ($request->filled('sub_location')) {
            $query->whereHas('subLocation', function($q) use ($request) {
                $q->where('name', $request->sub_location);
            });
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        // Remove status filtering from query - will be applied after processing device values

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('physical_device_name', 'like', "%{$search}%")
                  ->orWhereHas('deviceUIDs', function($uidQuery) use ($search) {
                      $uidQuery->where('uid', 'like', "%{$search}%");
                  });
            });
        }

        $devices = $query->get()->map(function($device) {
            $latestLog = $device->latestLog;
            $status = 'offline';
            $value = null;
            $unit = '';
            
            if ($latestLog && $latestLog->created_at >= now()->subHours(24)) {
                $status = 'active';
                // Check for warning conditions based on log state
                if (str_contains(strtolower($latestLog->state), 'warning') || 
                    str_contains(strtolower($latestLog->state), 'low') ||
                    str_contains(strtolower($latestLog->state), 'battery') ||
                    str_contains(strtolower($latestLog->state), 'alert')) {
                    $status = 'warning';
                }
            }

            // Get device-specific values based on device type and UIDs
            $deviceValues = $this->getDeviceValues($device);
            
            return [
                'id' => $device->id,
                'name' => $device->name,
                'physical_device_name' => $device->physical_device_name,
                'primary_uid' => $device->primaryUID ? $device->primaryUID->uid : null,
                'device_type' => $device->device_type,
                'status' => $deviceValues['status'] ?? $status,
                'value' => $deviceValues['value'] ?? $value,
                'unit' => $deviceValues['unit'] ?? $unit,
                'additional_values' => $deviceValues['additional_values'] ?? [],
                'entity' => $device->entity->name ?? null,
                'zone' => $device->zone->name ?? null,
                'level' => $device->level->name ?? null,
                'space' => $device->space->name ?? null,
                'location' => $device->location->name ?? null,
                'sub_location' => $device->subLocation->name ?? null,
                'haos_instance' => $device->haosInstance->name ?? null,
                'last_seen' => $latestLog ? $latestLog->created_at : null,
            ];
        });

        // Apply status filter after processing device values
        if ($request->filled('status')) {
            $devices = $devices->filter(function($device) use ($request) {
                return $device['status'] === $request->status;
            })->values(); // Re-index the collection to ensure array format
        }

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $query = Device::with(['entity', 'haosInstance', 'zone', 'level', 'space', 'location', 'subLocation', 'latestLog']);

        // Apply same filters as dashboardDevices
        if ($request->filled('entity')) {
            $query->whereHas('entity', function($q) use ($request) {
                $q->where('name', $request->entity);
            });
        }

        if ($request->filled('zone')) {
            $query->whereHas('zone', function($q) use ($request) {
                $q->where('name', $request->zone);
            });
        }

        if ($request->filled('level')) {
            $query->whereHas('level', function($q) use ($request) {
                $q->where('name', $request->level);
            });
        }

        if ($request->filled('space')) {
            $query->whereHas('space', function($q) use ($request) {
                $q->where('name', $request->space);
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('name', $request->location);
            });
        }

        if ($request->filled('sub_location')) {
            $query->whereHas('subLocation', function($q) use ($request) {
                $q->where('name', $request->sub_location);
            });
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        $devices = $query->get();
        
        $stats = [
            'total' => $devices->count(),
            'active' => 0,
            'offline' => 0,
            'warning' => 0,
        ];

        foreach ($devices as $device) {
            $latestLog = $device->latestLog;
            $status = 'offline';
            
            if ($latestLog && $latestLog->created_at >= now()->subHours(24)) {
                $status = 'active';
                // Check for warning conditions based on log state
                if (str_contains(strtolower($latestLog->state), 'warning') || 
                    str_contains(strtolower($latestLog->state), 'low') ||
                    str_contains(strtolower($latestLog->state), 'battery') ||
                    str_contains(strtolower($latestLog->state), 'alert')) {
                    $status = 'warning';
                }
            }

            // Get device-specific values based on device type and UIDs
            $deviceValues = $this->getDeviceValues($device);
            
            // Use the processed status from getDeviceValues if available
            $finalStatus = $deviceValues['status'] ?? $status;
            
            if ($finalStatus === 'warning') {
                $stats['warning']++;
            } elseif ($finalStatus === 'active') {
                $stats['active']++;
            } else {
                $stats['offline']++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get hierarchy data for filters
     */
    public function hierarchy(Request $request): JsonResponse
    {
        $query = Device::with(['entity', 'zone', 'level', 'space', 'location', 'subLocation']);

        // Apply parent filters to get relevant children
        if ($request->filled('entity')) {
            $query->whereHas('entity', function($q) use ($request) {
                $q->where('name', $request->entity);
            });
        }

        if ($request->filled('zone')) {
            $query->whereHas('zone', function($q) use ($request) {
                $q->where('name', $request->zone);
            });
        }

        // Continue with other filters...

        $devices = $query->get();

        $hierarchy = [
            'entities' => $devices->pluck('entity.name')->filter()->unique()->sort()->values(),
            'zones' => $devices->pluck('zone.name')->filter()->unique()->sort()->values(),
            'levels' => $devices->pluck('level.name')->filter()->unique()->sort()->values(),
            'spaces' => $devices->pluck('space.name')->filter()->unique()->sort()->values(),
            'locations' => $devices->pluck('location.name')->filter()->unique()->sort()->values(),
            'sub_locations' => $devices->pluck('subLocation.name')->filter()->unique()->sort()->values(),
            'device_types' => $devices->pluck('device_type')->filter()->unique()->sort()->values(),
        ];

        return response()->json([
            'success' => true,
            'data' => $hierarchy,
        ]);
    }

    /**
     * Get device-specific values based on device type and latest logs
     */
    private function getDeviceValues($device)
    {
        $result = [
            'status' => 'offline',
            'value' => null,
            'unit' => '',
            'additional_values' => []
        ];

        // Get all logs for this device from the last 24 hours
        $logs = DeviceLog::where('device_id', $device->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->get()
            ->keyBy('uid');

        if ($logs->isEmpty()) {
            return $result;
        }

        // Determine device type and extract appropriate values
        switch (strtolower($device->device_type)) {
            case 'smoke detector':
                return $this->getSmokeDetectorValues($logs);
            
            case 'sensor suhu':
                return $this->getTemperatureSensorValues($logs);
            
            case 'power outage detector':
                return $this->getPowerOutageDetectorValues($logs);
            
            case 'smart plug':
                return $this->getSmartPlugValues($logs);
            
            case 'smart energy meter':
                return $this->getSmartEnergyMeterValues($logs);
            
            default:
                // Default behavior for unknown device types
                $latestLog = $logs->sortByDesc('created_at')->first();
                if ($latestLog) {
                    // Check if device is unavailable (offline)
                    if ($latestLog->state === 'unavailable') {
                        $result['status'] = 'offline';
                        $result['value'] = 'Offline';
                    } else {
                        $result['status'] = 'active';
                        $result['value'] = $latestLog->state;
                        $result['unit'] = $latestLog->unit ?? '';
                    }
                }
                return $result;
        }
    }

    private function getSmokeDetectorValues($logs)
    {
        $smokeLog = $logs->get('binary_sensor.smoke_sensor_1_ksv');
        $batteryLog = $logs->get('sensor.battery_smoke_sensor_1_ksv');
        
        $result = [
            'status' => 'offline',
            'value' => 'Unknown',
            'unit' => '',
            'additional_values' => []
        ];

        if ($smokeLog) {
            // Check if device is unavailable (offline)
            if ($smokeLog->state === 'unavailable') {
                $result['status'] = 'offline';
                $result['value'] = 'Offline';
            } else {
                $result['status'] = 'active';
                $result['value'] = $smokeLog->state === 'on' ? 'Alert' : 'Clear';
                
                if ($smokeLog->state === 'on') {
                    $result['status'] = 'warning';
                }
            }
        }

        if ($batteryLog && $batteryLog->state !== 'unavailable') {
            $result['additional_values']['battery'] = [
                'value' => $batteryLog->state,
                'unit' => $batteryLog->unit ?? '%'
            ];
        }

        return $result;
    }

    /**
     * Get display name for UID
     */
    private function getUIDDisplayName($uid)
    {
        // Specific UID mappings for exact matches
        $specificMappings = [
            'binary_sensor.smoke_sensor_1_ksv' => 'Smoke Detection',
            'sensor.amount_smoke_sensor_1_ksv' => 'Smoke Amount',
            'switch.local_power_outage_1_ksv' => 'Power Status'
        ];
        
        // Check for exact UID matches first
        if (isset($specificMappings[$uid])) {
            return $specificMappings[$uid];
        }
        
        // Map common UID patterns to user-friendly names
        $uidMappings = [
            'temperature' => 'Temperature',
            'humidity' => 'Humidity',
            'battery' => 'Battery',
            'smoke_sensor' => 'Smoke Detection',
            'binary_sensor' => 'Status',
            'total_energy' => 'Total Energy',
            'current' => 'Current',
            'voltage' => 'Voltage',
            'power_outage' => 'Power Status',
            'switch' => 'Switch Status'
        ];
        
        // Find matching pattern in UID
        foreach ($uidMappings as $pattern => $displayName) {
            if (str_contains(strtolower($uid), $pattern)) {
                return $displayName;
            }
        }
        
        // Fallback: clean up the UID for display
        $cleanName = str_replace(['sensor.', 'binary_sensor.', 'switch.', '_'], [' ', ' ', ' ', ' '], $uid);
        $cleanName = ucwords(trim($cleanName));
        
        return $cleanName;
    }

    /**
     * Translate raw values to user-friendly display values
     */
    private function translateValue($uid, $value, $deviceType = null)
    {
        // Handle specific UID value translations
        $translations = [
            'binary_sensor.smoke_sensor_1_ksv' => [
                'on' => 'Smoke Detected',
                'off' => 'Clear'
            ],
            'switch.local_power_outage_1_ksv' => [
                'on' => 'Normal',
                'off' => 'Power Outage Detected'
            ]
        ];
        
        // Check for exact UID and value match
        if (isset($translations[$uid]) && isset($translations[$uid][$value])) {
            return $translations[$uid][$value];
        }
        
        // Special handling for Power Outage Detector when offline
        if ($deviceType === 'power_outage_detector' && ($value === 'unavailable' || $value === 'offline')) {
            return 'Power Outage Detected';
        }
        
        // Return original value if no translation found
        return $value;
    }

    private function getTemperatureSensorValues($logs)
    {
        $tempLog = $logs->get('sensor.temperature_1_ksv');
        $humidityLog = $logs->get('sensor.humidity_1_ksv');
        
        $result = [
            'status' => 'offline',
            'value' => 'N/A',
            'unit' => '',
            'additional_values' => []
        ];

        if ($tempLog) {
            // Check if device is unavailable (offline)
            if ($tempLog->state === 'unavailable') {
                $result['status'] = 'offline';
                $result['value'] = 'Offline';
            } else {
                $result['status'] = 'active';
                $result['value'] = $tempLog->state;
                $result['unit'] = $tempLog->unit ?? '°C';
            }
        }

        if ($humidityLog && $humidityLog->state !== 'unavailable') {
            $result['additional_values']['humidity'] = [
                'value' => $humidityLog->state,
                'unit' => $humidityLog->unit ?? '%'
            ];
        }

        return $result;
    }

    private function getPowerOutageDetectorValues($logs)
    {
        $power1Log = $logs->get('switch.local_power_outage_1_ksv');
        $power2Log = $logs->get('switch.local_power_outage_2_ksv');
        
        $result = [
            'status' => 'offline',
            'value' => 'Power Outage Detected', // Default to power outage when offline
            'unit' => '',
            'additional_values' => []
        ];

        // Check both power outage sensors
        $sensors = [$power1Log, $power2Log];
        $activeCount = 0;
        $warningCount = 0;
        
        foreach ($sensors as $log) {
            if ($log) {
                if ($log->state === 'unavailable') {
                    // Sensor is offline - continue to check other sensors
                    continue;
                } elseif ($log->state === 'off') {
                    $warningCount++;
                } else {
                    $activeCount++;
                }
            }
        }

        if ($activeCount > 0 || $warningCount > 0) {
            if ($warningCount > 0) {
                $result['status'] = 'warning';
                $result['value'] = 'Power Outage Detected';
            } else {
                $result['status'] = 'active';
                $result['value'] = 'Normal';
            }
        } else {
            // All sensors are offline or unavailable - show as offline with power outage detected
            $result['status'] = 'offline';
            $result['value'] = 'Power Outage Detected';
        }

        return $result;
    }

    private function getSmartPlugValues($logs)
    {
        $plugLog = $logs->get('switch.switch_smart_plug_1_ksv');
        $energyLog = $logs->get('sensor.total_energy_smart_plug_1_ksv');
        
        $result = [
            'status' => 'offline',
            'value' => 'Unknown',
            'unit' => '',
            'additional_values' => []
        ];

        if ($plugLog) {
            // Check if device is unavailable (offline)
            if ($plugLog->state === 'unavailable') {
                $result['status'] = 'offline';
                $result['value'] = 'Offline';
            } else {
                $result['status'] = 'active';
                $result['value'] = ucfirst($plugLog->state);
            }
        }

        if ($energyLog && $energyLog->state !== 'unavailable') {
            $result['additional_values']['energy'] = [
                'value' => $energyLog->state,
                'unit' => $energyLog->unit ?? 'kWh'
            ];
        }

        return $result;
    }

    private function getSmartEnergyMeterValues($logs)
    {
        $energyLog = $logs->get('sensor.total_energy_smart_meter_1_ksv');
        $currentLog = $logs->get('sensor.current_smart_meter_1_ksv');
        $voltageLog = $logs->get('sensor.voltage_smart_meter_1_ksv');
        
        $result = [
            'status' => 'offline',
            'value' => 'N/A',
            'unit' => '',
            'additional_values' => []
        ];

        if ($energyLog) {
            // Check if device is unavailable (offline)
            if ($energyLog->state === 'unavailable') {
                $result['status'] = 'offline';
                $result['value'] = 'Offline';
            } else {
                $result['status'] = 'active';
                $result['value'] = $energyLog->state;
                $result['unit'] = $energyLog->unit ?? 'kWh';
            }
        }

        if ($currentLog && $currentLog->state !== 'unavailable') {
            $result['additional_values']['current'] = [
                'value' => $currentLog->state,
                'unit' => $currentLog->unit ?? 'A'
            ];
        }

        if ($voltageLog && $voltageLog->state !== 'unavailable') {
            $result['additional_values']['voltage'] = [
                'value' => $voltageLog->state,
                'unit' => $voltageLog->unit ?? 'V'
            ];
        }

        return $result;
    }
}
