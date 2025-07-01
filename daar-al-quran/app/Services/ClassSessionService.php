<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Attendance;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ClassSessionService
{
    protected $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Get session creation data for classroom
     *
     * @param int $classroomId
     * @return array
     */
    public function getSessionCreationData(int $classroomId): array
    {
        $classroom = ClassRoom::with('students')->findOrFail($classroomId);
        
        // Verify ownership
        if ($classroom->user_id != Auth::id()) {
            throw new \Exception('غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        // Get schedule times
        $times = $this->getScheduleTimes($classroom);
        
        return [
            'classroom' => $classroom,
            'startTime' => $times['start'],
            'endTime' => $times['end']
        ];
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
     * Create a new session with attendance
     *
     * @param ClassRoom $classroom
     * @param array $data
     * @return ClassSession
     */
    public function createSession(ClassRoom $classroom, array $data): ClassSession
    {
        // Authorization is handled by the controller using policies
        
        // Ensure end time is after start time
        $times = $this->validateAndFixTimes($data['start_time'], $data['end_time']);
        
        // Create the session
        $session = ClassSession::create([
            'session_date' => $data['session_date'],
            'start_time' => $times['start'],
            'end_time' => $times['end'],
            'description' => $data['description'] ?? null,
            'class_room_id' => $classroom->id,
        ]);
        
        // Record attendance if provided
        if (!empty($data['attendance'])) {
            $this->recordAttendance($session, $data['attendance'], $data['notes'] ?? []);
        }
        
        // Send class message if requested
        if (!empty($data['send_message']) && !empty($data['message_title']) && !empty($data['message_content'])) {
            $this->sendClassMessage($classroom, $data['message_title'], $data['message_content']);
        }
        
        return $session;
    }
    
    /**
     * Validate and fix session times
     *
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    private function validateAndFixTimes(string $startTime, string $endTime): array
    {
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
        
        return ['start' => $startTime, 'end' => $endTime];
    }
    
    /**
     * Record attendance for session
     *
     * @param ClassSession $session
     * @param array $attendanceData
     * @param array $notes
     * @return void
     */
    private function recordAttendance(ClassSession $session, array $attendanceData, array $notes = []): void
    {
        foreach ($attendanceData as $studentId => $status) {
            Attendance::create([
                'status' => $status,
                'note' => $notes[$studentId] ?? null,
                'student_id' => $studentId,
                'class_session_id' => $session->id,
            ]);
        }
    }
    
    /**
     * Update attendance for a session
     *
     * @param ClassSession $session
     * @param array $data
     * @return void
     */
    public function updateAttendance(ClassSession $session, array $data): void
    {
        // Authorization is handled by the controller using policies
        
        $attendanceData = $data['attendance'] ?? [];
        $notes = $data['notes'] ?? [];
        
        foreach ($attendanceData as $studentId => $attendanceInfo) {
            // Handle nested structure from edit form: attendance[student_id][status] and attendance[student_id][note]
            if (is_array($attendanceInfo)) {
                $status = $attendanceInfo['status'] ?? null;
                $note = $attendanceInfo['note'] ?? null;
            } else {
                // Handle flat structure from session creation: attendance[student_id] = status
                $status = $attendanceInfo;
                $note = $notes[$studentId] ?? null;
            }
            
            if ($status) {
                Attendance::updateOrCreate(
                    [
                        'class_session_id' => $session->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $status,
                        'note' => $note,
                    ]
                );
            }
        }
    }
    
    /**
     * Send message to entire class
     *
     * @param ClassRoom $classroom
     * @param string $title
     * @param string $content
     * @return void
     */
    private function sendClassMessage(ClassRoom $classroom, string $title, string $content): void
    {
        foreach ($classroom->students as $student) {
            Message::create([
                'subject' => $title,
                'content' => $content,
                'sender_id' => Auth::id(),
                'sender_type' => 'teacher',
                'student_id' => $student->id,
                'class_room_id' => $classroom->id,
                'type' => 'class',
                'is_read' => false,
            ]);
        }
    }
    
    /**
     * Update session with new data
     *
     * @param int $classroomId
     * @param int $sessionId
     * @param array $data
     * @return ClassSession
     */
    public function updateSession(int $classroomId, int $sessionId, array $data): ClassSession
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Verify ownership
        if ($classroom->user_id != Auth::id()) {
            throw new \Exception('غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            throw new \Exception('هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        // Validate times
        $times = $this->validateAndFixTimes($data['start_time'], $data['end_time']);
        
        // Update session
        $session->update([
            'session_date' => $data['session_date'],
            'start_time' => $times['start'],
            'end_time' => $times['end'],
            'description' => $data['description'] ?? null,
        ]);
        
        // Update attendance
        $this->updateAttendance($session, $data);
        
        return $session;
    }
    
    /**
     * Delete session and cleanup
     *
     * @param int $classroomId
     * @param int $sessionId
     * @return void
     */
    public function deleteSession(int $classroomId, int $sessionId): void
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Verify ownership
        if ($classroom->user_id != Auth::id()) {
            throw new \Exception('غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $session = ClassSession::findOrFail($sessionId);
        
        if ($session->class_room_id != $classroomId) {
            throw new \Exception('هذه الجلسة لا تنتمي إلى هذا الفصل');
        }
        
        // Delete attendance records first
        $session->attendances()->delete();
        
        // Delete the session
        $session->delete();
    }
    
    /**
     * Get all sessions for teacher's accessible classrooms
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTeacherSessions()
    {
        $classrooms = $this->teacherService->getAccessibleClassrooms();
        
        return ClassSession::whereIn('class_room_id', $classrooms->pluck('id'))
            ->with(['classRoom.school', 'attendances'])
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
    }
} 