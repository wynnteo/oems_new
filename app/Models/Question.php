<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_type',
        'question_text',
        'description',
        'image_name',
        'options',
        'correct_answer',
        'exam_id',
        'is_active',
        'explanation',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
