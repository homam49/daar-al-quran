<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class ClassRoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('teacher') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ClassRoom $classRoom)
    {
        // Teachers can view classrooms in schools they belong to
        if ($user->hasRole('teacher')) {
            return $this->teacherBelongsToClassroomSchool($user, $classRoom);
        }
        
        // Admins can view any classroom
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasRole('teacher') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ClassRoom $classRoom)
    {
        // Teachers can update classrooms in schools they belong to
        if ($user->hasRole('teacher')) {
            return $this->teacherBelongsToClassroomSchool($user, $classRoom);
        }
        
        // Admins can update any classroom
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ClassRoom $classRoom)
    {
        // Teachers can delete classrooms in schools they belong to
        if ($user->hasRole('teacher')) {
            return $this->teacherBelongsToClassroomSchool($user, $classRoom);
        }
        
        // Admins can delete any classroom
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ClassRoom $classRoom)
    {
        return $this->delete($user, $classRoom);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ClassRoom $classRoom)
    {
        return $user->hasRole('admin');
    }

    /**
     * Check if teacher belongs to the school of the classroom.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClassRoom  $classRoom
     * @return bool
     */
    private function teacherBelongsToClassroomSchool(User $user, ClassRoom $classRoom): bool
    {
        // Check if teacher is approved for this school through school_teacher table
        $isApprovedForSchool = DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->where('school_id', $classRoom->school_id)
            ->where('is_approved', true)
            ->exists();

        if ($isApprovedForSchool) {
            return true;
        }

        // Also check if teacher has any classroom in this school (legacy support)
        $hasClassroomInSchool = \App\Models\ClassRoom::where('user_id', $user->id)
            ->where('school_id', $classRoom->school_id)
            ->exists();

        return $hasClassroomInSchool;
    }
}
