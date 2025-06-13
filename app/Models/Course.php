<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_code',
        'description',
        'price',
        'category',
        'difficulty_level',
        'instructor',
        'thumbnail',
        'video_url',
        'slug',
        'is_active',
        'is_featured',
        'duration',
        'language',
        'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'integer',
        'duration' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Enrolment::class, 'course_id', 'id', 'id', 'student_id');
    }
}
