<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurPromise extends Model
{
    protected $fillable = [
        'title',
        'content',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
