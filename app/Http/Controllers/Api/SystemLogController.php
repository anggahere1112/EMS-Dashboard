<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HaosInstance;
use App\Models\HaosSystemLog;
use App\Services\HaosApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SystemLogController extends Controller
{
    /**
     * Display a listing of system logs
     */
    public function index(Request $request): JsonResponse
    {
        $query = HaosSystemLog::with('haosInstance');

        // Filter by HAOS instance
        if ($request->has('haos_instance_id')) {
            $query->where('haos_instance_id', $request->haos_instance_id);
        }

        // Filter by UID (system component)
        if ($request->has('uid')) {
            $query->where('uid', 'like', "%{$request->uid}%");
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        $logs = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Display the specified system log
     */
    public function show(string $id): JsonResponse
    {
        $log = HaosSystemLog::with('haosInstance')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    /**
     * Sync system logs from HAOS
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

            $result = $service->syncSystemData();
            
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
     * Get system log statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_logs' => HaosSystemLog::count(),
            'logs_today' => HaosSystemLog::whereDate('created_at', today())->count(),
            'logs_this_week' => HaosSystemLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'by_haos_instance' => HaosSystemLog::with('haosInstance:id,name')
                ->selectRaw('haos_instance_id, COUNT(*) as count')
                ->groupBy('haos_instance_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->haosInstance->name ?? 'Unknown' => $item->count];
                }),
            'by_component' => HaosSystemLog::selectRaw('uid, COUNT(*) as count')
                ->groupBy('uid')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'uid'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get system health overview
     */
    public function health(): JsonResponse
    {
        $haosInstances = HaosInstance::all();
        $health = [];

        foreach ($haosInstances as $instance) {
            $service = new HaosApiService($instance);
            $isConnected = $service->testConnection();
            
            $recentLogs = HaosSystemLog::where('haos_instance_id', $instance->id)
                ->where('created_at', '>=', now()->subHours(1))
                ->count();

            $health[] = [
                'instance' => $instance->name,
                'ip_address' => $instance->ip_address,
                'is_connected' => $isConnected,
                'is_active' => $instance->is_active,
                'last_connected_at' => $instance->last_connected_at,
                'recent_logs_count' => $recentLogs,
                'status' => $isConnected ? 'online' : 'offline',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $health,
        ]);
    }

    /**
     * Clean old system logs
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:365',
        ]);

        $days = $request->get('days', 30);
        $cutoffDate = now()->subDays($days);

        $deletedCount = HaosSystemLog::where('created_at', '<', $cutoffDate)->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deletedCount} old system logs older than {$days} days",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Get HAOS statistics for dashboard
     */
    public function haosStats(Request $request): JsonResponse
    {
        $query = HaosInstance::with(['devices', 'systemLogs']);
        
        // Filter by entity if provided
        if ($request->has('entity') && $request->entity !== 'ALL') {
            $query->where('name', 'like', "%{$request->entity}%");
        }
        
        $instances = $query->get();
        
        $stats = [];
        
        foreach ($instances as $instance) {
            $recentLogs = $instance->systemLogs()
                ->where('created_at', '>=', now()->subHour())
                ->count();
                
            $isConnected = $instance->is_active && 
                          $instance->last_connected_at && 
                          $instance->last_connected_at >= now()->subMinutes(5);
            
            // Get system metrics from latest logs if available
            $metrics = [
                'memory' => 0,
                'cpu' => 0,
                'disk' => 0,
                'uptime' => 0,
            ];
            
            // Get latest system logs for this instance
            $latestLogs = $instance->systemLogs()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            foreach ($latestLogs as $log) {
                if (str_contains($log->uid, 'memory_usage')) {
                    $metrics['memory'] = (float) $log->state;
                } elseif (str_contains($log->uid, 'processor_use')) {
                    $metrics['cpu'] = (float) $log->state;
                } elseif (str_contains($log->uid, 'disk_usage')) {
                    $metrics['disk'] = (float) $log->state;
                } elseif (str_contains($log->uid, 'last_boot')) {
                    // Calculate uptime in days from last boot
                    try {
                        $lastBoot = \Carbon\Carbon::parse($log->state);
                        $metrics['uptime'] = round($lastBoot->diffInDays(now(), false), 1);
                    } catch (\Exception $e) {
                        $metrics['uptime'] = 0;
                    }
                }
            }
            
            $stats[] = [
                'name' => $instance->name,
                'entity' => $instance->name, // Assuming instance name corresponds to entity
                'is_active' => $isConnected,
                'device_count' => $instance->devices->count(),
                'recent_logs' => $recentLogs,
                'metrics' => $metrics,
                'last_seen' => $instance->last_connected_at,
            ];
        }
        
        // Calculate summary stats
        $summary = [
            'total_instances' => $instances->count(),
            'active_instances' => collect($stats)->where('is_active', true)->count(),
            'inactive_instances' => collect($stats)->where('is_active', false)->count(),
            'total_devices' => collect($stats)->sum('device_count'),
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'instances' => $stats,
                'summary' => $summary,
            ],
        ]);
    }
}
