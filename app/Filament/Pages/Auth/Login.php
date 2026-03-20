<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $response = parent::authenticate();

        if ($response) {
            /** @var \App\Models\User|null $user */
            $user = Filament::auth()->user();

            $user?->update(['last_login_at' => now()]);
        }

        return $response;
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.bookinghub_admin');
    }

    public function getSubheading(): string|Htmlable|null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return __('admin.sign_in_to_access_the_admin_panel');
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->prefixIcon('heroicon-m-envelope')
            ->placeholder(__('admin.eg_jackwilliams11gmailcom'));
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->prefixIcon('heroicon-m-lock-closed');
    }
}
