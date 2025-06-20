<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Certificate;
use App\Models\ExamRegistration;
use App\Models\StudentExamResults;
use App\Models\Transaction;
use Carbon\Carbon;
use stdClass;

class DashboardController extends Controller
{
    public function index() 
    {
        // Basic counts
        $totalCourses = Course::count();
        $totalStudents = Student::count();
        $totalExams = Exam::count();
        $totalQuestions = Question::count();
        $totalCertificates = Certificate::where('status', 'generated')->count();
        
        // Recent activity counts (last 30 days)
        $recentRegistrations = ExamRegistration::where('registered_at', '>=', Carbon::now()->subDays(30))->count();
        $recentExamAttempts = StudentExamResults::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $recentCertificates = Certificate::where('issued_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Revenue statistics
        $totalRevenue = Transaction::where('type', 'debit')->where('status', 'completed')->sum('amount');
        $monthlyRevenue = Transaction::where('type', 'debit')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');
        
        // Exam statistics
        $activeExams = Exam::where('status', 'active')->count();
        $completedExamAttempts = StudentExamResults::count();
        $averageScore = StudentExamResults::avg('score') ?? 0;
        
        // Course enrollment statistics
        $totalEnrollments = DB::table('enrolments')->count();
        $popularCourses = Course::withCount('enrolments')
            ->orderBy('enrolments_count', 'desc')
            ->limit(5)
            ->get();
        
        // Recent exams with statistics
        $recentExams = Exam::with('course')
            ->withCount([
                'studentExams as total_attempts',
                'studentExams as completed_attempts' => function($query) {
                    $query->whereNotNull('completed_at');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function($exam) {
                $exam->completion_rate = $exam->total_attempts > 0 
                    ? round(($exam->completed_attempts / $exam->total_attempts) * 100, 1)
                    : 0;
                
                // Get average score for this exam
                $exam->average_score = StudentExamResults::whereHas('studentExam', function($query) use ($exam) {
                    $query->where('exam_id', $exam->id);
                })->avg('score') ?? 0;
                
                return $exam;
            });
        
        // Monthly registration chart data (last 6 months)
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = ExamRegistration::whereYear('registered_at', $date->year)
                ->whereMonth('registered_at', $date->month)
                ->count();
            $monthlyRegistrations[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        
        // Monthly revenue chart data (last 6 months)
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Transaction::where('type', 'debit')
                ->where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            $monthlyRevenueData[] = [
                'month' => $date->format('M Y'),
                'revenue' => floatval($revenue)
            ];
        }
        
        // Exam performance distribution
        $performanceDistribution = StudentExamResults::selectRaw('
            CASE 
                WHEN score >= 90 THEN "Excellent (90-100%)"
                WHEN score >= 80 THEN "Good (80-89%)"
                WHEN score >= 70 THEN "Average (70-79%)"
                WHEN score >= 60 THEN "Below Average (60-69%)"
                ELSE "Poor (<60%)"
            END as performance_category,
            COUNT(*) as count
        ')
        ->groupBy('performance_category')
        ->get()
        ->pluck('count', 'performance_category')
        ->toArray();
        
        // Recent activities (last 10)
        $recentActivities = collect();
        
        // Recent registrations
        $recentRegActivities = ExamRegistration::with(['exam', 'student'])
            ->where('registered_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('registered_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($reg) {
                return (object)[
                    'type' => 'registration',
                    'title' => 'New exam registration',
                    'description' => "Student registered for " . $reg->exam->title,
                    'time' => $reg->registered_at,
                    'icon' => 'assignment_ind',
                    'color' => 'info'
                ];
            });
        
        // Recent certificates
        $recentCertActivities = Certificate::with(['student', 'course'])
            ->where('issued_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('issued_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($cert) {
                return (object)[
                    'type' => 'certificate',
                    'title' => 'Certificate issued',
                    'description' => "Certificate issued for " . $cert->course->title,
                    'time' => $cert->issued_at,
                    'icon' => 'card_membership',
                    'color' => 'success'
                ];
            });
        
        $recentActivities = $recentRegActivities->merge($recentCertActivities)
            ->sortByDesc('time')
            ->take(6);
        
        return view('admin.dashboard', compact(
            'totalCourses', 
            'totalStudents', 
            'totalExams', 
            'totalQuestions',
            'totalCertificates',
            'recentRegistrations',
            'recentExamAttempts',
            'recentCertificates',
            'totalRevenue',
            'monthlyRevenue',
            'activeExams',
            'completedExamAttempts',
            'averageScore',
            'totalEnrollments',
            'popularCourses',
            'recentExams',
            'monthlyRegistrations',
            'monthlyRevenueData',
            'performanceDistribution',
            'recentActivities'
        ));
    }
}