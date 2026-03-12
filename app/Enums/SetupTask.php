<?php

namespace App\Enums;

enum SetupTask: string
{
    case AdminProfile = 'admin_profile';
    case Cities = 'cities';
    case Taxes = 'taxes';
    case CancellationPolicy = 'cancellation_policy';
    case LegalPolicy = 'legal_policy';

    public function label(): string
    {
        return match ($this) {
            self::AdminProfile => 'Admin Profile',
            self::Cities => 'Add Cities',
            self::Taxes => 'Tax Configuration',
            self::CancellationPolicy => 'Cancellation Policy',
            self::LegalPolicy => 'Legal Policy',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AdminProfile => 'Set up your admin profile details.',
            self::Cities => 'Configure the cities your platform will operate in.',
            self::Taxes => 'Set up applicable taxes and fees.',
            self::CancellationPolicy => 'Define standard cancellation rules.',
            self::LegalPolicy => 'Add terms & condition and privacy policy.',
        };
    }

    public function buttonLabel(): string
    {
        return match ($this) {
            self::AdminProfile => 'Complete Profile',
            self::Cities => 'Add Cities',
            self::Taxes => 'Manage Taxes',
            self::CancellationPolicy => 'Set Policy',
            self::LegalPolicy => 'Add Policies',
        };
    }

    public function route(): string
    {
        return match ($this) {
            self::AdminProfile => '/admin-profile',
            self::Cities => '/cities',
            self::Taxes => '/taxes',
            self::CancellationPolicy => '/cancellation-policy',
            self::LegalPolicy => '/legal-policy',
        };
    }

    public function isGlobal(): bool
    {
        return $this === self::AdminProfile;
    }

    /**
     * @return array<self>
     */
    public static function countryScoped(): array
    {
        return [
            self::Cities,
            self::Taxes,
            self::CancellationPolicy,
            self::LegalPolicy,
        ];
    }
}
