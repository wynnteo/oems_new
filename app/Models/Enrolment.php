<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'enrollment_date',
    ];

    // An enrollment belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // An enrollment belongs to a student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
