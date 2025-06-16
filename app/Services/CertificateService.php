<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Student;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function generateCertificate($studentId, $examId, $score)
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
                'passing_score' => $exam->passing_score ?? 70
            ]
        ]);

        // Generate PDF
        $pdfPath = $this->generatePDF($certificate);
        
        // Update certificate with file path
        $certificate->update(['file_path' => $pdfPath]);
        
        return $certificate;
    }

    private function generatePDF($certificate)
    {
        $pdf = Pdf::loadView('certificates.template', compact('certificate'));
        
        $filename = 'certificates/' . $certificate->certificate_number . '.pdf';
        
        // Save to storage
        Storage::put($filename, $pdf->output());
        
        return $filename;
    }

    public function revokeCertificate($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);
        $certificate->update(['status' => 'revoked']);
        
        // Optionally delete the PDF file
        if ($certificate->file_path) {
            Storage::delete($certificate->file_path);
        }

        return $certificate;
    }

    public function verifyCertificate($verificationCode)
    {
        return Certificate::where('verification_code', $verificationCode)->first();
    }

}