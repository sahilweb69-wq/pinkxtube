<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Performer;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'source_id',
        'title',
        'source_url',
        'views',
        'like_count',
        'dislike_count',
        'rating',
        'duration',
        'embed_url',
        'thumb_url',
        'thumb_width',
        'thumb_height',
        'thumbs',
        'keywords',
        'is_vr',
        'is_3d',
        'uploader',
        'tags',
        'categories',
        'performers',
        'uploaded_at',
        'raw',
    ];

    protected $casts = [
        'tags' => 'array',
        'categories' => 'array',
        'performers' => 'array',
        'thumbs' => 'array',
        'uploaded_at' => 'datetime',
        'raw' => 'array',
    ];

    // Relationships for normalized data
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function performers()
    {
        return $this->belongsToMany(Performer::class);
    }
}
