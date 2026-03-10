<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;

class FilamentAuthenticate extends BaseAuthenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        if (Setting::get('setup_completed') !== 'true') {
            return;
        }

        parent::authenticate($request, $guards);
    }
}
