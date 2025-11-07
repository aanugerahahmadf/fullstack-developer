<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
