<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_id',
        'name',
        'designation',
        'father_name',
        'mother_name',
        'phone',
        'email',
        'photo',
        'joining_date',
        'leaving_date',
        'is_active',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'leaving_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Committee::class);
    }
}
