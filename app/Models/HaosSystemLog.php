<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HaosSystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'haos_instance_id',
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
     * Get the HAOS instance that owns the log.
     */
    public function haosInstance(): BelongsTo
    {
        return $this->belongsTo(HaosInstance::class);
    }
}
