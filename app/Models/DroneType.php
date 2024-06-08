<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DroneType extends Model
{
    use HasFactory;

    public const TABLE_NAME = 'drone_types';

    public const FIELD_ID = 'id';
    public const FIELD_NAME = 'name';
    public const FIELD_IMAGE = 'image';
    public const FIELD_MAX_SPEED = 'max_speed';

    protected $guarded = [];
}
