<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DroneTypeResource\Pages;
use App\Filament\Resources\DroneTypeResource\RelationManagers;
use App\Models\DroneType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DroneTypeResource extends Resource
{
    protected static ?string $model = DroneType::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $modelLabel = 'Модель дрона';

    protected static ?string $navigationLabel = 'Модели дронов';
    protected static ?string $pluralModelLabel = 'Модели дронов';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основное')->schema([
                    Forms\Components\TextInput::make(DroneType::FIELD_NAME)
                        ->label('Название')
                        ->required(),
                    Forms\Components\FileUpload::make(DroneType::FIELD_IMAGE)
                        ->required()
                        ->label('Изображение')
                ])
                ->columns(),

                Forms\Components\Section::make('Дополнительно')->schema([
                    Forms\Components\TextInput::make(DroneType::FIELD_MAX_SPEED)
                        ->default(1)
                        ->hidden()
                        ->required()
                        ->numeric()
                ])
                    ->hidden(true)
                ->columns()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(DroneType::FIELD_NAME)
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make(DroneType::FIELD_IMAGE)
                    ->label('Изображение')
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
            'index' => Pages\ListDroneTypes::route('/'),
            'create' => Pages\CreateDroneType::route('/create'),
            'edit' => Pages\EditDroneType::route('/{record}/edit'),
        ];
    }
}
