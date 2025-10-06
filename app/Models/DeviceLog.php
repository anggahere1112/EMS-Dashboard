<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'entity_id',
        'location_id',
        'uid',
        'state',
        'unit',
        'last_changed',
        'last_reported',
        'comparison_hash',
    ];

    protected $casts = [
        'last_changed' => 'datetime',
        'last_reported' => 'datetime',
    ];

    /**
     * Get the device that owns the log.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the entity for this log entry.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the location for this log entry.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
