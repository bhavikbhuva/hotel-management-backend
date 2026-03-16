<?php

use App\Filament\Pages\SetupWizard;
use App\Http\Controllers\ExportDownloadController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/setup', SetupWizard::class)
    ->name('setup-wizard');

Route::get('/export/download', ExportDownloadController::class)
    ->middleware('auth')
    ->name('export.download');

Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return redirect()->back()->with('success', 'Application cache cleared!');
})->name('clear-cache');

Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);

    return redirect()->back()->with('success', 'Database migrated successfully!');
})->name('migrate');
