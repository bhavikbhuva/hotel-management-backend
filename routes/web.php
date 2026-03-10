<?php

use App\Filament\Pages\SetupWizard;
use Illuminate\Support\Facades\Route;

Route::get('/setup', SetupWizard::class)
    ->name('setup-wizard');
