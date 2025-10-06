<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'haos_instance_id',
        'name',
        'device_type',
        'physical_device_name',
        'entity_id',
        'zone_id',
        'level_id',
        'space_id',
        'location_id',
        'sub_location_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the HAOS instance that owns the device.
     */
    public function haosInstance(): BelongsTo
    {
        return $this->belongsTo(HaosInstance::class);
    }

    /**
     * Get the entity that owns the device.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the zone location.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'zone_id');
    }

    /**
     * Get the level location.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'level_id');
    }

    /**
     * Get the space location.
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'space_id');
    }

    /**
     * Get the location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Get the sub location.
     */
    public function subLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'sub_location_id');
    }

    /**
     * Get the device logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }

    /**
     * Get the latest device log.
     */
    public function latestLog(): HasOne
    {
        return $this->hasOne(DeviceLog::class)->latest();
    }

    /**
     * Get all UIDs associated with this device.
     */
    public function deviceUIDs(): HasMany
    {
        return $this->hasMany(DeviceUID::class);
    }

    /**
     * Get the primary UID for this device.
     */
    public function primaryUID(): HasOne
    {
        return $this->hasOne(DeviceUID::class)->where('is_primary', true);
    }
}
