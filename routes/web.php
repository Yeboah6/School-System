<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\AuditController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

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
        Route::post('/branches', [SchoolController::class, 'storeBranch'])->name('school.branches.store');
        Route::put('/branches/{branch}', [SchoolController::class, 'updateBranch'])->name('school.branches.update');
        Route::delete('/branches/{branch}', [SchoolController::class, 'destroyBranch'])->name('school.branches.destroy');
    });

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/notes', [StudentController::class, 'storeNote'])->name('students.notes.store');
    Route::post('/students/timeline', [StudentController::class, 'storeTimeline'])->name('students.timeline.store');

    Route::get('/staff/overview', [StaffController::class, 'overview'])->name('staff.overview');
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/accountant-portal', [FinanceController::class, 'index'])->name('accountant-portal');
    Route::post('/finance/fee-types', [FinanceController::class, 'storeFeeType'])->name('finance.fee-types.store');
    Route::post('/finance/structures', [FinanceController::class, 'storeStructure'])->name('finance.structures.store');
    Route::post('/finance/invoices', [FinanceController::class, 'storeInvoice'])->name('finance.invoices.store');
    Route::post('/finance/invoices/{invoice}/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');

    Route::get('/examinations', [ExaminationController::class, 'index'])->name('examinations.index');
    Route::post('/examinations', [ExaminationController::class, 'storeExamination'])->name('examinations.store');
    Route::post('/examinations/grading-scales', [ExaminationController::class, 'storeScale'])->name('examinations.grading-scales.store');
    Route::post('/examinations/assessments', [ExaminationController::class, 'storeAssessment'])->name('examinations.assessments.store');
    Route::get('/examinations/assessments/{assessment}/results', [ExaminationController::class, 'results'])->name('examinations.results');
    Route::post('/examinations/assessments/{assessment}/results', [ExaminationController::class, 'storeResults'])->name('examinations.results.store');

    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/timetable/slots', [TimetableController::class, 'store'])->name('timetable.slots.store');
    Route::put('/timetable/slots/{slot}', [TimetableController::class, 'update'])->name('timetable.slots.update');

    Route::get('/classes', [ClassesController::class, 'index'])->name('classes.index');
    Route::post('/classes/promote', [ClassesController::class, 'promote'])->name('classes.promote');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

    Route::get('/parent-portal', [PortalController::class, 'parent'])->name('parent-portal');
    Route::get('/teacher-portal', [PortalController::class, 'teacher'])->name('teacher-portal');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
    Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
    Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
});

Route::get('/', function () {
    return redirect()->route('login');
});
