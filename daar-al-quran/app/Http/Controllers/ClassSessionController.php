<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
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
     * Display a listing of the sessions for a specific classroom.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function index(ClassRoom $classroom)
    {
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

        $sessions = $classroom->sessions()->orderBy('session_date', 'desc')->get();
        
        return view('teacher.sessions.index', compact('classroom', 'sessions'));
    }

    /**
     * Show the form for creating a new session.
     *
     * @param  int  $classroomId
     * @return \Illuminate\Http\Response
     */
    public function create($classroomId)
    {
        $classroom = ClassRoom::with('students')->findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        // Get the current day of the week (0 = Sunday, 1 = Monday, etc.)
        $currentDay = now()->dayOfWeek;
        
        // Find a schedule for the current day
        $schedule = $classroom->schedules()
            ->where('day', $currentDay)
            ->first();
        
        // Default start and end times if no schedule is found
        $startTime = '08:00';
        $endTime = '09:00';
        
        // If a schedule is found for today, use its times
        if ($schedule) {
            $startTime = $schedule->start_time->format('H:i');
            $endTime = $schedule->end_time->format('H:i');
        } else {
            // If no schedule for today, try to find any schedule for this class
            $anySchedule = $classroom->schedules()->first();
            if ($anySchedule) {
                $startTime = $anySchedule->start_time->format('H:i');
                $endTime = $anySchedule->end_time->format('H:i');
            }
        }
        
        return view('teacher.sessions.create', compact('classroom', 'startTime', 'endTime'));
    }

    /**
     * Store a newly created session in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $classroomId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $request->validate([
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late',
            'notes' => 'nullable|array',
        ]);
        
        // Ensure end time is after start time
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        $startHour = (int)substr($startTime, 0, 2);
        $startMinute = (int)substr($startTime, 3, 2);
        $startTimeMinutes = $startHour * 60 + $startMinute;
        
        $endHour = (int)substr($endTime, 0, 2);
        $endMinute = (int)substr($endTime, 3, 2);
        $endTimeMinutes = $endHour * 60 + $endMinute;
        
        // If end time is before or equal to start time, add an hour
        if ($endTimeMinutes <= $startTimeMinutes) {
            $endTimeMinutes = $startTimeMinutes + 60;
            $endHour = floor($endTimeMinutes / 60);
            $endMinute = $endTimeMinutes % 60;
            
            if ($endHour >= 24) {
                $endHour = $endHour - 24;
            }
            
            $endTime = sprintf('%02d:%02d', $endHour, $endMinute);
        }
        
        // Create the session
        $session = ClassSession::create([
            'session_date' => $request->session_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'description' => $request->description,
            'class_room_id' => $classroom->id,
        ]);
        
        // Record attendance for each student
        foreach ($request->attendance as $studentId => $status) {
            Attendance::create([
                'status' => $status,
                'note' => $request->notes[$studentId] ?? null,
                'student_id' => $studentId,
                'class_session_id' => $session->id,
            ]);
        }
        
        // Check if we need to send a class-wide message
        if ($request->filled('send_message') && $request->filled('message_title') && $request->filled('message_content')) {
            Message::create([
                'title' => $request->message_title,
                'content' => $request->message_content,
                'sender_id' => Auth::id(),
                'class_room_id' => $classroom->id,
                'type' => 'class',
            ]);
        }
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم إنشاء الجلسة وتسجيل الحضور بنجاح');
    }

    /**
     * Display the specified session.
     *
     * @param  int  $classroomId
     * @param  int  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function show($classroomId, $sessionId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::with(['attendances.student'])->findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            return back()->with('error', 'هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        return view('teacher.sessions.show', compact('classroom', 'session'));
    }

    /**
     * Show the form for editing the specified session.
     *
     * @param  int  $classroomId
     * @param  int  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function edit($classroomId, $sessionId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::with(['attendances.student'])->findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            return back()->with('error', 'هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        return view('teacher.sessions.edit', compact('classroom', 'session'));
    }

    /**
     * Update the specified session in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @param  int  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $classroomId, $sessionId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            return back()->with('error', 'هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        $request->validate([
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.note' => 'nullable|string',
        ]);
        
        // Ensure end time is after start time
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        $startHour = (int)substr($startTime, 0, 2);
        $startMinute = (int)substr($startTime, 3, 2);
        $startTimeMinutes = $startHour * 60 + $startMinute;
        
        $endHour = (int)substr($endTime, 0, 2);
        $endMinute = (int)substr($endTime, 3, 2);
        $endTimeMinutes = $endHour * 60 + $endMinute;
        
        // If end time is before or equal to start time, add an hour
        if ($endTimeMinutes <= $startTimeMinutes) {
            $endTimeMinutes = $startTimeMinutes + 60;
            $endHour = floor($endTimeMinutes / 60);
            $endMinute = $endTimeMinutes % 60;
            
            if ($endHour >= 24) {
                $endHour = $endHour - 24;
            }
            
            $endTime = sprintf('%02d:%02d', $endHour, $endMinute);
        }
        
        // Update the session
        $session->update([
            'session_date' => $request->session_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'description' => $request->description,
        ]);
        
        // Update attendance for each student
        foreach ($request->attendance as $studentId => $data) {
            $attendance = Attendance::where('class_session_id', $session->id)
                ->where('student_id', $studentId)
                ->first();
                
            if ($attendance) {
                $attendance->update([
                    'status' => $data['status'],
                    'note' => $data['note'] ?? null,
                ]);
            } else {
                Attendance::create([
                    'status' => $data['status'],
                    'note' => $data['note'] ?? null,
                    'student_id' => $studentId,
                    'class_session_id' => $session->id,
                ]);
            }
        }
        
        return redirect()->route('classroom.sessions.show', [$classroom->id, $session->id])
            ->with('success', 'تم تحديث الحضور بنجاح');
    }

    /**
     * Remove the specified session from storage.
     *
     * @param  int  $classroomId
     * @param  int  $sessionId
     * @return \Illuminate\Http\Response
     */
    public function destroy($classroomId, $sessionId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            return back()->with('error', 'هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        // Delete the session (this will also delete related attendance records via foreign keys)
        $session->delete();
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم حذف الجلسة بنجاح');
    }

    /**
     * Display a listing of all sessions for a teacher across all classrooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function allSessions()
    {
        $teacher = Auth::user();
        
        // Get all classrooms owned by this teacher
        $classrooms = ClassRoom::where('user_id', $teacher->id)->with('school')->get();
        
        // Get all sessions from these classrooms with pagination
        $sessions = ClassSession::whereIn('class_room_id', $classrooms->pluck('id'))
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->with(['classroom.school', 'attendances'])
            ->paginate(15);
        
        // Get unique schools from the classrooms for the filter
        $schools = $classrooms->pluck('school')->unique('id')->sortBy('name');
        
        return view('teacher.sessions.all', compact('sessions', 'classrooms', 'schools'));
    }
}
