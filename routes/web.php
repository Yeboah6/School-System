<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('school')->group(function () {
        Route::get('/', [SchoolController::class, 'index'])->name('school.index');
        Route::get('/details', [SchoolController::class, 'details'])->name('school.details');
        Route::post('/profile', [SchoolController::class, 'updateSchoolProfile'])->name('school.profile.update');
        Route::post('/academic-years', [SchoolController::class, 'storeAcademicYear'])->name('school.academic-years.store');
        Route::put('/academic-years/{academicYear}', [SchoolController::class, 'updateAcademicYear'])->name('school.academic-years.update');
        Route::delete('/academic-years/{academicYear}', [SchoolController::class, 'destroyAcademicYear'])->name('school.academic-years.destroy');
        Route::post('/terms', [SchoolController::class, 'storeTerm'])->name('school.terms.store');
        Route::put('/terms/{term}', [SchoolController::class, 'updateTerm'])->name('school.terms.update');
        Route::delete('/terms/{term}', [SchoolController::class, 'destroyTerm'])->name('school.terms.destroy');
        Route::post('/departments', [SchoolController::class, 'storeDepartment'])->name('school.departments.store');
        Route::put('/departments/{department}', [SchoolController::class, 'updateDepartment'])->name('school.departments.update');
        Route::delete('/departments/{department}', [SchoolController::class, 'destroyDepartment'])->name('school.departments.destroy');
        Route::post('/classes', [SchoolController::class, 'storeClass'])->name('school.classes.store');
        Route::put('/classes/{class}', [SchoolController::class, 'updateClass'])->name('school.classes.update');
        Route::delete('/classes/{class}', [SchoolController::class, 'destroyClass'])->name('school.classes.destroy');
        Route::post('/subjects', [SchoolController::class, 'storeSubject'])->name('school.subjects.store');
        Route::put('/subjects/{subject}', [SchoolController::class, 'updateSubject'])->name('school.subjects.update');
        Route::delete('/subjects/{subject}', [SchoolController::class, 'destroySubject'])->name('school.subjects.destroy');
    });

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::post('/students/notes', [StudentController::class, 'storeNote'])->name('students.notes.store');
    Route::post('/students/timeline', [StudentController::class, 'storeTimeline'])->name('students.timeline.store');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
});

Route::get('/', function () {
    return redirect()->route('login');
});
