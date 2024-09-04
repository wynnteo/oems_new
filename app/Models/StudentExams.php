<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExams extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_key',
        'exam_id',
        'student_id',
        'started_at',
        'status',
        'completed_at',
        'progress',
        'current_question_id',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    
    public function examResult()
    {
        return $this->hasOne(StudentExamResult::class);
    }
}
