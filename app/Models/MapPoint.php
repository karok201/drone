<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapPoint extends Model
{
    public const TABLE_NAME = 'map_points';

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_PATH = 'path';
    public const FIELD_LONGITUDE = 'longitude';
    public const FIELD_LATITUDE = 'latitude';
    public const FIELD_USER_ID = 'user_id';
    public const FIELD_DRONE_TYPE_ID = 'drone_type_id';
    public const FIELD_LAST_LONGITUDE = 'last_longitude';
    public const FIELD_LAST_LATITUDE = 'last_latitude';
    public const FIELD_CURRENT_LONGITUDE = 'current_longitude';
    public const FIELD_CURRENT_LATITUDE  = 'current_latitude';

    public function droneType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DroneType::class);
    }


    protected $guarded = [];

    protected $appends = [
        'location',
    ];

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->latitude,
            "lng" => (float)$this->longitude,
        ];
    }

//    public function getPathAttribute()
//    {
//        return $this->path;
//    }

    public function setPathAttribute($path): void
    {
        $this->attributes['path'] = $path;
//        $this->attributes['longitude'] = $location['lng'];
    }

    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['latitude'] = $location['lat'];
            $this->attributes['longitude'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'latitude',
            'lng' => 'longitude',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'location';
    }

    use HasFactory;
}
