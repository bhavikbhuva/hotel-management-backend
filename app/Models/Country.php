<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'currency_symbol',
        'currency_code',
        'currency_name',
        'is_active',
    ];

    public function operatingCountry(): HasOne
    {
        return $this->hasOne(OperatingCountry::class);
    }
}
