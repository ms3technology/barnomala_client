<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['title', 'content', 'published_at', 'is_active', 'is_urgent'];

    protected $casts = [
        'published_at' => 'date',
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
    ];

    /**
     * Get the artifacts for the notice.
     */
    public function artifacts()
    {
        return $this->hasMany(NoticeArtifact::class);
    }
}
