<?php

namespace App\Services;

use App\Models\User;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\MemorizationProgress;
use App\Models\Attendance;
use App\Services\PdfCredentialService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use App\Models\Message;

class TeacherService
{
    /**
     * Get dashboard statistics for teacher.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        // Get all classrooms accessible by the teacher
        $classRooms = $this->getAccessibleClassrooms();
        
        // Get total students across all accessible classrooms
        $totalStudents = Student::whereHas('classRooms', function ($query) use ($classRooms) {
            $query->whereIn('class_room_id', $classRooms->pluck('id'));
        })->distinct()->count();
        
        // Get recent sessions from accessible classrooms
        $recentSessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->with(['classRoom.school', 'classRoom.students', 'attendances'])
            ->orderBy('session_date', 'desc')
            ->limit(5)
            ->get();
        
        // Add attendance count to each session
        foreach ($recentSessions as $session) {
            $session->attendance_count = $session->attendances()->count();
        }
        
        // Get total sessions count
        $totalSessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))->count();
        
        // Get pending messages for accessible classrooms
        $pendingMessages = Message::whereIn('class_room_id', $classRooms->pluck('id'))
            ->whereNull('read_at')
            ->count();
        
        // Today's sessions from accessible classrooms
        $todaySessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->whereDate('session_date', today())
            ->with(['classRoom'])
            ->get();

        // Get schools count
        $teacherSchools = $this->getTeacherSchools();
        $schoolsCount = $teacherSchools['schools']->count();

        // Get pending schools
        $pendingSchools = $this->getPendingSchools();

        return [
            'classRooms' => $classRooms,
            'classrooms_count' => $classRooms->count(),
            'students_count' => $totalStudents,
            'sessions_count' => $totalSessions,
            'schools_count' => $schoolsCount,
            'totalStudents' => $totalStudents,
            'recentSessions' => $recentSessions,
            'recent_sessions' => $recentSessions,
            'pendingMessages' => $pendingMessages,
            'today_sessions' => $todaySessions,
            'pendingSchools' => $pendingSchools,
            'unread_messages' => $pendingMessages, // Alias for backward compatibility
        ];
    }
    
    /**
     * Get all classrooms accessible by the current teacher.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleClassrooms()
    {
        $teacherId = Auth::id();
        
        // Get schools where teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Get schools where teacher has created classrooms (legacy support)
        $classroomSchoolIds = ClassRoom::where('user_id', $teacherId)
            ->pluck('school_id')
            ->toArray();
        
        // Combine and get unique school IDs
        $allSchoolIds = array_unique(array_merge($approvedSchoolIds, $classroomSchoolIds));
        
        // Return all classrooms in these schools
        return ClassRoom::whereIn('school_id', $allSchoolIds)
            ->with(['school', 'schedules'])
            ->get();
    }
    
    /**
     * Get teacher's schools that they can access.
     *
     * @return array
     */
    public function getTeacherSchools(): array
    {
        $teacherId = Auth::id();
        
        // Get schools where teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Get schools where teacher has created classrooms (legacy support)  
        $classroomSchoolIds = ClassRoom::where('user_id', $teacherId)
            ->pluck('school_id')
            ->toArray();
        
        // Combine and get unique school IDs
        $allSchoolIds = array_unique(array_merge($approvedSchoolIds, $classroomSchoolIds));
        
        $schools = School::whereIn('id', $allSchoolIds)->get();
        
        return compact('schools');
    }
    
    /**
     * Join a school using the provided code.
     *
     * @param string $schoolCode
     * @return array
     */
    public function joinSchool(string $schoolCode): array
    {
        $school = School::where('code', $schoolCode)->first();
        
        if (!$school) {
            return [
                'success' => false,
                'message' => 'رمز المدرسة غير صحيح'
            ];
        }
        
        // Check if already associated
        $exists = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('school_id', $school->id)
            ->exists();
            
        if ($exists) {
            return [
                'success' => false,
                'message' => 'أنت مسجل بالفعل في هذه المدرسة'
            ];
        }
        
        // Add teacher to school (pending approval)
        DB::table('school_teacher')->insert([
            'user_id' => Auth::id(),
            'school_id' => $school->id,
            'is_approved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return [
            'success' => true,
            'message' => 'تم إرسال طلب الانضمام بنجاح. في انتظار موافقة مدير المدرسة.'
        ];
    }
    
    /**
     * Get schools data for teacher
     *
     * @return array
     */
    public function getSchools(): array
    {
        return $this->getTeacherSchools();
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
        $classrooms = $this->getAccessibleClassrooms();
        
        $query = Attendance::whereHas('session', function ($q) use ($classrooms) {
            $q->whereIn('class_room_id', $classrooms->pluck('id'));
        });
        
        if (!empty($filters['classroom_id'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('class_room_id', $filters['classroom_id']);
            });
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('session_date', '>=', $filters['date_from']);
            });
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('session_date', '<=', $filters['date_to']);
            });
        }
        
        $attendances = $query->with(['student', 'session.classRoom'])->get();
        
        return [
            'attendances' => $attendances,
            'classrooms' => $classrooms
        ];
    }
    
    /**
     * Get performance report data
     *
     * @param array $filters
     * @return array
     */
    public function getPerformanceReportData(array $filters): array
    {
        $classrooms = $this->getAccessibleClassrooms();
        
        // Get students from accessible classrooms
        $students = Student::whereHas('classRooms', function ($query) use ($classrooms) {
            $query->whereIn('class_room_id', $classrooms->pluck('id'));
        })->with(['classRooms' => function ($query) use ($classrooms) {
            $query->whereIn('class_room_id', $classrooms->pluck('id'));
        }])->get();
        
        return [
            'students' => $students,
            'classrooms' => $classrooms
        ];
    }
    
    /**
     * Check if teacher has access to a specific classroom.
     *
     * @param int $classroomId
     * @return bool
     */
    public function hasAccessToClassroom(int $classroomId): bool
    {
        $classroom = ClassRoom::find($classroomId);
        
        if (!$classroom) {
            return false;
        }
        
        $teacherId = Auth::id();
        
        // Check if teacher is approved for this school
        $isApprovedForSchool = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('school_id', $classroom->school_id)
            ->where('is_approved', true)
            ->exists();

        if ($isApprovedForSchool) {
            return true;
        }

        // Also check if teacher has any classroom in this school (legacy support)
        return ClassRoom::where('user_id', $teacherId)
            ->where('school_id', $classroom->school_id)
            ->exists();
    }
    
    /**
     * Get accessible classrooms for a specific school.
     *
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleClassroomsForSchool(int $schoolId)
    {
        if (!$this->hasAccessToSchool($schoolId)) {
            return collect();
        }
        
        return ClassRoom::where('school_id', $schoolId)
            ->with(['school', 'schedules'])
            ->get();
    }
    
    /**
     * Check if teacher has access to a specific school.
     *
     * @param int $schoolId
     * @return bool
     */
    public function hasAccessToSchool(int $schoolId): bool
    {
        $teacherId = Auth::id();
        
        // Check if teacher is approved for this school
        $isApprovedForSchool = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('school_id', $schoolId)
            ->where('is_approved', true)
            ->exists();

        if ($isApprovedForSchool) {
            return true;
        }

        // Also check if teacher has any classroom in this school (legacy support)
        return ClassRoom::where('user_id', $teacherId)
            ->where('school_id', $schoolId)
            ->exists();
    }
    
    /**
     * Generate PDF with student credentials and QR codes
     *
     * @param array $studentIds
     * @param int|null $classroomId
     * @return string
     */
    public function generateStudentCredentialsPdf(array $studentIds = [], int $classroomId = null): string
    {
        $teacherId = Auth::id();
        
        if ($classroomId) {
            // Generate for specific classroom
            $classroom = ClassRoom::where('user_id', $teacherId)
                ->findOrFail($classroomId);
            
            if (!empty($studentIds)) {
                // Get specific students from the classroom
                $students = $classroom->students()
                    ->whereIn('students.id', $studentIds)
                    ->get();
            } else {
                // Get all students from the classroom
                $students = $classroom->students;
            }
            
            $title = 'بيانات تسجيل الدخول - فصل ' . $classroom->name;
        } else {
            // Generate for selected students across all teacher's accessible classrooms
            $accessibleClassrooms = $this->getAccessibleClassrooms();
            $accessibleClassroomIds = $accessibleClassrooms->pluck('id')->toArray();
            $accessibleSchoolIds = $accessibleClassrooms->pluck('school_id')->unique()->toArray();
            
            $students = Student::whereIn('id', $studentIds)
                ->where(function ($query) use ($accessibleClassroomIds, $accessibleSchoolIds) {
                    // Student must be either in an accessible classroom OR in an accessible school
                    $query->whereHas('classRooms', function ($subQuery) use ($accessibleClassroomIds) {
                        $subQuery->whereIn('class_room_id', $accessibleClassroomIds);
                    })
                    ->orWhereIn('school_id', $accessibleSchoolIds);
                })
                ->get();
                
            $title = 'بيانات تسجيل الدخول للطلاب المختارين';
        }
        
        if ($students->isEmpty()) {
            throw new \Exception('لا توجد طلاب للإنشاء PDF');
        }
        
        $youtubeUrl = config('app.youtube_tutorial_url');
        $pdfService = new PdfCredentialService();
        
        return $pdfService->generateCredentialsPdf($students, $youtubeUrl, $title);
    }

    /**
     * Get pending schools for teacher.
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
} 