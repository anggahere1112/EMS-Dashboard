<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'name',
        'type',
        'parent_id',
        'description',
    ];

    /**
     * Get the entity that owns the location.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the parent location.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Get the child locations.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Get devices assigned to this zone (for zone type locations).
     */
    public function devicesAsZone(): HasMany
    {
        return $this->hasMany(Device::class, 'zone_id');
    }

    /**
     * Get devices assigned to this level (for level type locations).
     */
    public function devicesAsLevel(): HasMany
    {
        return $this->hasMany(Device::class, 'level_id');
    }

    /**
     * Get devices assigned to this space (for space type locations).
     */
    public function devicesAsSpace(): HasMany
    {
        return $this->hasMany(Device::class, 'space_id');
    }

    /**
     * Get devices assigned to this location (for location type locations).
     */
    public function devicesAsLocation(): HasMany
    {
        return $this->hasMany(Device::class, 'location_id');
    }

    /**
     * Get devices assigned to this sub location (for sub_location type locations).
     */
    public function devicesAsSubLocation(): HasMany
    {
        return $this->hasMany(Device::class, 'sub_location_id');
    }

    /**
     * Get the full hierarchical path for this location.
     * Example: "Kantor Surabaya/Zona A/Level 1/Room 101/Desk 12"
     */
    public function getFullPath(): string
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }
        
        return implode('/', $path);
    }

    /**
     * Get device logs for this location.
     */
    public function deviceLogs(): HasMany
    {
        return $this->hasMany(DeviceLog::class, 'location_id');
    }
}
