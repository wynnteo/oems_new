<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExamRegistrationController;
use App\Http\Controllers\EWalletController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StudentExamController;

// Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardController,
    StudentController,
    CourseController,
    ExamController,
    QuestionController,
    StudentExamsController
};

// Student Controllers
use App\Http\Controllers\{
    ExamScheduleController,
    StudentDashboardController,
    StudentCourseController
};

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Required Routes
|--------------------------------------------------------------------------
*/
//Route::middleware(['auth', 'verified'])->group(function () {
    
    // Profile Management
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    //Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('admin')->group(function () { 
        // Admin Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Course Management
        Route::controller(CourseController::class)->prefix('courses')->name('courses.')->group(function () {
            Route::get('/search', 'search')->name('search');
            Route::patch('/{course}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::patch('/{course}/toggle-featured', 'toggleFeatured')->name('toggle-featured');
        });
        Route::resource('courses', CourseController::class)->names([
            'index' => 'courses.index',
            'create' => 'courses.create',
            'store' => 'courses.store',
            'show' => 'courses.show',
            'edit' => 'courses.edit',
            'update' => 'courses.update',
            'destroy' => 'courses.destroy',
        ]);

        // Certificate Management
        Route::resource('certificates', CertificateController::class)->names([
            'index' => 'certificates.index',
            'create' => 'certificates.create',
            'store' => 'certificates.store',
            'show' => 'certificates.show',
            'edit' => 'certificates.edit',
            'update' => 'certificates.update',
            'destroy' => 'certificates.destroy',
        ]);   

        // Student Management
        Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
            Route::post('/{student}/enroll', 'enroll')->name('enroll');
            Route::delete('/{student}/unenroll/{enrollment}', 'unenroll')->name('unenroll');
        });
        Route::resource('students', StudentController::class)->names([
            'index' => 'students.index',
            'create' => 'students.create',
            'store' => 'students.store',
            'show' => 'students.show',
            'edit' => 'students.edit',
            'update' => 'students.update',
            'destroy' => 'students.destroy',
        ]);

        // Exam Management
        Route::controller(ExamController::class)->prefix('exams')->name('exams.')->group(function () {
            Route::patch('/{exam}/toggle-status', 'toggleStatus')->name('toggle-status');
        });
        Route::resource('exams', ExamController::class)->names([
            'index' => 'exams.index',
            'create' => 'exams.create',
            'store' => 'exams.store',
            'show' => 'exams.show',
            'edit' => 'exams.edit',
            'update' => 'exams.update',
            'destroy' => 'exams.destroy',
        ]);

        // Question Management
        Route::controller(QuestionController::class)->prefix('questions')->name('questions.')->group(function () {
            Route::get('/create/{examId?}', 'create')->name('create');
            Route::post('/import', 'import')->name('import');
            Route::get('/{id}/duplicate', 'duplicate')->name('duplicate');
            Route::patch('/{question}/toggle-status', 'toggleStatus')->name('toggle-status');
        });
        Route::resource('questions', QuestionController::class)->names([
            'index' => 'questions.index',
            'store' => 'questions.store',
            'show' => 'questions.show',
            'edit' => 'questions.edit',
            'update' => 'questions.update',
            'destroy' => 'questions.destroy',
        ]);

        // Results Management
        Route::controller(StudentExamsController::class)->prefix('results')->name('results.')->group(function () {
            Route::get('/view/{id}', 'view')->name('view');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    // Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
    Route::prefix('student')->name('student.')->group(function () {
        // Student Dashboard & Profile
        Route::controller(StudentDashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            Route::get('/courses', 'courses')->name('courses');
            Route::get('/course/{id}/details', 'showCourseDetails')->name('courses.show');
            Route::get('/exams', 'exams')->name('exams');
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');
        });

        // E-Wallet Management
        Route::get('/ewallet', [EWalletController::class, 'index'])->name('ewallet.index');
        Route::get('/ewallet/topup', [EWalletController::class, 'topup'])->name('ewallet.topup');
        
        // Exam Scheduling
        Route::controller(ExamScheduleController::class)->prefix('exams')->name('exams.')->group(function () {
            Route::get('/schedule', 'index')->name('schedule');
            Route::get('/scheduled', 'getScheduledExams')->name('scheduled');
            
            // Individual Exam Actions
            Route::prefix('/{exam}')->group(function () {
                Route::get('/show', 'showExamDetails')->name('show');
                Route::get('/details', 'getExamDetails')->name('details');
                Route::get('/full-details', 'getFullExamDetails')->name('full-details');
                Route::get('/availability', 'checkExamAvailability')->name('check-availability');
                
                // Scheduling Actions
                Route::post('/schedule', 'scheduleExam')->name('schedule-exam');
                Route::post('/cancel', 'cancelExamRegistration')->name('cancel-registration');
                
                // Rescheduling
                Route::get('/reschedule', 'showRescheduleForm')->name('reschedule.show');
                Route::post('/reschedule', 'rescheduleExam')->name('reschedule.process');
            });
        });

        // Exam Taking
        Route::controller(StudentExamController::class)->prefix('exam')->name('exam.')->group(function () {
            Route::get('/{examId}', 'show')->name('show');
            Route::post('/{examId}/start', 'start')->name('start');
            Route::post('/{examId}/submit-answer', 'submitAnswer')->name('submit_answer');
            
            // Exam Session Routes
            Route::get('/{code}/{session_key}', 'showExamPage')->name('page');
            Route::post('/{code}/page/{session_key}', 'showExamPage')->name('page.post');
            Route::post('/{code}/feedback/{session_key}', 'submitFeedback')->name('feedback');
            Route::get('/summary/{code}/{session_key}', 'showAfterExamCompleted')->name('summary');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Payment Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('payments')->name('payments.')->group(function () {
        
        // Stripe Payment Routes
        Route::controller(StripeController::class)->prefix('stripe')->name('stripe.')->group(function () {
            Route::post('/create-intent', 'createPaymentIntent')->name('create-intent');
            Route::get('/payment-success', 'handleTransaction')->name('success');
        });

        // PayPal Payment Routes
        Route::controller(PayPalController::class)->prefix('paypal')->name('paypal.')->group(function () {
            Route::post('/create', 'createOrder')->name('create');
            Route::get('/return', 'captureOrder')->name('return');
            Route::get('/cancel', function () {
                return redirect()->route('student.ewallet.index')->with('error', 'Payment cancelled.');
            })->name('cancel');
        });
    });
//});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';