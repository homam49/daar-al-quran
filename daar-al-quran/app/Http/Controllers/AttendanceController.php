<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:teacher', 'approved']);
    }

    /**
     * Show the form for editing attendance for a session.
     *
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassSession $session)
    {
        // Check if the authenticated user owns the classroom of this session
        if ($session->classRoom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الجلسة');
        }

        $students = $session->classRoom->students;
        $attendances = $session->attendances()->pluck('status', 'student_id')->toArray();
        $notes = $session->attendances()->pluck('note', 'student_id')->toArray();
        
        return view('teacher.attendances.edit', compact('session', 'students', 'attendances', 'notes'));
    }

    /**
     * Update the attendance records for a session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassSession $session)
    {
        // Check if the authenticated user owns the classroom of this session
        if ($session->classRoom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الجلسة');
        }

        $request->validate([
            'status' => 'required|array',
            'status.*' => 'required|in:present,absent,late',
            'note' => 'nullable|array',
            'note.*' => 'nullable|string',
        ]);

        $students = $session->classRoom->students;

        foreach ($students as $student) {
            if (isset($request->status[$student->id])) {
                // Get or create attendance record
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_session_id' => $session->id,
                    ],
                    [
                        'status' => $request->status[$student->id],
                        'note' => $request->note[$student->id] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('teacher.classroom.sessions.show', [$session->classRoom->id, $session->id])
            ->with('success', 'تم تحديث الحضور بنجاح');
    }
}
