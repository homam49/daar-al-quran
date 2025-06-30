<?php

namespace App\Services;

use App\Models\User;
use App\Models\School;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * Get dashboard statistics for admin
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $school = School::where('admin_id', Auth::id())->first();
        
        if (!$school) {
            return $this->getEmptyStats();
        }
        
        return [
            'school' => $school,
            'schools_count' => 1,
            'classrooms_count' => $school->classRooms()->count(),
            'teachers_count' => $this->getApprovedTeachersCount($school),
            'students_count' => $school->students()->count(),
            'pending_teachers' => $this->getPendingTeachers($school),
        ];
    }
    
    /**
     * Get empty stats when admin has no school
     *
     * @return array
     */
    private function getEmptyStats(): array
    {
        return [
            'school' => null,
            'schools_count' => 0,
            'classrooms_count' => 0,
            'teachers_count' => 0,
            'students_count' => 0,
            'pending_teachers' => [
                'system' => collect([]),
                'school' => collect([])
            ],
        ];
    }
    
    /**
     * Get count of approved teachers for a school
     *
     * @param School $school
     * @return int
     */
    private function getApprovedTeachersCount(School $school): int
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        
        return DB::table('school_teacher')
            ->join('users', 'users.id', '=', 'school_teacher.user_id')
            ->where('school_teacher.school_id', $school->id)
            ->where('users.role_id', $teacherRole->id)
            ->where('users.is_approved', true)
            ->where('school_teacher.is_approved', true)
            ->distinct('users.id')
            ->count('users.id');
    }
    
    /**
     * Get pending teacher approvals
     *
     * @param School $school
     * @return array
     */
    private function getPendingTeachers(School $school): array
    {
        $teacherRole = Role::where('name', 'teacher')->first();
        
        $schoolPendingTeachers = DB::table('school_teacher')
            ->join('users', 'users.id', '=', 'school_teacher.user_id')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('users.role_id', $teacherRole->id)
            ->where('users.is_approved', true)
            ->where('school_teacher.is_approved', false)
            ->where('schools.id', $school->id)
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.created_at as user_created_at',
                'schools.id as school_id',
                'schools.name as school_name',
                'school_teacher.created_at as joined_at'
            )
            ->get();
        
        return [
            'system' => collect([]),
            'school' => $schoolPendingTeachers
        ];
    }
    
    /**
     * Get teachers for admin's schools
     *
     * @return array
     */
    public function getTeachersData(): array
    {
        $schools = School::where('admin_id', Auth::id())->get();
        $schoolIds = $schools->pluck('id')->toArray();
        $teacherRole = Role::where('name', 'teacher')->first();
        
        $pendingTeachers = $this->getPendingTeachersForSchools($schoolIds, $teacherRole);
        $teachers = $this->getApprovedTeachersForSchools($schoolIds, $teacherRole);
        
        return compact('teachers', 'schools', 'pendingTeachers');
    }
    
    /**
     * Get pending teachers for multiple schools
     *
     * @param array $schoolIds
     * @param Role $teacherRole
     * @return \Illuminate\Support\Collection
     */
    private function getPendingTeachersForSchools(array $schoolIds, Role $teacherRole)
    {
        return DB::table('school_teacher')
            ->join('users', 'users.id', '=', 'school_teacher.user_id')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('users.role_id', $teacherRole->id)
            ->where('users.is_approved', true)
            ->where('school_teacher.is_approved', false)
            ->whereIn('school_teacher.school_id', $schoolIds)
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.created_at as user_created_at',
                'schools.id as school_id',
                'schools.name as school_name',
                'school_teacher.created_at as joined_at'
            )
            ->get();
    }
    
    /**
     * Get approved teachers for multiple schools
     *
     * @param array $schoolIds
     * @param Role $teacherRole
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getApprovedTeachersForSchools(array $schoolIds, Role $teacherRole)
    {
        $teachers = User::where('role_id', $teacherRole->id)
            ->whereHas('teacherSchools', function($query) use ($schoolIds) {
                $query->whereIn('schools.id', $schoolIds)
                      ->where('school_teacher.is_approved', true);
            })
            ->get();
        
        // Add school info to each teacher
        foreach ($teachers as $teacher) {
            $schoolData = $teacher->teacherSchools()
                ->whereIn('schools.id', $schoolIds)
                ->where('school_teacher.is_approved', true)
                ->first();
            
            if ($schoolData) {
                $teacher->school_name = $schoolData->name;
                $teacher->school_id = $schoolData->id;
            }
        }
        
        return $teachers;
    }
    
    /**
     * Approve teacher for school
     *
     * @param int $userId
     * @param int $schoolId
     * @return bool
     */
    public function approveTeacherForSchool(int $userId, int $schoolId): bool
    {
        // Verify school ownership
        $school = School::where('id', $schoolId)
            ->where('admin_id', Auth::id())
            ->first();
            
        if (!$school) {
            return false;
        }
        
        DB::table('school_teacher')
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->update([
                'is_approved' => true,
                'updated_at' => now()
            ]);
            
        return true;
    }
    
    /**
     * Reject teacher for school
     *
     * @param int $userId
     * @param int $schoolId
     * @return bool
     */
    public function rejectTeacherForSchool(int $userId, int $schoolId): bool
    {
        // Verify school ownership
        $school = School::where('id', $schoolId)
            ->where('admin_id', Auth::id())
            ->first();
            
        if (!$school) {
            return false;
        }
        
        DB::table('school_teacher')
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->delete();
            
        return true;
    }
    
    /**
     * Approve teacher in system
     *
     * @param User $user
     * @return void
     */
    public function approveTeacherInSystem(User $user): void
    {
        $user->update(['is_approved' => true]);
    }
    
    /**
     * Remove teacher from admin's schools only
     *
     * @param User $user
     * @return void
     */
    public function deleteTeacher(User $user): void
    {
        // Get admin's schools
        $adminSchoolIds = School::where('admin_id', Auth::id())->pluck('id');
        
        // Remove teacher from admin's schools only
        DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->whereIn('school_id', $adminSchoolIds)
            ->delete();
        
        // Note: We do NOT delete the user account - they should remain in the system
        // even if they're not part of any schools currently
    }
    
    /**
     * Get students for admin's schools
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudents()
    {
        $schools = School::where('admin_id', Auth::id())->get();
        $schoolIds = $schools->pluck('id');
        
        return Student::whereIn('school_id', $schoolIds)
            ->with('school')
            ->get();
    }
    
    /**
     * Delete student
     *
     * @param Student $student
     * @return bool
     */
    public function deleteStudent(Student $student): bool
    {
        // Verify student belongs to admin's school
        $school = School::where('id', $student->school_id)
            ->where('admin_id', Auth::id())
            ->first();
            
        if (!$school) {
            return false;
        }
        
        $student->delete();
        return true;
    }
    
    /**
     * Update user password
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
     * Update user name
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
     * Update username
     *
     * @param array $data
     * @return void
     */
    public function updateUsername(array $data): void
    {
        Auth::user()->update([
            'username' => $data['username']
        ]);
    }
    
    /**
     * Get classrooms for admin's schools
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClassrooms()
    {
        $schools = School::where('admin_id', Auth::id())->get();
        $schoolIds = $schools->pluck('id');
        
        return \App\Models\ClassRoom::whereIn('school_id', $schoolIds)
            ->with(['school', 'teacher', 'students'])
            ->get();
    }
} 