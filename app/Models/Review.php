<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'property_id',
        'rating',
        'review',
        'status',
        'is_visible',
        'is_featured',
        'featured_order',
        'is_edited',
        'edited_at',
        'removal_requested',
        'removal_status',
        'approved_by',
        'approved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
