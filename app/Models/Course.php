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
        'created_at'
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // A course can have many enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrolment::class);
    }
}
