<?php

namespace App\Filament\Widgets;

use App\Models\MapPoint;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DroneMap extends MapTableWidget
{
	protected static ?int $sort = 1;

//    protected static array $layers = [
//        'https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml'
//    ];

    protected static ?string $heading = 'Мои дроны на карте';

    protected ?string $placeholderHeight = '1000px';
    protected static ?string $maxHeight = '1000px';
    protected static ?string $minHeight = '750px';

    protected int | string | array $columnSpan = 2;

	protected static ?string $pollingInterval = null;

	protected static ?bool $clustering = true;
    protected static ?int $zoom = 1;

	protected static ?string $mapId = 'incidents';

	protected function getTableQuery(): Builder
	{
		return MapPoint::query()->where(MapPoint::FIELD_USER_ID, auth()->id());
	}

	protected function getTableColumns(): array
	{
		return [
			Tables\Columns\TextColumn::make(MapPoint::FIELD_NAME)
                ->label('Название')
                ->searchable()
                ->sortable(),
			Tables\Columns\TextColumn::make('droneType.name')
                ->label('Модель')
                ->searchable()
                ->sortable(),
			MapColumn::make('location')
				->extraImgAttributes(
					fn ($record): array => ['title' => $record->latitude . ',' . $record->longitude]
				)
//				->height('150')
//				->width('250')
                ->label('Местоположение')
                ->columnSpanFull(),
		];
	}

	protected function getTableFilters(): array
	{
		return [
			RadiusFilter::make('location')
				->section('Radius Filter')
				->selectUnit(),
            MapIsFilter::make('map'),
		];
	}

	protected function getTableActions(): array
	{
		return [
		];
	}

	protected function getData(): array
	{
		$locations = $this->getRecords();

		$data = [];

		foreach ($locations as $location)
		{
            $path = [];

            foreach (json_decode($location->path, true) as $item) {
                $path[] = [
                    'lat' => $item[0],
                    'lng' => $item[1],
                ];
            }

			$data[] = [
				'location' => [
					'lat' => $location->latitude ? round(floatval($location->latitude), static::$precision) : 0,
                    'lng' => $location->longitude ? round(floatval($location->longitude), static::$precision) : 0,
				],
                'id' => $location->id,
                'icon' => [
                    'url' => url('images/drone.svg'),
                    'type' => 'svg',
                    'scale' => [45,45],
                ],
                'path' => $path
			];
		}

		return $data;
	}
}
