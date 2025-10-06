<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the HAOS instances for the entity.
     */
    public function haosInstances(): HasMany
    {
        return $this->hasMany(HaosInstance::class);
    }

    /**
     * Get the locations for the entity.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the devices for the entity.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
