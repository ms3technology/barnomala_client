<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'galleries';

    public const TYPE_PHOTO = 'photo';
    public const TYPE_VIDEO = 'video';

    protected $fillable = [
        'type',
        'title',
        'category',
        'date',
        'image_path',
        'video_url',
        'video_path',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
