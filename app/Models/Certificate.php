<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
   use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id', 
        'exam_id',
        'certificate_number',
        'certificate_data',
        'score',
        'issued_at',
        'status',
        'verification_code',
        'file_path',
        'completion_type',
        'distinction',
        'notes'
    ];

    protected $casts = [
        'certificate_data' => 'array',
        'issued_at' => 'datetime',
        'score' => 'decimal:2'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Generate unique certificate number
    public static function generateCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (self::where('certificate_number', $number)->exists());
        
        return $number;
    }

    // Generate unique verification code
    public static function generateVerificationCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('verification_code', $code)->exists());
        
        return $code;
    }

    // Check if certificate is valid
    public function isValid()
    {
        return $this->status === 'generated';
    }
}
