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

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public static function rules()
    {
        return [
            'latitude' => 'nullable|numeric|between:-90,90|decimal:0,8',
            'longitude' => 'nullable|numeric|between:-180,180|decimal:0,8',
        ];
    }

    // Convert decimal degrees to DMS format for display
    public function getLatitudeDmsAttribute()
    {
        if (!$this->latitude) return null;
        
        $degrees = floor(abs($this->latitude));
        $minutes = fmod(abs($this->latitude) * 60, 60);
        $seconds = fmod($minutes * 60, 60);
        $minutes = floor($minutes);
        $hemisphere = $this->latitude >= 0 ? 'N' : 'S';
        
        return sprintf('%d° %d′ %.2f″ %s', $degrees, $minutes, $seconds, $hemisphere);
    }

    // Convert decimal degrees to DMS format for display
    public function getLongitudeDmsAttribute()
    {
        if (!$this->longitude) return null;
        
        $degrees = floor(abs($this->longitude));
        $minutes = fmod(abs($this->longitude) * 60, 60);
        $seconds = fmod($minutes * 60, 60);
        $minutes = floor($minutes);
        $hemisphere = $this->longitude >= 0 ? 'E' : 'W';
        
        return sprintf('%d° %d′ %.2f″ %s', $degrees, $minutes, $seconds, $hemisphere);
    }

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

        static::creating(function ($building) {
            static::clearAllCaches();
        });

        static::updating(function ($building) {
            static::clearAllCaches();
        });

        static::retrieved(function ($building) {
            static::clearAllCaches();
        });

        static::saving(function ($building) {
            static::clearAllCaches();
            
            // Validate latitude and longitude ranges with higher precision
            if ($building->latitude !== null) {
                $building->latitude = max(-90, min(90, round((float)$building->latitude, 8)));
            }
            
            if ($building->longitude !== null) {
                $building->longitude = max(-180, min(180, round((float)$building->longitude, 8)));
            }
        });

        static::saved(function ($building) {
            static::clearAllCaches();
        });

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
