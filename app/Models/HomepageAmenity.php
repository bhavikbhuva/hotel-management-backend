<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAmenity extends Model
{
    protected $fillable = [
        'facility_id',
        'description',
        'sort_order',
        'is_active',
    ];
}
