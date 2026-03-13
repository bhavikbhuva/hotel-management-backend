<?php

namespace App\Models;

use App\Enums\TaxStatus;
use App\Enums\TaxType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id',
        'property_type_id',
        'name',
        'description',
        'type',
        'value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => TaxType::class,
            'status' => TaxStatus::class,
            'value' => 'decimal:4',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function scopeForCountry(Builder $query, int $countryId): Builder
    {
        return $query->where('country_id', $countryId);
    }
}
