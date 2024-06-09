<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Component;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make('Личные данные')->schema([
                            $this->getNameFormComponent(),
                            $this->getSurnameFormComponent(),
                            $this->getThirdNameFormComponent(),
                        ]),
                        Section::make('Паспортные данные')->schema([
                            $this->getPassportSeriesComponent(),
                            $this->getPassportNumberComponent(),
                            $this->getPassportDateComponent()->columns(10),
                            Checkbox::make('is_agreed')
                                ->label('Согласен (-на) на обработку персональных данных')
                                ->required()
                        ]),
                        Section::make('Данные аккаунта')->schema([
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ])
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('role')
            ->options([
                'buyer' => 'Buyer',
                'seller' => 'Seller',
            ])
            ->default('buyer')
            ->required();
    }

    protected function getPassportSeriesComponent(): Component
    {
        return TextInput::make('passport_series')
            ->numeric()
            ->label('Серия паспорта')
            ->required();
    }

    protected function getPassportNumberComponent(): Component
    {
        return TextInput::make('passport_number')
            ->numeric()
            ->label('Номер паспорта')
            ->required();
    }

    protected function getPassportDateComponent(): Component
    {
        return DatePicker::make('passport_date')
            ->label('Дата выдачи паспорта')
            ->native(false)
            ->required();
    }

    protected function getSurnameFormComponent(): Component
    {
        return TextInput::make('surname')
            ->label('Фамилия')
            ->required();
    }

    protected function getThirdNameFormComponent(): Component
    {
        return TextInput::make('third_name')
            ->label('Отчество')
            ->required();
    }
}
