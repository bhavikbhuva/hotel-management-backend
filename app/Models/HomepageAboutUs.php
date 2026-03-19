<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAboutUs extends Model
{
    protected $fillable = [
        'title',
        'description',
        'button_text',
        'contact_no',
        'image',
        'is_active',
    ];
}
