<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'section_key',
        'title',
        'description',
        'button_text',
        'contact_no',
        'image',
        'is_active',
        'amenities_data',
        'reviews_data',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'amenities_data' => 'array',
            'reviews_data' => 'array',
        ];
    }
}
