<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_legacy_id',
        'teacher_name',
        'designation',
        'department',
        'father_name',
        'mother_name',
        'blood_group',
        'religion',
        'present_address',
        'permanent_address',
        'gender',
        'priority_index',
        'photo',
        'teacher_image',
        'teacher_code',
        'phone',
        'email',
        'joining_date',
        'experience_years',
        'mpo',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'status' => 'boolean',
        'priority_index' => 'integer',
        'experience_years' => 'integer',
    ];

    public function qualifications(): HasMany
    {
        return $this->hasMany(TeacherQualification::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(TeacherTraining::class);
    }
}
