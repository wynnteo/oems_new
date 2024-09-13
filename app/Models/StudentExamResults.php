<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExamResults extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_exam_id',  
        'attempt_number',
        'total_correct',
        'total_incorrect',
        'score',
        'review',
    ];

    protected $casts = [
        'review' => 'array', 
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // An enrollment belongs to a student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentExam()
    {
        return $this->belongsTo(StudentExams::class);
    }
}
