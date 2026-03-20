<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfile extends Page
{
    protected static string $routePath = '/admin-profile';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $title = 'admin.my_profile';

    public function getTitle(): string|Htmlable
    {
        return __('admin.my_profile');
    }

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.admin-profile';

    public ?string $current_password = null;

    public ?string $new_password = null;

    public ?string $new_password_confirmation = null;

    public function getSubheading(): ?string
    {
        return __('admin.subheading_profile');
    }

    public function getHeading(): string|Htmlable
    {
        return __('admin.my_profile');
    }

    public function editProfileAction(): Action
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return Action::make('editProfile')
            ->label(__('admin.edit_details'))
            ->modalHeading(__('admin.edit_profile'))
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->modalWidth('md')
            ->modalSubmitActionLabel(__('admin.save_details'))
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalCancelAction(fn (Action $action) => $action->extraAttributes(['class' => 'order-first']))
            ->fillForm([
                'avatar' => $user->avatar,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ])
            ->schema([
                FileUpload::make('avatar')
                    ->label(__('admin.profile_photo'))
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/jpeg'])
                    ->helperText('Maximum Size: 5MB. Supported Files: PNG/SVG/JPG'),
                TextInput::make('name')
                    ->label(__('admin.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('admin.phone'))
                    ->required()
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('E.g., +1 (555) 123-4567'),
                TextInput::make('email')
                    ->label(__('admin.email'))
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique('users', 'email', ignorable: $user),
            ])
            ->action(function (array $data): void {
                /** @var \App\Models\User $user */
                $user = auth()->user();

                $user->update([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'avatar' => $data['avatar'] ?? $user->avatar,
                ]);

                Notification::make()
                    ->title(__('admin.profile_updated'))
                    ->success()
                    ->send();
            });
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
            'new_password_confirmation' => ['required'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        Notification::make()
            ->title(__('admin.password_updated'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return [
            'user' => $user,
        ];
    }
}
