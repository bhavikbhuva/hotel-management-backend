<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TaxManage extends Page
{
    protected static ?string $slug = 'taxes';

    protected static ?string $title = 'Tax Manage';

    protected static ?string $navigationLabel = 'Tax Manage';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.tax-manage';

    public static function getNavigationIcon(): string|\BackedEnum|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Location & Policies';
    }
}
