<?php

// Helper file to assist IDEs with type recognition for Eloquent models

namespace App\Models;

/**
 * User Model
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string $guard = null)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string $guard = null)
 * @method bool hasAllRoles(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string $guard = null)
 * @method bool can(string $permission)
 * @method array roles()
 */
class User extends \Illuminate\Foundation\Auth\User
{
    // This class is for IDE helper purposes only
    use \Illuminate\Notifications\Notifiable;
    use \Spatie\Permission\Traits\HasRoles;
}

/**
 * Building Model
 *
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
class Building extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}

/**
 * Room Model
 *
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
class Room extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}

/**
 * Cctv Model
 *
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
class Cctv extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}

/**
 * Contact Model
 *
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
class Contact extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}

/**
 * Chart Model
 *
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
class Chart extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}

/**
 * Stats Model
 *
 * @property int $id
 * @property string $metric
 * @property string $value
 * @property \Illuminate\Support\Carbon $timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method int getKey()
 * @method string getKeyName()
 */
class Stats extends \Illuminate\Database\Eloquent\Model
{
    // This class is for IDE helper purposes only
}
