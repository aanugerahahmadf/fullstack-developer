<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $unit_name
 * @property int $efficiency
 * @property int $capacity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class UnitPerformance extends Model
{
    protected $table = 'unit_performance';
    
    protected $fillable = [
        'unit_name',
        'efficiency',
        'capacity',
    ];

    protected $casts = [
        'efficiency' => 'integer',
        'capacity' => 'integer',
    ];

    // Clear cache when unit performance is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::creating(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::updating(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::retrieved(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::saving(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::saved(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::created(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::updated(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });

        static::deleted(function ($unitPerformance) {
            \App\Models\Building::clearAllCaches();
        });
    }
}