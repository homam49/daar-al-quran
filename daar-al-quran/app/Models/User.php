<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'role_id',
        'is_approved',
        'school_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the schools created by the admin user.
     */
    public function schools()
    {
        return $this->hasMany(School::class);
    }

    /**
     * Get the school that the teacher belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the classes created by the teacher user.
     */
    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }

    /**
     * Check if the user is a moderator.
     */
    public function isModerator()
    {
        return $this->role->name === 'moderator';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher()
    {
        return $this->role->name === 'teacher';
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Get the schools the teacher is assigned to.
     */
    public function teacherSchools()
    {
        return $this->belongsToMany(School::class, 'school_teacher', 'user_id', 'school_id')->withTimestamps();
    }

    /**
     * Get the schools managed by the admin.
     */
    public function adminSchools()
    {
        return $this->hasMany(School::class, 'admin_id');
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }
}
