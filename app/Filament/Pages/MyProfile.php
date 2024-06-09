<?php

namespace App\Filament\Pages;

use App\Models\DroneType;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class MyProfile extends Page
{
    protected static string $view = "filament.pages.my-profile";

    public $user;
    public $userData;
    // Password
    public $new_password;
    public $new_password_confirmation;
    // Sanctum tokens
    public $token_name;
    public $abilities = [];
    public $plain_text_token;
    protected $loginColumn;

    public function boot()
    {
        // user column
        $this->loginColumn = config('filament-breezy.fallback_login_field');
    }

    public function mount()
    {
        $this->user = Filament::auth()->user();
//        $this->updateProfileForm->fill($this->user->toArray());
    }

    public function form(Form $form): Form
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
                        ->label('Максимальная скорость (м/с)')
                        ->required()
                        ->numeric()
                ])
                    ->columns()
            ]);
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            "updateProfileForm" => $this->makeForm()
                ->model(config('filament-breezy.user_model'))
                ->schema($this->getUpdateProfileFormSchema())
                ->statePath('userData'),

            "updatePasswordForm" => $this->makeForm()->schema(
                $this->getUpdatePasswordFormSchema()
            ),
            "createApiTokenForm" => $this->makeForm()->schema(
                $this->getCreateApiTokenFormSchema()
            ),
        ]);
    }

    protected function getUpdateProfileFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->label(__('filament-breezy::default.fields.name')),
            Forms\Components\TextInput::make('email')
                ->required()
                ->email(fn () => $this->loginColumn === 'email')
                ->unique(config('filament-breezy.user_model'), ignorable: $this->user)
                ->label(__('filament-breezy::default.fields.email')),
        ];
    }

    public function updateProfile()
    {
        $this->user->update($this->updateProfileForm->getState());
        $this->notify("success", __('filament-breezy::default.profile.personal_info.notify'));
    }

    protected function getUpdatePasswordFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make("new_password")
                ->label(__('filament-breezy::default.fields.new_password'))
                ->password()
                ->required(),
            Forms\Components\TextInput::make("new_password_confirmation")
                ->label(__('filament-breezy::default.fields.new_password_confirmation'))
                ->password()
                ->same("new_password")
                ->required(),
        ];
    }

    public function updatePassword()
    {
        $state = $this->updatePasswordForm->getState();
        $this->user->update([
            "password" => Hash::make($state["new_password"]),
        ]);
        session()->forget("password_hash_web");
        Filament::auth()->login($this->user);
        $this->notify("success", __('filament-breezy::default.profile.password.notify'));
        $this->reset(["new_password", "new_password_confirmation"]);
    }

    protected function getCreateApiTokenFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('token_name')->label(__('filament-breezy::default.fields.token_name'))->required(),
            Forms\Components\CheckboxList::make('abilities')
                ->label(__('filament-breezy::default.fields.abilities'))
                ->options(config('filament-breezy.sanctum_permissions'))
                ->columns(2)
                ->required(),
        ];
    }

    public function createApiToken()
    {
        $state = $this->createApiTokenForm->getState();
        $indexes = $state['abilities'];
        $abilities = config("filament-breezy.sanctum_permissions");
        $selected = collect($abilities)->filter(function ($item, $key) use (
            $indexes
        ) {
            return in_array($key, $indexes);
        })->toArray();
        $this->plain_text_token = Filament::auth()->user()->createToken($state['token_name'], array_values($selected))->plainTextToken;
        $this->notify("success", __('filament-breezy::default.profile.sanctum.create.notify'));
        $this->emit('tokenCreated');
        $this->reset(['token_name']);
    }

    public function getBreadcrumbs(): array
    {
        return [
            url()->current() => __('filament-breezy::default.profile.profile'),
        ];
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-breezy.profile_page_icon', 'heroicon-o-document-text');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-breezy::default.profile.account');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-breezy::default.profile.profile');
    }

    public function getTitle(): string
    {
        return __('filament-breezy::default.profile.my_profile');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
