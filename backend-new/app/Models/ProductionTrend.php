<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $date
 * @property float $production
 * @property float $target
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class ProductionTrend extends Model
{
    protected $table = 'production_trends';
    
    protected $fillable = [
        'date',
        'production',
        'target',
        'traffic_volume',
        'average_speed',
        'incidents',
        'congestion_index',
        'signal_changes',
        'green_wave_efficiency',
    ];

    protected $casts = [
        'date' => 'date',
        'production' => 'decimal:2',
        'target' => 'decimal:2',
        'traffic_volume' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'incidents' => 'integer',
        'congestion_index' => 'decimal:2',
        'signal_changes' => 'integer',
        'green_wave_efficiency' => 'decimal:2',
    ];

    // Clear cache when production trend is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::creating(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::updating(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::retrieved(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::saving(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::saved(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::created(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::updated(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });

        static::deleted(function ($productionTrend) {
            \App\Models\Building::clearAllCaches();
        });
    }
}