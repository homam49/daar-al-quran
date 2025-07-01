<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Attendance;
use App\Services\ClassSessionService;
use App\Services\TeacherService;
use App\Http\Requests\StoreSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
{
    protected $classSessionService;
    protected $teacherService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ClassSessionService $classSessionService, TeacherService $teacherService)
    {
        $this->middleware(['auth', 'role:teacher', 'approved']);
        $this->classSessionService = $classSessionService;
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of all sessions for the teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $classrooms = $this->teacherService->getAccessibleClassrooms();
        
        $sessions = ClassSession::whereIn('class_room_id', $classrooms->pluck('id'))
            ->with(['classRoom.school', 'attendances'])
            ->orderBy('session_date', 'desc')
            ->paginate(15);

        // Get unique schools from the classrooms
        $schools = $classrooms->pluck('school')->unique('id')->filter();

        return view('teacher.sessions.all', compact('sessions', 'classrooms', 'schools'));
    }

    /**
     * Show the form for creating a new session.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function create(ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);

        $students = $classroom->students;
        
        // Get schedule times for the classroom
        $times = $this->getScheduleTimes($classroom);
        $startTime = $times['start'];
        $endTime = $times['end'];
        
        return view('teacher.sessions.create', compact('classroom', 'students', 'startTime', 'endTime'));
    }

    /**
     * Get default schedule times for classroom
     *
     * @param ClassRoom $classroom
     * @return array
     */
    private function getScheduleTimes(ClassRoom $classroom): array
    {
        $currentDay = now()->dayOfWeek;
        $startTime = '08:00';
        $endTime = '09:00';
        
        // Find schedule for current day
        $schedule = $classroom->schedules()
            ->where('day', $currentDay)
            ->first();
        
        if ($schedule) {
            $startTime = $schedule->start_time->format('H:i');
            $endTime = $schedule->end_time->format('H:i');
        } else {
            // Try to find any schedule for this class
            $anySchedule = $classroom->schedules()->first();
            if ($anySchedule) {
                $startTime = $anySchedule->start_time->format('H:i');
                $endTime = $anySchedule->end_time->format('H:i');
            }
        }
        
        return ['start' => $startTime, 'end' => $endTime];
    }

    /**
     * Store a newly created session in storage.
     *
     * @param  \App\Http\Requests\StoreSessionRequest  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSessionRequest $request, ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);

        $session = $this->classSessionService->createSession($classroom, $request->validated());

        return redirect()->route('classroom.sessions.show', [$classroom, $session])
            ->with('success', 'تم إنشاء الجلسة بنجاح');
    }

    /**
     * Display the specified session.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function show(ClassRoom $classroom, ClassSession $session)
    {
        $this->authorize('view', $classroom);

        // Ensure session belongs to classroom
        if ($session->class_room_id != $classroom->id) {
            abort(404);
        }

        $session->load(['classRoom', 'attendances.student']);
        $students = $classroom->students;
        $attendances = $session->attendances()->pluck('status', 'student_id')->toArray();
        $notes = $session->attendances()->pluck('note', 'student_id')->toArray();

        return view('teacher.sessions.show', compact('classroom', 'session', 'students', 'attendances', 'notes'));
    }

    /**
     * Show the form for editing the specified session.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassRoom $classroom, ClassSession $session)
    {
        $this->authorize('update', $classroom);

        // Ensure session belongs to classroom
        if ($session->class_room_id != $classroom->id) {
            abort(404);
        }

        $students = $classroom->students;

        return view('teacher.sessions.edit', compact('classroom', 'session', 'students'));
    }

    /**
     * Update the specified session in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassRoom $classroom, ClassSession $session)
    {
        $this->authorize('update', $classroom);

        // Ensure session belongs to classroom
        if ($session->class_room_id != $classroom->id) {
            abort(404);
        }

        $validatedData = $request->validate([
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendance' => 'nullable|array',
            'attendance.*.status' => 'nullable|in:present,absent,late',
            'attendance.*.note' => 'nullable|string|max:255',
        ]);

        // Update session basic info
        $session->update([
            'description' => $validatedData['description'],
            'session_date' => $validatedData['session_date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
        ]);

        // Update attendance if provided
        if (!empty($validatedData['attendance'])) {
            $this->classSessionService->updateAttendance($session, $validatedData);
        }

        return redirect()->route('classroom.sessions.show', [$classroom, $session])
            ->with('success', 'تم تحديث الجلسة بنجاح');
    }

    /**
     * Remove the specified session from storage.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassRoom $classroom, ClassSession $session)
    {
        $this->authorize('delete', $classroom);

        // Ensure session belongs to classroom
        if ($session->class_room_id != $classroom->id) {
            abort(404);
        }

        // Store classroom ID before deleting session
        $classroomId = $classroom->id;
        
        // Delete the session
        $session->delete();

        // Redirect with explicit success message and classroom ID
        return redirect()
            ->route('classrooms.show', ['classroom' => $classroomId])
            ->with('success', 'تم حذف الجلسة بنجاح');
    }

    /**
     * Update attendance for a session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\ClassSession  $session
     * @return \Illuminate\Http\Response
     */
    public function updateAttendance(Request $request, ClassRoom $classroom, ClassSession $session)
    {
        $this->authorize('update', $classroom);

        // Ensure session belongs to classroom
        if ($session->class_room_id != $classroom->id) {
            abort(404);
        }

        $this->classSessionService->updateAttendance($session, $request->all());

        return redirect()->route('classroom.sessions.show', [$classroom, $session])
            ->with('success', 'تم تحديث الحضور بنجاح');
    }
}
