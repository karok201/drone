<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flight extends Model
{
    public const TABLE_NAME = 'flights';

    public const FIELD_ID = 'id';
    public const FIELD_TIME_START = 'time_start';
    public const FIELD_IS_REPEAT = 'is_repeat';
    public const FIELD_MAP_POINT_ID = 'map_point_id';
    public const FIELD_PATH = 'path';
    public const FIELD_IS_FINISHED = 'is_finished';

    protected $guarded = [];

    use HasFactory;

    public function mapPoint(): BelongsTo
    {
        return $this->belongsTo(MapPoint::class)->where('user_id', auth()->id());
    }
}
