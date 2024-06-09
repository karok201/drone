<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Filament\Resources\FlightResource\RelationManagers;
use App\Models\Flight;
use App\Models\MapPoint;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
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
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralLabel = 'Полетные задания';
    protected static ?string $modelLabel = 'Полетное задание';
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\DateTimePicker::make('time_start')
                        ->label('Дата начала полета')
                        ->required(),
                    Select::make(Flight::FIELD_MAP_POINT_ID)
                        ->label('Дрон')
                        ->relationship(name: 'mapPoint', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                ])
                    ->columns(),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Hidden::make('path')
                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                            if (!$state) {
                                return;
                            }

                            $geoFence = Geometry::fromJson($state);

                            if ($geoFence instanceof LineString) {
                                $set('path', json_encode($geoFence->toArray()['coordinates']));
                            } elseif ($geoFence instanceof Polygon) {
                                //$polygon = $geoFence;
                            } else {
                                throw new Exception('The geo fence is invalid.');
                            }
                        }),

                    Map::make('')
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
                        ->geolocate() // adds a button to request device location and set map marker accordingly
                        ->geolocateLabel('Get Location') // overrides the default label for geolocate button
                        ->geolocateOnLoad(true, false) // geolocate on load, second arg 'always' (default false, only for new form))
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
                Tables\Columns\TextColumn::make('mapPoint.name')
                    ->label('Дрон')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_start')
                    ->label('Дата полета'),
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
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
