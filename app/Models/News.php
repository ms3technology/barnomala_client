<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'summary', 'content', 'published_at', 'image_json', 'is_active', 'is_featured'];

    protected $casts = [
        'published_at' => 'date',
        'image_json' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function artifacts()
    {
        return $this->hasMany(NewsArtifact::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_json['url'] ?? asset('images/default-news.png');
    }
}
