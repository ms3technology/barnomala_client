<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'session',
        'description',
        'order_index',
        'status',
        'note',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(\App\Models\CommitteeMember::class);
    }
}
