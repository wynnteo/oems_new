<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use App\Models\Exam;
use App\Models\StudentExams;
use App\Models\StudentExamResults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use \App\Services\CertificateService;
use PDF;

class CertificateController extends Controller
{
    /**
     * Display a listing of the certificates.
     */
    public function index()
    {
        $certificates = Certificate::with(['student', 'course', 'exam'])
            ->orderBy('issued_at', 'desc')
            ->paginate(20);

        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create()
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('is_active', 'active')->orderBy('title')->get();
        $exams = Exam::where('status', 'available')->orderBy('title')->get();

        return view('admin.certificates.create', compact('students', 'courses', 'exams'));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateCertificateData($request);

        // Check for duplicate certificate
        if ($this->certificateExists($validatedData['student_id'], $validatedData['course_id'], $validatedData['exam_id'])) {
            return back()->withErrors(['error' => 'Certificate already exists for this student, course, and exam combination.']);
        }

        // If exam is selected, validate exam completion and passing score
        if (!empty($validatedData['exam_id'])) {
            $examValidation = $this->validateExamCompletion($validatedData['student_id'], $validatedData['exam_id']);
            if (!$examValidation['valid']) {
                return back()->withErrors(['error' => $examValidation['message']]);
            }
            // Use the actual exam score
            $validatedData['score'] = $examValidation['score'];
        }

        $certificate = $this->createCertificate($validatedData);

        // Generate PDF if status is 'generated'
        if ($certificate->status === 'generated') {
            $this->generateCertificatePDF($certificate);
        }

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate created successfully.');
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        $certificate->load(['student', 'course', 'exam']);
        
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Show the form for editing the specified certificate.
     */
    public function edit(Certificate $certificate)
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $courses = Course::where('is_active', 'active')->orderBy('title')->get();
        $exams = Exam::where('status', 'available')->orderBy('title')->get();

        return view('admin.certificates.edit', compact('certificate', 'students', 'courses', 'exams'));
    }

    /**
     * Update the specified certificate in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        $validatedData = $this->validateCertificateData($request);

        // Check for duplicate certificate (excluding current)
        if ($this->certificateExists(
            $validatedData['student_id'], 
            $validatedData['course_id'], 
            $validatedData['exam_id'],
            $certificate->id
        )) {
            return back()->withErrors(['error' => 'Certificate already exists for this student, course, and exam combination.']);
        }

        // If exam is selected, validate exam completion and passing score
        if (!empty($validatedData['exam_id'])) {
            $examValidation = $this->validateExamCompletion($validatedData['student_id'], $validatedData['exam_id']);
            if (!$examValidation['valid']) {
                return back()->withErrors(['error' => $examValidation['message']]);
            }
            // Use the actual exam score
            $validatedData['score'] = $examValidation['score'];
        }

        $oldStatus = $certificate->status;
        $certificate->update($validatedData);

        // Generate PDF if status changed to 'generated'
        if ($oldStatus !== 'generated' && $certificate->status === 'generated') {
            $this->generateCertificatePDF($certificate);
        }

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate updated successfully.');
    }

    /**
     * Remove the specified certificate from storage.
     */
    public function destroy(Certificate $certificate)
    {
        // Delete associated file if exists
        if ($certificate->file_path && Storage::exists('public/' . $certificate->file_path)) {
            Storage::delete('public/' . $certificate->file_path);
        }

        $certificate->delete();

        return redirect()->route('certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Toggle certificate status.
     */
    public function toggleStatus(Request $request, Certificate $certificate)
    {
        $request->validate([
            'status' => 'required|in:generated,pending,revoked'
        ]);

        $oldStatus = $certificate->status;
        $certificate->update(['status' => $request->status]);

        // Generate PDF if status changed to 'generated'
        if ($oldStatus !== 'generated' && $request->status === 'generated') {
            $this->generateCertificatePDF($certificate);
        } elseif ($request->status === 'revoked') {
            // Delete associated file if exists when revoking
            if ($certificate->file_path && Storage::exists('public/' . $certificate->file_path)) {
                Storage::delete('public/' . $certificate->file_path);
                $certificate->update(['file_path' => null]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Certificate status updated successfully.',
            'status' => $certificate->status
        ]);
    }

    /**
     * Download certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        if (!$certificate->file_path || !Storage::exists('public/' . $certificate->file_path)) {
            $this->generateCertificatePDF($certificate);
        }

        $filePath = storage_path('app/public/' . $certificate->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Certificate file not found.');
        }

        return response()->download($filePath, $certificate->certificate_number . '.pdf');
    }

    /**
     * Verify certificate using verification code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:8'
        ]);

        $certificate = Certificate::where('verification_code', strtoupper($request->verification_code))
            ->where('status', 'generated')
            ->with(['student', 'course', 'exam'])
            ->first();

        if (!$certificate) {
            return back()->withErrors(['verification_code' => 'Invalid verification code or certificate not found.']);
        }

        return view('admin.certificates.verify', compact('certificate'));
    }

    /**
     * Regenerate certificate PDF.
     */
    public function regenerate(Certificate $certificate)
    {
        if ($certificate->status !== 'generated') {
            return back()->with('error', 'Can only regenerate certificates with generated status.');
        }

        $this->generateCertificatePDF($certificate);

        return back()->with('success', 'Certificate PDF regenerated successfully.');
    }

    /**
     * Get courses by student (AJAX).
     */
    public function getCoursesByStudent(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return response()->json([]);
        }

        $courses = Course::whereHas('enrolments', function ($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })->orderBy('title')->get(['id', 'title', 'course_code']);

        return response()->json($courses);
    }

    /**
     * Get exams by course (AJAX).
     */
    public function getExamsByCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json([]);
        }

        $exams = Exam::where('course_id', $courseId)
            ->where('status', 'available')
            ->orderBy('title')
            ->get(['id', 'title', 'start_time']);

        return response()->json($exams);
    }

    /**
     * Get exam result for student (AJAX).
     */
    public function getExamResult(Request $request)
    {
        $studentId = $request->get('student_id');
        $examId = $request->get('exam_id');
        
        if (!$studentId || !$examId) {
            return response()->json([
                'success' => false,
                'message' => 'Student ID and Exam ID are required'
            ]);
        }

        $examValidation = $this->validateExamCompletion($studentId, $examId);
        
        if (!$examValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $examValidation['message']
            ]);
        }

        return response()->json([
            'success' => true,
            'score' => $examValidation['score'],
            'distinction' => $this->calculateDistinction($examValidation['score']),
            'can_generate_certificate' => true
        ]);
    }

    /**
     * Validate certificate data from request.
     */
    private function validateCertificateData(Request $request): array
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'exam_id' => 'nullable|exists:exams,id',
            'issued_at' => 'required|date',
            'status' => 'required|in:generated,pending,revoked',
            'completion_type' => 'required|in:course_completion,exam_passed,achievement,participation',
            'distinction' => 'nullable|in:pass,merit,distinction,high_distinction',
            'notes' => 'nullable|string|max:1000',
            'certificate_data' => 'nullable|array',
        ];

        // Score is only required if no exam is selected (manual entry)
        if (empty($request->exam_id)) {
            $rules['score'] = 'required|numeric|min:0|max:100';
        }

        return $request->validate($rules);
    }

    /**
     * Check if certificate already exists.
     */
    private function certificateExists(int $studentId, int $courseId, ?int $examId, ?int $excludeId = null): bool
    {
        $query = Certificate::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('exam_id', $examId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new certificate with validated data.
     */
    private function createCertificate(array $validatedData): Certificate
    {
        // Calculate distinction based on score if not provided
        if (empty($validatedData['distinction'])) {
            $validatedData['distinction'] = $this->calculateDistinction($validatedData['score']);
        }

        // Generate unique identifiers
        $validatedData['certificate_number'] = Certificate::generateCertificateNumber();
        $validatedData['verification_code'] = Certificate::generateVerificationCode();

        return Certificate::create($validatedData);
    }

    /**
     * Generate certificate PDF file.
     */
    private function generateCertificatePDF(Certificate $certificate): string
    {
        $certificateService = new CertificateService();
        return $certificateService->generatePDF($certificate);
    }

    /**
     * Calculate distinction based on score.
     */
    private function calculateDistinction(float $score): ?string
    {
        if ($score >= 85) return 'high_distinction';
        if ($score >= 75) return 'distinction';
        if ($score >= 65) return 'merit';
        if ($score >= 50) return 'pass';
        
        return null;
    }

    /**
     * Validate exam completion and passing score.
     */
    private function validateExamCompletion(int $studentId, int $examId): array
    {
        // Find the student's exam attempt
        $studentExam = StudentExams::where('student_id', $studentId)
            ->where('exam_id', $examId)
            ->where('status', 'completed')
            ->first();

        if (!$studentExam) {
            return [
                'valid' => false,
                'message' => 'Student has not completed this exam yet. Certificate cannot be generated.'
            ];
        }

        // Get the exam result
        $examResult = StudentExamResults::where('student_exam_id', $studentExam->id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        if (!$examResult) {
            return [
                'valid' => false,
                'message' => 'No exam results found for this student. Certificate cannot be generated.'
            ];
        }

        $passingScore = $studentExam->exam->passing_score ?? 50;
        if ($examResult->score < $passingScore) {
            return [
                'valid' => false,
                'message' => "Student did not pass the exam (Score: {$examResult->score}%). Certificate cannot be generated for failed exams."
            ];
        }

        return [
            'valid' => true,
            'score' => $examResult->score,
            'message' => 'Exam completed successfully'
        ];
    }
}