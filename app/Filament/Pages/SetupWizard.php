<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Setting;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SetupWizard extends SimplePage
{
    protected static string $layout = 'filament-panels::components.layout.simple';

    protected static ?string $slug = 'setup';

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];
    // Filament forms store data inside this variable.

    public function mount(): void
    {
        Filament::setCurrentPanel(Filament::getDefaultPanel());
        Filament::bootCurrentPanel();
        // This ensures Filament knows which panel is active.Because this page is outside normal admin routes

        if (Setting::get('setup_completed') === 'true') {
            $this->redirect('/');

            return;
        }

        $this->form->fill();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Setup Wizard';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'BookingHub Setup';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Complete the initial setup to get started.';
    }

    public function getMaxWidth(): Width|string|null
    {
        return Width::TwoExtraLarge;
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('complete'),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Admin Account')
                        ->description('Create the super admin account')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->prefixIcon('heroicon-m-user')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Email')
                                ->prefixIcon('heroicon-m-envelope')
                                ->email()
                                ->required()
                                ->unique(User::class, 'email')
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Phone')
                                ->prefixIcon('heroicon-m-phone')
                                ->tel()
                                ->nullable()
                                ->maxLength(20),
                            TextInput::make('password')
                                ->label('Password')
                                ->prefixIcon('heroicon-m-lock-closed')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(8)
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->label('Confirm Password')
                                ->prefixIcon('heroicon-m-lock-closed')
                                ->password()
                                ->revealable()
                                ->required(),
                        ])
                        ->columns(1),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button
                            type="submit"
                            size="sm"
                        >
                            Complete Setup
                        </x-filament::button>
                    BLADE))),
            ])
            ->statePath('data');
    }

    public function complete(): void
    {
        $data = $this->form->getState();

        /** @var User $user */
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
            'email_verified_at' => now(),
        ]); 

        Setting::set('setup_completed', 'true');

        Filament::auth()->login($user);

        Notification::make()
            ->title('Setup completed successfully!')
            ->success()
            ->send();

        $this->redirect('/');
    }
}
