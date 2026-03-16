<?php

namespace App\Models;

use App\Enums\BlogStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'blog_category_id',
        'created_by',
        'title',
        'slug',
        'short_description',
        'content',
        'cover_image',
        'meta_title',
        'meta_description',
        'keywords',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BlogStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', BlogStatus::Published);
    }
}
