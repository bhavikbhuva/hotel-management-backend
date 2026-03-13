<?php

namespace App\Models;

use App\Enums\CityStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'name',
        'state',
        'latitude',
        'longitude',
        'google_place_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CityStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CityStatus::Active);
    }
}
