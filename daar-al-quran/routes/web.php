<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolDeletionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UnifiedProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Email Verification Routes
Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
Route::post('/email/resend-guest', [App\Http\Controllers\Auth\VerificationController::class, 'resendForGuest'])->name('verification.resend-guest');
Route::post('/email/update', [App\Http\Controllers\Auth\VerificationController::class, 'updateEmail'])->name('verification.update-email');

// Password Reset Routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Common routes for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    // Unified profile routes
    Route::get('/profile', [UnifiedProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/password', [UnifiedProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/name', [UnifiedProfileController::class, 'updateName'])->name('profile.name.update');
    Route::put('/profile/phone', [UnifiedProfileController::class, 'updatePhone'])->name('profile.update.phone');
    Route::put('/profile/address', [UnifiedProfileController::class, 'updateAddress'])->name('profile.update.address');
    Route::put('/profile/info', [UnifiedProfileController::class, 'updatePersonalInfo'])->name('profile.update.info');
});

// Student authentication
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Student Email Verification Routes
Route::get('/student/email/verify', [App\Http\Controllers\Auth\StudentVerificationController::class, 'show'])->name('student.verification.notice');
Route::get('/student/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\StudentVerificationController::class, 'verify'])->name('student.verification.verify');
Route::post('/student/email/resend', [App\Http\Controllers\Auth\StudentVerificationController::class, 'resend'])->name('student.verification.resend');
Route::post('/student/email/update', [App\Http\Controllers\Auth\StudentVerificationController::class, 'updateEmail'])->name('student.verification.update-email');

// Student Password Reset Routes
Route::get('/student/password/reset', [App\Http\Controllers\Auth\StudentForgotPasswordController::class, 'showLinkRequestForm'])->name('student.password.request');
Route::post('/student/password/email', [App\Http\Controllers\Auth\StudentForgotPasswordController::class, 'sendResetLinkEmail'])->name('student.password.email');
Route::get('/student/password/reset/{token}', [App\Http\Controllers\Auth\StudentResetPasswordController::class, 'showResetForm'])->name('student.password.reset');
Route::post('/student/password/reset', [App\Http\Controllers\Auth\StudentResetPasswordController::class, 'reset'])->name('student.password.update');

// Moderator routes
Route::middleware(['auth', 'role:moderator', 'verified'])->prefix('moderator')->group(function () {
    Route::get('dashboard', [ModeratorController::class, 'dashboard'])->name('moderator.dashboard');
    
    // User management routes
    Route::get('users', [ModeratorController::class, 'users'])->name('moderator.users');
    Route::get('users/create', [ModeratorController::class, 'createUser'])->name('moderator.users.create');
    Route::get('users/{user}', [ModeratorController::class, 'showUser'])->name('moderator.users.show');
    Route::delete('users/{user}', [ModeratorController::class, 'deleteUser'])->name('moderator.users.delete');
    Route::post('users/{user}/approve', [ModeratorController::class, 'approveUser'])->name('moderator.users.approve');
    Route::delete('users/{user}/reject', [ModeratorController::class, 'rejectUser'])->name('moderator.users.reject');
    
    // Pending users routes
    Route::get('pending-users', [ModeratorController::class, 'pendingUsers'])->name('moderator.pending-users');
    
    // School management routes
    Route::get('schools', [ModeratorController::class, 'schools'])->name('moderator.schools');
    Route::get('schools/create', [ModeratorController::class, 'createSchool'])->name('moderator.schools.create');
    Route::post('schools', [ModeratorController::class, 'storeSchool'])->name('moderator.schools.store');
    Route::get('schools/{school}', [ModeratorController::class, 'showSchool'])->name('moderator.schools.show');
    Route::get('schools/{school}/edit', [ModeratorController::class, 'editSchool'])->name('moderator.schools.edit');
    Route::put('schools/{school}', [ModeratorController::class, 'updateSchool'])->name('moderator.schools.update');
    Route::delete('schools/{school}', [ModeratorController::class, 'deleteSchool'])->name('moderator.schools.delete');
    
    // Profile route - redirect to unified profile
    Route::get('profile', function() {
        return redirect()->route('profile.show');
    })->name('moderator.profile');
    
    // Reports route
    Route::get('reports/generate', [ModeratorController::class, 'generateReports'])->name('moderator.reports.generate');
    Route::get('reports', [ModeratorController::class, 'reports'])->name('moderator.reports');
    
    // System routes
    Route::get('system-logs', [ModeratorController::class, 'systemLogs'])->name('moderator.system-logs');
    Route::get('system-backup', [ModeratorController::class, 'systemBackup'])->name('moderator.system-backup');
});

// Admin routes
Route::middleware(['auth', 'role:admin', 'approved', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // School deletion
    Route::get('/schools/deletion-form', [SchoolDeletionController::class, 'showForm'])->name('admin.schools.deletion-form');
    Route::post('/schools/deletion', [SchoolDeletionController::class, 'deletionAction'])->name('admin.schools.deletion-action');
    
    // School management routes - simplified for single school
    Route::get('/schools/create', [SchoolController::class, 'create'])->name('admin.schools.create');
    Route::post('/schools', [SchoolController::class, 'store'])->name('admin.schools.store');
    Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('admin.schools.edit');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('admin.schools.delete');
    
    // Redirect old schools index route to dashboard
    Route::get('/schools', function() {
        return redirect()->route('admin.dashboard');
    });
    
    // Redirect old school show route to dashboard (should be after specific /schools/* routes)
    Route::get('/schools/{school}', function() {
        return redirect()->route('admin.dashboard');
    });

    // Also redirect the confirm delete route
    Route::get('/schools/{school}/confirm-delete', function() {
        return redirect()->route('admin.dashboard');
    });
    
    // Teacher management routes
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::get('/teachers/{user}', [AdminController::class, 'showTeacher'])->name('admin.teachers.show');
    Route::put('/teachers/{user}/approve', [AdminController::class, 'approveTeacher'])->name('admin.teachers.approve');
    Route::post('/teachers/approve-school', [AdminController::class, 'approveTeacherSchool'])->name('admin.teachers.approve-school');
    Route::post('/teachers/reject-school', [AdminController::class, 'rejectTeacherSchool'])->name('admin.teachers.reject-school');
    Route::delete('/teachers/{user}', [AdminController::class, 'deleteTeacher'])->name('admin.teachers.delete');
    
    // Student management routes
    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::delete('/students/{student}', [AdminController::class, 'deleteStudent'])->name('admin.students.delete');
    
    // Classroom management routes
    Route::get('/classrooms', [AdminController::class, 'classrooms'])->name('admin.classrooms');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    
    // Profile - redirect to unified profile
    Route::get('/profile', function() {
        return redirect()->route('profile.show');
    })->name('admin.profile');
    Route::post('/profile/password', [AdminController::class, 'updatePassword'])->name('admin.password.update');
    Route::put('/profile/name', [AdminController::class, 'updateName'])->name('admin.name.update');
});

// Teacher routes
Route::middleware(['auth', 'role:teacher', 'approved', 'verified'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/schools', [TeacherController::class, 'schools'])->name('teacher.schools');
    Route::get('/join-school', [TeacherController::class, 'showJoinSchoolForm'])->name('teacher.join-school.form');
    Route::post('/join-school', [TeacherController::class, 'joinSchool'])->name('teacher.join-school.store');
    
    // Profile - redirect to unified profile
    Route::get('/profile', function() {
        return redirect()->route('profile.show');
    })->name('teacher.profile');
    Route::post('/profile/password/update', [TeacherController::class, 'updatePassword'])->name('teacher.password.update');
    Route::post('/profile/name/update', [TeacherController::class, 'updateName'])->name('teacher.name.update');
    
    // Reports routes
    Route::get('/reports', [TeacherController::class, 'reports'])->name('teacher.reports');
    Route::get('/reports/attendance', [TeacherController::class, 'attendanceReport'])->name('teacher.reports.attendance');
    Route::get('/reports/performance', [TeacherController::class, 'performanceReport'])->name('teacher.reports.performance');
    Route::get('/reports/export/{type}', [TeacherController::class, 'exportReport'])->name('teacher.reports.export');
    Route::get('/classrooms/{classroom}/students/list', [TeacherController::class, 'getStudentsList'])->name('teacher.classroom.students.list');
    
    // Student routes
    Route::get('/students', [StudentController::class, 'allStudents'])->name('students.index');
    
    // Session routes
    Route::get('/sessions', [ClassSessionController::class, 'allSessions'])->name('sessions.index');
    
    // Classroom routes
    Route::resource('classrooms', ClassRoomController::class);
    Route::post('/classrooms/broadcast-message', [ClassRoomController::class, 'broadcastMessage'])->name('classrooms.broadcast-message');
    
    // Simplify student management - remove redundant routes
    // Only keep the route for displaying students in a classroom
    Route::get('/classrooms/{classroom}/students', [StudentController::class, 'index'])->name('teacher.classroom.students');
    
    // Session management
    Route::get('/classrooms/{classroom}/sessions', [ClassSessionController::class, 'index'])->name('classroom.sessions.index');
    Route::get('/classrooms/{classroom}/sessions/create', [ClassSessionController::class, 'create'])->name('classroom.sessions.create');
    Route::post('/classrooms/{classroom}/sessions', [ClassSessionController::class, 'store'])->name('classroom.sessions.store');
    Route::get('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'show'])->name('classroom.sessions.show');
    Route::get('/classrooms/{classroom}/sessions/{session}/edit', [ClassSessionController::class, 'edit'])->name('classroom.sessions.edit');
    Route::put('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'update'])->name('classroom.sessions.update');
    Route::delete('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'destroy'])->name('classroom.sessions.destroy');
    
    // Add the missing route for removing students from classrooms
    Route::delete('/classroom/{classroom}/students/{student}', [StudentController::class, 'removeFromClassroom'])->name('classroom.students.remove');
    
    // Add the missing route for adding students to classrooms
    Route::post('/classroom/{classroom}/students/store', [StudentController::class, 'storeInClassroom'])->name('classroom.students.store');
    
    // Add the missing route for attaching existing students to classrooms
    Route::post('/classroom/{classroom}/students/attach', [StudentController::class, 'attachToClassroom'])->name('classroom.students.attach');
    
    // Add the missing route for sending notes to students in a classroom
    Route::post('/classroom/{classroom}/students/note', [StudentController::class, 'sendNote'])->name('classroom.students.note');
    
    // Attendance management
    Route::get('/sessions/{session}/attendance', [AttendanceController::class, 'edit'])->name('sessions.attendance.edit');
    Route::post('/sessions/{session}/attendance', [AttendanceController::class, 'update'])->name('sessions.attendance.update');
    
    // Messages and announcements
    Route::get('/messages', [MessageController::class, 'index'])->name('teacher.messages');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('teacher.messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('teacher.messages.store');
    Route::get('/messages/{id}/reply', [MessageController::class, 'reply'])->name('teacher.messages.reply');
    Route::post('/messages/{id}/reply', [MessageController::class, 'sendReply'])->name('teacher.messages.send-reply');
    Route::post('/messages/{id}/mark-read', [MessageController::class, 'markAsRead'])->name('teacher.messages.mark-read');
});

// Quick temporary route for viewing student credentials
Route::get('/classroom/{classroom}/students/{student}/view-credentials', function(App\Models\ClassRoom $classroom, App\Models\Student $student) {
    // Check authorization
    if ($classroom->user_id !== auth()->id()) {
        return redirect()->back()->with('error', 'غير مصرح بالوصول إلى هذا الفصل');
    }
    
    // Simple view with credentials
    return view('teacher.students.simple-credentials', [
        'classroom' => $classroom,
        'student' => $student
    ]);
})->name('classroom.students.view-credentials');

// Student routes
Route::prefix('student')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
    
    // Authentication required routes - grouped to apply auth:student middleware once
    Route::middleware(['auth:student'])->group(function () {
        // Email verification routes - accessible without completed profile
        Route::get('/email/verify', [App\Http\Controllers\Auth\StudentVerificationController::class, 'show'])->name('student.verification.notice');
        Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\StudentVerificationController::class, 'verify'])->name('student.verification.verify');
        Route::post('/email/resend', [App\Http\Controllers\Auth\StudentVerificationController::class, 'resend'])->name('student.verification.resend');
        
        // Profile completion routes
        Route::get('/complete-profile', [StudentAuthController::class, 'showCompleteProfileForm'])->name('student.complete-profile');
        Route::post('/update-profile', [StudentAuthController::class, 'updateProfile'])->name('student.update-profile');
    
        // Protected routes - need both firstlogin and verified middleware
        Route::middleware(['student.firstlogin', 'student.verified'])->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/attendance', [StudentController::class, 'attendance'])->name('student.attendance');
        
            // Profile routes - using UnifiedProfileController
            Route::get('/profile', [UnifiedProfileController::class, 'showStudent'])->name('student.profile');
            Route::put('/profile/info', [UnifiedProfileController::class, 'updateStudentInfo'])->name('student.profile.info.update');
            Route::post('/profile/password', [UnifiedProfileController::class, 'updateStudentPassword'])->name('student.profile.password.update');
        
        // Messages routes
        Route::get('/messages', [StudentController::class, 'messages'])->name('student.messages');
            
            // Student compose message routes
            Route::get('/messages/compose', [StudentController::class, 'composeMessage'])->name('student.messages.compose');
            Route::post('/messages/send', [StudentController::class, 'sendMessage'])->name('student.messages.send');
            
            // View specific message
        Route::get('/messages/{id}', [StudentController::class, 'viewMessage'])->name('student.messages.view');
        Route::post('/messages/{id}/mark-read', [StudentController::class, 'markMessageRead'])->name('student.messages.mark-read');
        
        // Classroom routes
        Route::get('/classrooms', [StudentController::class, 'classrooms'])->name('student.classrooms');
        });
    });
});
