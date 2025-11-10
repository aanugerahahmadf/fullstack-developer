<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\BuildingService;

/**
 * @property int $id
 * @property string $name
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $marker_icon_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Room[] $rooms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cctv[] $cctvs
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Building extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'marker_icon_url',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function cctvs(): HasMany
    {
        return $this->hasMany(Cctv::class);
    }

    // Clear cache when building is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::created(function ($building) {
            static::clearAllCaches();
        });

        static::updated(function ($building) {
            static::clearAllCaches();
        });

        static::deleted(function ($building) {
            static::clearAllCaches();
        });
    }

    // Method to clear all relevant caches
    public static function clearAllCaches()
    {
        // We'll use a simple approach to clear caches by calling the service
        try {
            // This is a workaround since we can't directly access the service here
            // In a real application, you might want to use a more elegant solution
            \Illuminate\Support\Facades\Cache::forget('buildings_with_rooms_and_cctvs');
            \Illuminate\Support\Facades\Cache::forget('dashboard_stats');
            \Illuminate\Support\Facades\Cache::forget('production_trends');
            \Illuminate\Support\Facades\Cache::forget('unit_performance');
        } catch (\Exception $e) {
            // Log the error but don't stop the operation
            \Illuminate\Support\Facades\Log::error('Failed to clear cache: ' . $e->getMessage());
        }
    }
}
