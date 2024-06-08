<?php

namespace App\Filament\Resources\MapPointResource\Pages;

use App\Filament\Resources\MapPointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMapPoints extends ListRecords
{
    protected static string $resource = MapPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
