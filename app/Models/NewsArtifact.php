<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArtifact extends Model
{
    protected $fillable = ['news_id', 'file_path', 'file_name', 'file_type', 'file_size'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
