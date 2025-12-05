<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $phone
 * @property string $instagram
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Contact extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'instagram',
        'address',
    ];

    // Clear cache when contact is created, updated, or deleted
    public static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::updating(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::retrieved(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::saving(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::saved(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::created(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::updated(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });

        static::deleted(function ($contact) {
            \App\Models\Building::clearAllCaches();
        });
    }
}
