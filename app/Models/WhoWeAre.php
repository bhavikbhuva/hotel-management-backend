<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhoWeAre extends Model
{
    protected $fillable = [
        'title',
        'short_description',
        'content',
        'image',
    ];
}
