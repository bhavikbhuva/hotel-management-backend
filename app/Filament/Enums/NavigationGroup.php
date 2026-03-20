<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasLabel
{
    case PropertyManagement = 'Property Management';
    case ContentManagement = 'Content Management';
    case Marketing = 'Marketing';
    case LocationPolicies = 'Location & Policies';
    case Settings = 'Settings';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PropertyManagement => __('admin.property_management'),
            self::ContentManagement => __('admin.content_management'),
            self::Marketing => __('admin.marketing'),
            self::LocationPolicies => __('admin.location_policies'),
            self::Settings => __('admin.settings'),
        };
    }
}
