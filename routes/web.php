<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentExamsController;

use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\EWalletController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::patch('/admin/courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
Route::patch('/admin/courses/{course}/toggle-featured', [CourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');
Route::resource('/admin/courses', CourseController::class);

Route::resource('/admin/students', StudentController::class);
Route::post('/admin/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
Route::get('/admin/students/{student}/unenroll/{enrollment}', [StudentController::class, 'unenroll'])->name('students.unenroll');

Route::patch('/admin/exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus']);
Route::resource('/admin/exams', ExamController::class);

Route::get('/admin/questions/{id}/duplicate', [QuestionController::class, 'duplicate'])->name('questions.duplicate');
Route::patch('/admin/questions/{question}/toggle-status', [QuestionController::class, 'toggleStatus'])->name('questions.toggle-status');
Route::resource('/admin/questions', QuestionController::class);
Route::get('/admin/questions/create/{examId?}', [QuestionController::class, 'create'])->name('questions.create');
Route::post('admin/questions/import', [QuestionController::class, 'import'])->name('questions.import');

Route::get('/admin/results/view/{id}', [StudentExamsController::class, 'view'])->name('results.view');

// Route::middleware(['auth'])->group(function () {
    // Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    // Route::get('/student/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');
    Route::get('/student/ewallet', [EWalletController::class, 'index'])->name('student.ewallet.index');
    // Route::get('/student/exams', [StudentExamController::class, 'index'])->name('student.exams.index');
    // Route::get('/student/profile', [StudentExamController::class, 'index'])->name('student.profile');

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/courses', [StudentDashboardController::class, 'courses'])->name('student.courses');
    Route::get('/student/exams', [StudentDashboardController::class, 'exams'])->name('student.exams');
    Route::get('/student/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
    Route::put('/student/profile', [StudentDashboardController::class, 'updateProfile'])->name('student.profile.update');

    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.create');
    Route::get('/stripe/payment-success', [StripeController::class, 'handleTransaction'])->name('payment.success');

    Route::post('/paypal/create', [PayPalController::class, 'createOrder'])->name('paypal.create');
    Route::get('/paypal/return', [PayPalController::class, 'captureOrder'])->name('paypal.return');
    Route::get('/paypal/cancel', function () {
        return redirect()->route('student.ewallet.index')->with('error', 'Payment cancelled.');
    })->name('paypal.cancel');

    Route::get('/exam/{examId}', [StudentExamController::class, 'show'])->name('student.exam.show');
    Route::post('/exam/{examId}/start', [StudentExamController::class, 'start'])->name('student.exam.start');
    Route::get('/exam/{code}/{session_key}', [StudentExamController::class, 'showExamPage'])->name('exam.page');
    Route::post('/exam/{code}/page/{session_key}', [StudentExamController::class, 'showExamPage'])->name('exam.page.post');
    Route::post('/exam/{code}/feedback/{session_key}', [StudentExamController::class, 'submitFeedback'])->name('exam.feedback');
    Route::get('/exam/summary/{code}/{session_key}', [StudentExamController::class, 'showAfterExamCompleted'])->name('exam.summary');
    Route::post('/exam/{examId}/submit-answer', [StudentExamController::class, 'submitAnswer'])
        ->name('student.exam.submit_answer');

// });

require __DIR__.'/auth.php';
