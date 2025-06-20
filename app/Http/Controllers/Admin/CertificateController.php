<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
     * Validate certificate data from request.
     */
    private function validateCertificateData(Request $request): array
    {
        return $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'exam_id' => 'nullable|exists:exams,id',
            'score' => 'required|numeric|min:0|max:100',
            'issued_at' => 'required|date',
            'status' => 'required|in:generated,pending,revoked',
            'completion_type' => 'required|in:course_completion,exam_passed,achievement,participation',
            'distinction' => 'nullable|in:pass,merit,distinction,high_distinction',
            'notes' => 'nullable|string|max:1000',
            'certificate_data' => 'nullable|array',
        ]);
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
        $certificate->load(['student', 'course', 'exam']);

        // Create certificates directory if it doesn't exist
        $certificatesDir = 'certificates/' . date('Y/m');
        if (!Storage::exists('public/' . $certificatesDir)) {
            Storage::makeDirectory('public/' . $certificatesDir);
        }

        $fileName = $certificate->certificate_number . '.pdf';
        $filePath = $certificatesDir . '/' . $fileName;

        // Generate PDF using a view
        $pdf = PDF::loadView('admin.certificates.pdf', compact('certificate'));
        
        // Save PDF to storage
        Storage::put('public/' . $filePath, $pdf->output());

        // Update certificate with file path
        $certificate->update(['file_path' => $filePath]);

        return $filePath;
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
}