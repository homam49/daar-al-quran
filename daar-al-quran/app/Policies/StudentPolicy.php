<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the student's memorization progress.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Student $student)
    {
        // Check if user is a teacher
        if (!$user->hasRole('teacher')) {
            return false;
        }

        // Check if teacher has access to this student through any classroom in schools they belong to
        $teacherId = $user->id;
        
        // Get schools where teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', $teacherId)
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Get schools where teacher has created classrooms (legacy support)
        $classroomSchoolIds = \App\Models\ClassRoom::where('user_id', $teacherId)
            ->pluck('school_id')
            ->toArray();
        
        // Combine and get unique school IDs
        $allSchoolIds = array_unique(array_merge($approvedSchoolIds, $classroomSchoolIds));
        
        // Get all classrooms in these schools
        $accessibleClassroomIds = \App\Models\ClassRoom::whereIn('school_id', $allSchoolIds)
            ->pluck('id')
            ->toArray();
        
        return $student->classRooms()->whereIn('class_room_id', $accessibleClassroomIds)->exists();
    }

    /**
     * Determine whether the user can view the student's memorization progress.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Student $student)
    {
        return $this->update($user, $student);
    }
} 