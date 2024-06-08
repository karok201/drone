<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MapPointResource\Pages;
use App\Filament\Resources\MapPointResource\RelationManagers;
use App\Models\DroneType;
use App\Models\MapPoint;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Closure;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Modules\Employee\Entities\Employee;
use Traineratwot\FilamentOpenStreetMap\Forms\Components\MapInput;

class MapPointResource extends Resource
{
    protected static ?string $model = MapPoint::class;

    protected static ?int $navigationSort = 2;


    protected static ?string $modelLabel = 'Дрон';
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'Мои дроны';
    protected static ?string $pluralModelLabel = 'Мои дроны';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Название')
                        ->required(),
                    Select::make(MapPoint::FIELD_DRONE_TYPE_ID)
                        ->label('Модель')
                        ->relationship(name: 'droneType', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Hidden::make('path')
                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                            $geoFence = Geometry::fromJson($state);

                            if ($geoFence instanceof LineString) {
                                $set('path', json_encode($geoFence->toArray()['coordinates']));
                            } elseif ($geoFence instanceof Polygon) {
                                //$polygon = $geoFence;
                            } else {
                                throw new Exception('The geo fence is invalid.');
                            }
                        }),

                    Map::make('location')
                        ->autocomplete(
                            fieldName: 'address_line_1'
                        )
                        ->reverseGeocode([
                            'address_line_1' => '%S %n',
                            'city' => '%L',
                            'state' => '%A1',
                            'zip_code' => '%z',
                            'country' => '%c',
                        ])
                        ->mapControls([
                            'mapTypeControl'    => true,
                            'scaleControl'      => true,
                            'streetViewControl' => false,
                            'rotateControl'     => true,
                            'fullscreenControl' => true,
                            'searchBoxControl'  => false, // creates geocomplete field inside map
                            'zoomControl'       => true,
                        ])
                        ->height(fn () => '400px') // map height (width is controlled by Filament options)
                        ->defaultZoom(14) // default zoom level when opening form
                        ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                        ->defaultLocation([47.2126, 38.9160]) // default for new forms
                        ->draggable() // allow dragging to move marker
                        ->clickable(false) // allow clicking to move marker
                        ->geolocate() // adds a button to request device location and set map marker accordingly
                        ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                        ->geolocateOnLoad(true, false) // geolocate on load, second arg 'always' (default false, only for new form))
                        ->hintIconTooltip(url('images/drone.svg'))
                        ->hintIcon(url('images/drone.svg'))
                        ->columnSpanFull()
                        ->drawingControl()
                        ->drawingModes([
                            'polyline' => true,
                        ])
                        ->drawingField('path')
                        ->label('Полетное задание')
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(MapPoint::FIELD_NAME)
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('droneType.name')
                    ->label('Модель'),
                MapColumn::make('location')
                    ->extraImgAttributes(
                        fn ($record): array => ['title' => $record->latitude . ',' . $record->longitude]
                    )
                    ->height('100')
                    ->label('Местоположение')
                    ->columnSpanFull(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMapPoints::route('/'),
            'create' => Pages\CreateMapPoint::route('/create'),
            'edit' => Pages\EditMapPoint::route('/{record}/edit'),
        ];
    }
}
