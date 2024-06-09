<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\FileUpload;
use Livewire\Component;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;
use Filament\Facades\Filament;


class Register extends Component implements HasForms
{
    use InteractsWithForms;

    public User $user;

    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation  = '';
    public $company_name = '';
    public $website = '';
    public $address = '';
    public $logo = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(50)
                            ->unique(User::class),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->maxLength(50)
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        TextInput::make('passwordConfirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required()
                            ->maxLength(50)
                            ->minLength(8)
                            ->dehydrated(false),
                    ])
                    ->columns([
                        'sm' => 2,
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
                Wizard\Step::make('Company Information')
                    ->schema([
                        TextInput::make('company_name')->label('Company Name')->required()->maxLength(50)->autofocus(),
                        TextInput::make('website')->label('Company Website')->prefix('https://')->maxLength(50),
                        TextInput::make('address')->label('Company Address')->maxLength(100),
                        FileUpload::make('logo')->label('Company Logo')->image()->directory('logos'),
                    ])
            ])
                ->columns([
                    'sm' => 1,
                ])
                ->columnSpan([
                    'sm' => 1,
                ])
                ->submitAction(new HtmlString(html: '<button type="submit">Register</button>'))

        ];
    }

    public function register()
    {
        $user = User::create($this->form->getState());
        Filament::auth()->login(user: $user, remember:true);
        return redirect()->intended(Filament::getUrl('filament.pages.dashboard'));
    }

    public function render(): View
    {
        return view('livewire.register');
    }
}
