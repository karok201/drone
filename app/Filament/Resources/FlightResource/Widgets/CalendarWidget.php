<?php

namespace App\Filament\Resources\FlightResource\Widgets;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Flight::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Flight::query()->get()->toArray();
    }
}
