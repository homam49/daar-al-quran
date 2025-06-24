<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolDeletionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnifiedProfileController;
use Illuminate\Support\Facades\Auth;

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
    
    // Redirect old schools routes to dashboard
    Route::get('/schools', function() {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/schools/{school}', function() {
        return redirect()->route('admin.dashboard');
    });
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

// Include modular route files
require __DIR__.'/modules/teacher.php';
require __DIR__.'/modules/student.php';
