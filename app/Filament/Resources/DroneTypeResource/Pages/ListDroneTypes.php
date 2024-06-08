<?php

namespace App\Filament\Resources\DroneTypeResource\Pages;

use App\Filament\Resources\DroneTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDroneTypes extends ListRecords
{
    protected static string $resource = DroneTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
