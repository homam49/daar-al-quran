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
        $this->authorize('view', $session->classRoom);

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
        $this->authorize('update', $session->classRoom);

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:255',
        ]);

        // Update or create attendance records
        foreach ($request->attendance as $studentId => $status) {
            $attendance = Attendance::updateOrCreate(
                [
                    'class_session_id' => $session->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => $status,
                    'note' => $request->notes[$studentId] ?? null,
                ]
            );
        }

        return redirect()->route('classroom.sessions.show', [$session->classRoom, $session])
            ->with('success', 'تم تحديث الحضور بنجاح');
    }
}
