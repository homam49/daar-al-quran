<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UnifiedProfileController;
use App\Http\Controllers\MemorizationController;

// Student authentication (public)
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Student Email Verification Routes (public)
Route::get('/student/email/verify', [App\Http\Controllers\Auth\StudentVerificationController::class, 'show'])->name('student.verification.notice');
Route::get('/student/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\StudentVerificationController::class, 'verify'])->name('student.verification.verify');
Route::post('/student/email/resend', [App\Http\Controllers\Auth\StudentVerificationController::class, 'resend'])->name('student.verification.resend');
Route::post('/student/email/update', [App\Http\Controllers\Auth\StudentVerificationController::class, 'updateEmail'])->name('student.verification.update-email');

// Student Password Reset Routes (public)
Route::get('/student/password/reset', [App\Http\Controllers\Auth\StudentForgotPasswordController::class, 'showLinkRequestForm'])->name('student.password.request');
Route::post('/student/password/email', [App\Http\Controllers\Auth\StudentForgotPasswordController::class, 'sendResetLinkEmail'])->name('student.password.email');
Route::get('/student/password/reset/{token}', [App\Http\Controllers\Auth\StudentResetPasswordController::class, 'showResetForm'])->name('student.password.reset');
Route::post('/student/password/reset', [App\Http\Controllers\Auth\StudentResetPasswordController::class, 'reset'])->name('student.password.update');

// Protected student routes
Route::prefix('student')->middleware(['auth:student'])->group(function () {
    // Profile completion routes (accessible without completed profile)
    Route::get('/complete-profile', [StudentAuthController::class, 'showCompleteProfileForm'])->name('student.complete-profile');
    Route::post('/update-profile', [StudentAuthController::class, 'updateProfile'])->name('student.update-profile');

    // Fully authenticated student routes
    Route::middleware(['student.firstlogin', 'student.verified'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/attendance', [StudentController::class, 'attendance'])->name('student.attendance');
        
        // Profile routes
        Route::get('/profile', [UnifiedProfileController::class, 'showStudent'])->name('student.profile');
        Route::put('/profile/info', [UnifiedProfileController::class, 'updateStudentInfo'])->name('student.profile.info.update');
        Route::post('/profile/password', [UnifiedProfileController::class, 'updateStudentPassword'])->name('student.profile.password.update');
    
        // Messages routes
        Route::get('/messages', [StudentController::class, 'messages'])->name('student.messages');
        Route::get('/messages/compose', [StudentController::class, 'composeMessage'])->name('student.messages.compose');
        Route::post('/messages/send', [StudentController::class, 'sendMessage'])->name('student.messages.send');
        Route::get('/messages/{id}', [StudentController::class, 'viewMessage'])->name('student.messages.view');
        Route::post('/messages/{id}/mark-read', [StudentController::class, 'markMessageRead'])->name('student.messages.mark-read');
        
        // Classroom routes
        Route::get('/classrooms', [StudentController::class, 'classrooms'])->name('student.classrooms');
        
        // Memorization routes
        Route::get('/memorization', [MemorizationController::class, 'showStudent'])->name('student.memorization');
        Route::get('/memorization/{type}/{number}', [MemorizationController::class, 'getStudentProgress'])->name('student.memorization.progress');
    });
}); 