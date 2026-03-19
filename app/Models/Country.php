<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    protected $fillable = [
        'ref_country_id',
        'name',
        'iso_code',
        'currency_symbol',
        'currency_code',
        'currency_name',
        'is_active',
    ];

    public function refCountry(): BelongsTo
    {
        return $this->belongsTo(RefCountry::class, 'ref_country_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
