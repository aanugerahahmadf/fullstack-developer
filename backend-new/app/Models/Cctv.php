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
 * @property string $ip_rtsp_url
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
        'ip_rtsp_url',
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
    public static function boot()
    {
        parent::boot();

        static::created(function ($cctv) {
            \App\Models\Building::clearAllCaches();
        });

        static::updated(function ($cctv) {
            \App\Models\Building::clearAllCaches();
        });

        static::deleted(function ($cctv) {
            \App\Models\Building::clearAllCaches();
        });
    }
}
