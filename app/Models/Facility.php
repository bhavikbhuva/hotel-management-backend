<?php

namespace App\Models;

use App\Enums\FacilityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'facility_category_id');
    }
}
