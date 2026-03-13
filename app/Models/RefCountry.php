<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefCountry extends Model
{
    protected $table = 'ref_countries';

    /** @var bool Read-only reference table — disable auto-incrementing BigInt assumption */
    public $incrementing = true;

    protected $keyType = 'int';

    /** @var string[] Guard all attributes — this is a read-only model */
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return [
            'timezones' => 'array',
            'translations' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'flag' => 'boolean',
            'population' => 'integer',
            'gdp' => 'integer',
            'area_sq_km' => 'float',
        ];
    }

    public function states(): HasMany
    {
        return $this->hasMany(RefState::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(RefCity::class, 'country_id');
    }
}
