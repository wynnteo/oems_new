<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\QuestionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::resource('/admin/courses', CourseController::class);


Route::resource('/admin/students', StudentController::class);
Route::post('/admin/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
Route::get('/admin/students/{student}/unenroll/{enrollment}', [StudentController::class, 'unenroll'])->name('students.unenroll');


Route::resource('/admin/exams', ExamController::class);

Route::resource('/admin/questions', QuestionController::class);
Route::get('/admin/questions/create/{examId?}', [QuestionController::class, 'create'])->name('questions.create');
Route::post('admin/questions/import', [QuestionController::class, 'import'])->name('questions.import');

require __DIR__.'/auth.php';
