<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceUID extends Model
{
    use HasFactory;

    protected $table = 'device_uids';

    protected $fillable = [
        'device_id',
        'uid',
        'uid_type',
        'description',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the device that owns this UID.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
