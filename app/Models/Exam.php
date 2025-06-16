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
        'ip_restrictions',
        'price',
        'end_time',
    ];

    

    public function studentExams()
    {
        return $this->hasMany(StudentExams::class);
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'randomize_questions' => 'boolean',
        'review_questions' => 'boolean',
        'show_answers' => 'boolean',
        'pagination' => 'boolean',
        'retake_allowed' => 'boolean',
        'allow_rating' => 'boolean',
        'ip_restrictions' => 'boolean',
        'passing_grade' => 'decimal:2',
        'price' => 'decimal:2',
        'duration' => 'integer',
        'number_of_questions' => 'integer',
        'number_retake' => 'integer',
    ];

    // An exam belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
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

    public function getAverageRatingAttribute()
    {
        return $this->ratings->count() > 0 ? round($this->ratings->avg('rating'), 1) : 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings->count();
    }

    public function getSubmissionsCountAttribute()
    {
        return $this->studentExams()
                    ->where(function($query) {
                        $query->whereNotNull('completed_at')
                            ->orWhere('status', 'COMPLETED');
                    })
                    ->count();
    }

    public function getTotalAttemptsAttribute()
    {
        return $this->studentExams->count();
    }
}
