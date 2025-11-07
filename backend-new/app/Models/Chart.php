<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property array $data
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Chart extends Model
{
    protected $fillable = [
        'title',
        'data',
        'type',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
