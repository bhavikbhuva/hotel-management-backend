<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    protected $fillable = [
        'country_id',
        'property_type_id',
        'cancellation_cutoff_time',
        'is_active',
    ];

    public function rules()
    {
        return $this->hasMany(CancellationPolicyRule::class);
    }
}
