<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ExamRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'registered_at',
        'status',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'exam_data'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'exam_data' => 'array'
    ];

    /**
     * Get the exam that this registration belongs to
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student who registered for this exam
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the exam result for this registration
     */
    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class, 'id', 'exam_registration_id');
    }

    /**
     * Scope to get only registered exams
     */
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }

    /**
     * Scope to get only completed exams
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get upcoming exams
     */
    public function scopeUpcoming($query)
    {
        return $query->whereHas('exam', function($examQuery) {
            $examQuery->where('start_time', '>', now());
        });
    }

    /**
     * Scope to get past exams
     */
    public function scopePast($query)
    {
        return $query->whereHas('exam', function($examQuery) {
            $examQuery->where('end_time', '<', now());
        });
    }

    /**
     * Check if the registration can be cancelled
     */
    public function canBeCancelled(): bool
    {
        if ($this->status !== 'registered') {
            return false;
        }

        // Check if cancellation deadline has passed (24 hours before exam)
        $examStartTime = Carbon::parse($this->exam->start_time);
        $cancellationDeadline = $examStartTime->subHours(24);

        return now()->isBefore($cancellationDeadline);
    }

    /**
     * Check if the student can start the exam
     */
    public function canStartExam(): bool
    {
        if ($this->status !== 'registered') {
            return false;
        }

        $examStartTime = Carbon::parse($this->exam->start_time);
        $examEndTime = Carbon::parse($this->exam->end_time);
        $now = now();

        // Allow starting 15 minutes before exam time and up to 15 minutes after
        $allowedStartTime = $examStartTime->copy()->subMinutes(15);
        $lateEntryDeadline = $examStartTime->copy()->addMinutes(15);

        return $now->isBetween($allowedStartTime, $lateEntryDeadline) && $now->isBefore($examEndTime);
    }

    /**
     * Get time remaining for the exam
     */
    public function getTimeRemainingAttribute(): int
    {
        if (!$this->exam) {
            return 0;
        }

        $examEndTime = Carbon::parse($this->exam->end_time);
        $now = now();

        if ($now->isAfter($examEndTime)) {
            return 0;
        }

        return $examEndTime->diffInMinutes($now);
    }

    /**
     * Mark registration as started
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'started',
            'started_at' => now()
        ]);
    }

    /**
     * Mark registration as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Cancel the registration
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);
    }

    /**
     * Mark as no show
     */
    public function markAsNoShow(): void
    {
        $this->update([
            'status' => 'no_show'
        ]);
    }

    /**
     * Get formatted registration date
     */
    public function getFormattedRegisteredAtAttribute(): string
    {
        return $this->registered_at ? $this->registered_at->format('M d, Y g:i A') : '';
    }

    /**
     * Get formatted started date
     */
    public function getFormattedStartedAtAttribute(): string
    {
        return $this->started_at ? $this->started_at->format('M d, Y g:i A') : '';
    }

    /**
     * Get formatted completed date
     */
    public function getFormattedCompletedAtAttribute(): string
    {
        return $this->completed_at ? $this->completed_at->format('M d, Y g:i A') : '';
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Set registered_at timestamp when creating
        static::creating(function ($model) {
            if (!$model->registered_at) {
                $model->registered_at = now();
            }
        });
    }
}