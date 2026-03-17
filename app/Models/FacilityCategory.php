<?php

namespace App\Models;

use App\Enums\FacilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => FacilityStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class)->orderBy('sort_order');
    }
}
