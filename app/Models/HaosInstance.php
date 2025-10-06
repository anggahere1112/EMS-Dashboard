<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HaosInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'name',
        'ip_address',
        'port',
        'bearer_token',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
    ];

    /**
     * Get the entity that owns the HAOS instance.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the devices for the HAOS instance.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get the connection logs for the HAOS instance.
     */
    public function connectionLogs(): HasMany
    {
        return $this->hasMany(HaosConnectionLog::class);
    }

    /**
     * Get the system logs for the HAOS instance.
     */
    public function systemLogs(): HasMany
    {
        return $this->hasMany(HaosSystemLog::class);
    }
}
