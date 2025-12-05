<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $building_id
 * @property int|null $room_id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string $ip_address
 * @property int $rtsp_port
 * @property int $hls_port
 * @property string $ip_rtsp_url
 * @property string $hls_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Building $building
 * @property-read \App\Models\Room|null $room
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Cctv extends Model
{
    protected $fillable = [
        'building_id',
        'room_id',
        'name',
        'username',
        'password',
        'ip_address',
        'rtsp_port',
        'hls_port',
        'ip_rtsp_url',
        'hls_url',
    ];

    protected $attributes = [
        'rtsp_port' => 554,
        'hls_port' => 8000,
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // Clear cache when CCTV is created, updated, or deleted
    // Also auto-generate URLs
    public static function boot()
    {
        parent::boot();

        static::creating(function ($cctv) {
            // Auto-generate URLs when creating
            self::generateUrls($cctv);
        });

        static::updating(function ($cctv) {
            // Auto-generate URLs when updating
            self::generateUrls($cctv);
        });

        static::retrieved(function ($cctv) {
            // Auto-generate URLs when retrieving
            self::generateUrls($cctv);
        });

        static::saving(function ($cctv) {
            // Auto-generate URLs when saving (covers both creating and updating)
            self::generateUrls($cctv);
        });

        static::saved(function ($cctv) {
            // Clear caches after saving
            \App\Models\Building::clearAllCaches();
            \App\Models\Room::clearAllCaches();
            self::clearAllCaches();
        });

        static::created(function ($cctv) {
            \App\Models\Building::clearAllCaches();
            \App\Models\Room::clearAllCaches();
             self::clearAllCaches();
        });

        static::updated(function ($cctv) {
            \App\Models\Building::clearAllCaches();
            \App\Models\Room::clearAllCaches();
            self::clearAllCaches();
        });

        static::deleted(function ($cctv) {
            \App\Models\Building::clearAllCaches();
            \App\Models\Room::clearAllCaches();
            self::clearAllCaches();
        });
    }

    // Method to clear all relevant caches
    public static function clearAllCaches()
    {
        // Delegate to Building model's clearAllCaches method for now
        \App\Models\Building::clearAllCaches();
    }

    // Helper function to generate RTSP and HLS URLs
    public static function generateUrls($cctv)
    {
        if ($cctv->ip_address) {
            $rtspPort = $cctv->rtsp_port ?? 554;
            $hlsPort = $cctv->hls_port ?? 8000;
            
            // Generate RTSP URL
            $cctv->ip_rtsp_url = "rtsp://admin:password.123@{$cctv->ip_address}:{$rtspPort}/stream";
            
            // Generate HLS URL
            $hlsStreamId = str_replace('.', '_', $cctv->ip_address);
            $cctv->hls_url = "http://127.0.0.1:{$hlsPort}/live/{$hlsStreamId}/index.m3u8";
        }
    }
}
