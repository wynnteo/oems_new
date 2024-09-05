<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'exam_code',
        'title',
        'description',
        'start_time',
        'duration',
        'duration_unit',
        'randomize_questions',
        'review_questions',
        'number_of_questions',
        'show_answers',
        'pagination',
        'retake_allowed',
        'allow_rating',
        'number_retake',
        'passing_grade',
        'access_code',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    // An exam belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function formatDuration()
    {
        if ($this->duration_unit === 'minutes') {
            $hours = intdiv($this->duration, 60);
            $minutes = $this->duration % 60;

            $formatted = '';
            if ($hours > 0) {
                $formatted .= $hours . ' hr';
                if ($minutes > 0) {
                    $formatted .= ' ' . $minutes . ' mins';
                }
            } else {
                $formatted .= $minutes . ' mins';
            }

            return $formatted;
        } elseif ($this->duration_unit === 'hours') {
            $formatted = $this->duration . ' hr';
            return $formatted;
        }
    }
}
