<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Student;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateCertificate($studentId, $examId, $score, $completionType = null, $distinction = null, $notes = null)
    {
        $student = Student::findOrFail($studentId);
        $exam = Exam::with('course')->findOrFail($examId);
        
        // Create certificate record
        $certificate = Certificate::create([
            'student_id' => $studentId,
            'course_id' => $exam->course_id,
            'exam_id' => $examId,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'verification_code' => Certificate::generateVerificationCode(),
            'score' => $score,
            'issued_at' => now(),
            'certificate_data' => [
                'student_name' => $student->name,
                'course_title' => $exam->course->title,
                'exam_title' => $exam->title,
                'score' => $score,
                'issued_date' => now()->format('F j, Y'),
                'passing_score' => $exam->passing_score ?? 70,
                'completion_type' => $completionType ?? 'exam_passed',
                'distinction' => $this->calculateDistinction($score),
                'notes' => $notes ?? null,
            ]
        ]);

        // Generate PDF
        $pdfPath = $this->generatePDF($certificate);
        
        // Update certificate with file path
        $certificate->update(['file_path' => $pdfPath]);
        
        return $certificate;
    }

    public function generatePDF($certificate)
    {
        $certificate->load(['student', 'course', 'exam']);

        // Create certificates directory if it doesn't exist
        $certificatesDir = 'certificates/' . date('Y/m');
        if (!Storage::exists('public/' . $certificatesDir)) {
            Storage::makeDirectory('public/' . $certificatesDir);
        }

        $fileName = $certificate->certificate_number . '.pdf';
        $filePath = $certificatesDir . '/' . $fileName;

        // Generate PDF using the template
        $pdf = Pdf::loadView('certificates.template', compact('certificate'));
        
        // Save PDF to storage
        Storage::put('public/' . $filePath, $pdf->output());

        // Update certificate with file path
        $certificate->update(['file_path' => $filePath]);

        return $filePath;
    }

    public function revokeCertificate($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);
        $certificate->update(['status' => 'revoked']);
        
        // Optionally delete the PDF file
        if ($certificate->file_path && Storage::exists('public/' . $certificate->file_path)) {
            Storage::delete('public/' . $certificate->file_path);
            $certificate->update(['file_path' => null]);
        }

        return $certificate;
    }

    public function verifyCertificate($verificationCode)
    {
        return Certificate::where('verification_code', $verificationCode)->first();
    }

    private function calculateDistinction($score)
    {
        if ($score >= 85) return 'high_distinction';
        if ($score >= 75) return 'distinction';
        if ($score >= 65) return 'merit';
        if ($score >= 50) return 'pass';
        return null;
    }
}