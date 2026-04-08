<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGallery extends Model
{
    protected $fillable = [
        'title',
        'category',
        'date',
        'image_path',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
