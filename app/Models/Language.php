<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'is_rtl',
        'status',
        'is_default',
    ];

    protected $casts = [
        'is_rtl'     => 'boolean',
        'status'     => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($language) {
            if ($language->is_default) {
                // Ensure only one default language exists
                static::where('id', '!=', $language->id)->update(['is_default' => false]);
            }
        });

        static::deleting(function ($language) {
            if ($language->is_default) {
                throw new \Exception("Cannot delete the default language.");
            }
            if (static::count() <= 1) {
                throw new \Exception("Cannot delete the last remaining language.");
            }
        });
    }
}
