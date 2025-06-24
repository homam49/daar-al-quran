<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        // Teachers can only view their own classrooms
        if ($user->hasRole('teacher')) {
            return $user->id === $classRoom->user_id;
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
        // Teachers can only update their own classrooms
        if ($user->hasRole('teacher')) {
            return $user->id === $classRoom->user_id;
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
        // Teachers can only delete their own classrooms
        if ($user->hasRole('teacher')) {
            return $user->id === $classRoom->user_id;
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
}
