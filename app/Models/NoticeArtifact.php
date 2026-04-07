<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeArtifact extends Model
{
    protected $fillable = ['notice_id', 'file_path', 'file_name', 'file_type', 'file_size'];

    /**
     * Get the notice that owns the artifact.
     */
    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }
}
