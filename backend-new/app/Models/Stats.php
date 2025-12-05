<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $metric
 * @property string $value
 * @property \Illuminate\Support\Carbon $timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Stats extends Model
{
    protected $fillable = [
        'metric',
        'value',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    // Clear cache when stats is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::creating(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::updating(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::retrieved(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::saving(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::saved(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::created(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::updated(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });

        static::deleted(function ($stats) {
            \App\Models\Building::clearAllCaches();
        });
    }
}
