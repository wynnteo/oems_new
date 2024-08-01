<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExams extends Model
{
    use HasFactory;

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    
    public function examResult()
    {
        return $this->hasOne(StudentExamResult::class);
    }
}
