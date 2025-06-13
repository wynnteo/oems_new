<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'student_code',
        'date_of_birth',
        'phone_number',
        'gender',
        'status',
        'address',
        'created_at',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrolment::class);
    }

    // A student can enroll in many courses
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrolments')
            ->withPivot('id', 'course_id', 'student_id', 'enrollment_date')
            ->withTimestamps();
    }

    public function studentExams()
    {
        return $this->hasMany(StudentExams::class);
    }

    public function examsResults()
    {
        return $this->hasMany(StudentExamResults::class);
    }

    public function studentExamResults()
    {
        return $this->hasManyThrough(StudentExamResults::class, StudentExams::class, 'student_id', 'student_exam_id', 'id', 'id');
    }
}
