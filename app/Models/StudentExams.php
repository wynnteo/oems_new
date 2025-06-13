<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'ip_address',
    ];

    protected $casts = [
        'started_at' => 'datetime', 
        'started_at_utc' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    
    public function examResult()
    {
        return $this->hasOne(StudentExamResults::class, 'student_exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
