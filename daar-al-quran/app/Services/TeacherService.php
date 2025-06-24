<?php

namespace App\Services;

use App\Models\User;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\MemorizationProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherService
{
    /**
     * Get dashboard statistics for teacher
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $classRooms = ClassRoom::where('user_id', Auth::id())->get();
        $schoolIds = $classRooms->pluck('school_id')->unique();
        
        // Get approved schools count
        $approvedSchools = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        $schools_count = count(array_unique(array_merge($schoolIds->toArray(), $approvedSchools)));
        
        // Get pending schools
        $pendingSchools = $this->getPendingSchools();
        
        // Get statistics
        $stats = [
            'schools_count' => $schools_count,
            'classrooms_count' => $classRooms->count(),
            'students_count' => $this->getUniqueStudentsCount($classRooms),
            'sessions_count' => $this->getSessionsCount($classRooms),
            'memorization_stats' => $this->getMemorizationStats($classRooms),
            'today_sessions' => $this->getTodaySessions($classRooms),
            'recent_sessions' => $this->getRecentSessions($classRooms),
            'classRooms' => $classRooms,
            'pendingSchools' => $pendingSchools,
        ];
        
        return $stats;
    }
    
    /**
     * Get pending schools for teacher
     *
     * @return \Illuminate\Support\Collection
     */
    private function getPendingSchools()
    {
        return DB::table('school_teacher')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('school_teacher.user_id', Auth::id())
            ->where('school_teacher.is_approved', false)
            ->select('schools.id', 'schools.name')
            ->get();
    }
    
    /**
     * Get unique students count across classrooms
     *
     * @param \Illuminate\Database\Eloquent\Collection $classRooms
     * @return int
     */
    private function getUniqueStudentsCount($classRooms): int
    {
        return Student::whereHas('classRooms', function($query) use ($classRooms) {
            $query->whereIn('class_rooms.id', $classRooms->pluck('id'));
        })->distinct('id')->count();
    }
    
    /**
     * Get sessions count for classrooms
     *
     * @param \Illuminate\Database\Eloquent\Collection $classRooms
     * @return int
     */
    private function getSessionsCount($classRooms): int
    {
        return ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))->count();
    }
    
    /**
     * Get memorization statistics
     *
     * @param \Illuminate\Database\Eloquent\Collection $classRooms
     * @return array
     */
    private function getMemorizationStats($classRooms): array
    {
        $teacherStudentIds = Student::whereHas('classRooms', function($query) use ($classRooms) {
            $query->whereIn('class_rooms.id', $classRooms->pluck('id'));
        })->pluck('id');
        
        $memorization_records = MemorizationProgress::whereIn('student_id', $teacherStudentIds)->get();
        
        return [
            'total_memorized' => $memorization_records->where('status', 'memorized')->count(),
            'in_progress' => $memorization_records->where('status', 'in_progress')->count(),
            'total_students_tracking' => $memorization_records->unique('student_id')->count(),
            'pages_memorized' => $memorization_records->where('type', 'page')->where('status', 'memorized')->count(),
            'surahs_memorized' => $memorization_records->where('type', 'surah')->where('status', 'memorized')->count(),
            'pages_in_progress' => $memorization_records->where('type', 'page')->where('status', 'in_progress')->count(),
            'surahs_in_progress' => $memorization_records->where('type', 'surah')->where('status', 'in_progress')->count(),
            'total_content_items' => $memorization_records->count(),
        ];
    }
    
    /**
     * Get today's sessions
     *
     * @param \Illuminate\Database\Eloquent\Collection $classRooms
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTodaySessions($classRooms)
    {
        $today = now()->format('Y-m-d');
        
        $sessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->whereDate('session_date', $today)
            ->orderBy('start_time')
            ->with(['classRoom.school'])
            ->get();
        
        // Ensure classRoom relationship exists
        foreach ($sessions as $session) {
            if (!$session->classRoom) {
                $session->classRoom = ClassRoom::find($session->class_room_id);
            }
        }
        
        return $sessions;
    }
    
    /**
     * Get recent sessions
     *
     * @param \Illuminate\Database\Eloquent\Collection $classRooms
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentSessions($classRooms)
    {
        $sessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(4)
            ->with(['classRoom.school'])
            ->get();
        
        // Ensure classRoom relationship exists and add attendance count
        foreach ($sessions as $session) {
            if (!$session->classRoom) {
                $session->classRoom = ClassRoom::find($session->class_room_id);
            }
            $session->attendance_count = $session->attendances()->count();
        }
        
        return $sessions;
    }
    
    /**
     * Join a school using school code
     *
     * @param string $schoolCode
     * @return array
     */
    public function joinSchool(string $schoolCode): array
    {
        $school = School::where('code', $schoolCode)->first();
        
        if (!$school) {
            return ['success' => false, 'message' => 'رمز المدرسة غير صحيح'];
        }
        
        $user = Auth::user();
        
        // Check if teacher is already associated with this school
        $alreadyJoined = DB::table('school_teacher')
            ->where('school_id', $school->id)
            ->where('user_id', $user->id)
            ->exists();
        
        if (!$alreadyJoined) {
            DB::table('school_teacher')->insert([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'is_approved' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Store in session
        session(['pending_school_id' => $school->id]);
        session(['pending_school_name' => $school->name]);
        
        return [
            'success' => true, 
            'message' => 'تم تقديم طلب الانضمام إلى المدرسة بنجاح. يرجى انتظار موافقة مدير المدرسة.'
        ];
    }
    
    /**
     * Get schools data for teacher
     *
     * @return array
     */
    public function getSchoolsData(): array
    {
        // Get schools where teacher has classrooms
        $classRooms = ClassRoom::where('user_id', Auth::id())->get();
        $schoolIds = $classRooms->pluck('school_id')->unique();
        
        // Get schools where teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Combine and get unique school IDs
        $allSchoolIds = collect($schoolIds)->merge($approvedSchoolIds)->unique();
        
        $schools = School::whereIn('id', $allSchoolIds)->get();
        
        return compact('schools');
    }
    
    /**
     * Update teacher password
     *
     * @param array $data
     * @return void
     */
    public function updatePassword(array $data): void
    {
        Auth::user()->update([
            'password' => Hash::make($data['password'])
        ]);
    }
    
    /**
     * Update teacher name
     *
     * @param array $data
     * @return void
     */
    public function updateName(array $data): void
    {
        Auth::user()->update([
            'name' => $data['name']
        ]);
    }
    
    /**
     * Get attendance report data
     *
     * @param array $filters
     * @return array
     */
    public function getAttendanceReportData(array $filters): array
    {
        $classrooms = ClassRoom::where('user_id', Auth::id())->get();
        $query = ClassSession::whereIn('class_room_id', $classrooms->pluck('id'))
            ->with(['attendances.student', 'classRoom']);
        
        // Apply filters
        if (!empty($filters['classroom_id'])) {
            $query->where('class_room_id', $filters['classroom_id']);
        }
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('session_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->whereDate('session_date', '<=', $filters['end_date']);
        }
        
        $sessions = $query->orderBy('session_date', 'desc')->get();
        
        return compact('sessions', 'classrooms');
    }
    
    /**
     * Get performance report data
     *
     * @param array $filters
     * @return array
     */
    public function getPerformanceReportData(array $filters): array
    {
        $classrooms = ClassRoom::where('user_id', Auth::id())->get();
        $classroomIds = $classrooms->pluck('id');
        
        $studentsQuery = Student::whereHas('classRooms', function($query) use ($classroomIds) {
            $query->whereIn('class_rooms.id', $classroomIds);
        })->with(['attendances.classSession', 'classRooms']);
        
        // Apply classroom filter
        if (!empty($filters['classroom_id'])) {
            $studentsQuery->whereHas('classRooms', function($query) use ($filters) {
                $query->where('class_rooms.id', $filters['classroom_id']);
            });
        }
        
        $students = $studentsQuery->get();
        
        // Calculate performance metrics for each student
        foreach ($students as $student) {
            $attendances = $student->attendances;
            
            // Apply date filters to attendances
            if (!empty($filters['start_date'])) {
                $attendances = $attendances->filter(function($attendance) use ($filters) {
                    return $attendance->classSession->session_date >= $filters['start_date'];
                });
            }
            
            if (!empty($filters['end_date'])) {
                $attendances = $attendances->filter(function($attendance) use ($filters) {
                    return $attendance->classSession->session_date <= $filters['end_date'];
                });
            }
            
            $totalSessions = $attendances->count();
            $presentCount = $attendances->where('status', 'present')->count();
            $lateCount = $attendances->where('status', 'late')->count();
            
            $student->attendance_rate = $totalSessions > 0 
                ? round((($presentCount + $lateCount) / $totalSessions) * 100, 1) 
                : 0;
            $student->total_sessions = $totalSessions;
            $student->present_count = $presentCount;
            $student->late_count = $lateCount;
            $student->absent_count = $attendances->where('status', 'absent')->count();
        }
        
        return compact('students', 'classrooms');
    }
} 