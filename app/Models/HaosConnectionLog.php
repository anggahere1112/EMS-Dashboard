<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HaosConnectionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'haos_instance_id',
        'status',
        'error_message',
        'response_time_ms',
        'devices_synced',
    ];

    protected $casts = [
        'response_time_ms' => 'integer',
        'devices_synced' => 'integer',
    ];

    public function haosInstance()
    {
        return $this->belongsTo(HaosInstance::class);
    }
}
