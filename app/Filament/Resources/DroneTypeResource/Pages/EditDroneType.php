<?php

namespace App\Filament\Resources\DroneTypeResource\Pages;

use App\Filament\Resources\DroneTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDroneType extends EditRecord
{
    protected static string $resource = DroneTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
