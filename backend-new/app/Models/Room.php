<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $building_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Building $building
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cctv[] $cctvs
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Room extends Model
{
    protected $fillable = [
        'building_id',
        'name',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function cctvs(): HasMany
    {
        return $this->hasMany(Cctv::class);
    }

    // Clear cache when room is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::creating(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::updating(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::retrieved(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::saving(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::saved(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::created(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::updated(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });

        static::deleted(function ($room) {
            \App\Models\Building::clearAllCaches();
            self::clearAllCaches();
        });
    }

    // Method to clear all relevant caches
    public static function clearAllCaches()
    {
        // Delegate to Building model's clearAllCaches method for now
        \App\Models\Building::clearAllCaches();
    }
}
