<?php

namespace App\Filament\Widgets;

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
            Stat::make('Количество полетов', MapPoint::query()->where(MapPoint::FIELD_USER_ID, auth()->id())->count()),
            Stat::make('Количество запланированных полетов', MapPoint::query()->where(MapPoint::FIELD_USER_ID, auth()->id())->count()),
        ];
    }
}
