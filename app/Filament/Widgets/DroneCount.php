<?php

namespace App\Filament\Widgets;

use App\Models\Flight;
use App\Models\MapPoint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DroneCount extends BaseWidget
{
//    protected int | string | array $columnSpan = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Количество дронов', MapPoint::query()->where(MapPoint::FIELD_USER_ID, auth()->id())->count()),
            Stat::make('Количество совершенных полетов', Flight::query()->whereIn(Flight::FIELD_MAP_POINT_ID, MapPoint::query()->where('user_id', auth()->id())->pluck('id'))->where('is_finished', true)->count()),
            Stat::make('Количество предстоящих полетов', Flight::query()->whereIn(Flight::FIELD_MAP_POINT_ID, MapPoint::query()->where('user_id', auth()->id())->pluck('id'))->where('is_finished', false)->count()),
        ];
    }
}
