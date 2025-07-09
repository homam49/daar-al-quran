<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MemorizationController;

// Teacher routes
Route::middleware(['auth', 'role:teacher', 'approved', 'verified'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/schools', [TeacherController::class, 'schools'])->name('teacher.schools');
    Route::get('/join-school', [TeacherController::class, 'showJoinSchoolForm'])->name('teacher.join-school.form');
    Route::post('/join-school', [TeacherController::class, 'joinSchool'])->name('teacher.join-school.store');
    
    // Profile routes
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
    
    // Student management routes
    Route::get('/students', [StudentController::class, 'allStudents'])->name('teacher.students.index');
    Route::get('/classrooms/{classroom}/students', [StudentController::class, 'index'])->name('teacher.classroom.students');
    Route::get('/classrooms/{classroom}/students/create', [StudentController::class, 'create'])->name('classroom.students.create');
    Route::post('/classrooms/{classroom}/students', [StudentController::class, 'store'])->name('classroom.students.store');
    Route::get('/classrooms/{classroom}/students/{student}/edit', [StudentController::class, 'edit'])->name('classroom.students.edit');
    Route::put('/classrooms/{classroom}/students/{student}', [StudentController::class, 'update'])->name('classroom.students.update');
    Route::delete('/classroom/{classroom}/students/{student}', [StudentController::class, 'removeFromClassroom'])->name('classroom.students.remove');
    Route::post('/classroom/{classroom}/students/attach', [StudentController::class, 'store'])->name('classroom.students.attach');
    Route::post('/classroom/{classroom}/students/note', [StudentController::class, 'sendNote'])->name('classroom.students.note');
    Route::get('/classrooms/{classroom}/students/{student}/credentials', [StudentController::class, 'viewCredentials'])->name('classroom.students.credentials');
    
    // PDF generation routes
    Route::post('/classrooms/{classroom}/students/credentials/pdf', [TeacherController::class, 'generateClassroomCredentialsPdf'])->name('classroom.students.credentials.pdf');
    Route::post('/students/credentials/pdf', [TeacherController::class, 'generateSelectedCredentialsPdf'])->name('teacher.students.credentials.pdf');
    
    // Session routes
    Route::get('/sessions', [ClassSessionController::class, 'all'])->name('sessions.index');
    
    // Classroom routes
    Route::resource('classrooms', ClassRoomController::class);
    Route::post('/classrooms/broadcast-message', [ClassRoomController::class, 'broadcastMessage'])->name('classrooms.broadcast-message');
    
    // Session management
    Route::get('/classrooms/{classroom}/sessions/create', [ClassSessionController::class, 'create'])->name('classroom.sessions.create');
    Route::post('/classrooms/{classroom}/sessions', [ClassSessionController::class, 'store'])->name('classroom.sessions.store');
    Route::get('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'show'])->name('classroom.sessions.show');
    Route::get('/classrooms/{classroom}/sessions/{session}/edit', [ClassSessionController::class, 'edit'])->name('classroom.sessions.edit');
    Route::put('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'update'])->name('classroom.sessions.update');
    Route::delete('/classrooms/{classroom}/sessions/{session}', [ClassSessionController::class, 'destroy'])->name('classroom.sessions.destroy');
    
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
    
    // Memorization tracking routes
    Route::get('/students/{student}/memorization', [MemorizationController::class, 'show'])->name('teacher.memorization.show');
    Route::post('/students/{student}/memorization', [MemorizationController::class, 'update'])->name('teacher.memorization.update');
    Route::get('/students/{student}/memorization/{type}/{number}', [MemorizationController::class, 'getProgressInfo'])->name('teacher.memorization.progress');
    Route::post('/students/{student}/memorization/batch', [MemorizationController::class, 'batchUpdate'])->name('teacher.memorization.batch_update');
}); 