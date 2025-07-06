<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'school_id',
        'user_id',
    ];

    /**
     * Get the school that the class belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the teacher who created the class.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (teacher) who created the class.
     * This is an alias for the teacher() relationship to maintain backward compatibility.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the students for the class.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_class_rooms');
    }

    /**
     * Get the schedules for the class.
     */
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * Get the sessions for the class.
     */
    public function sessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    /**
     * Get the announcements for the class.
     */
    public function announcements()
    {
        return $this->hasMany(Message::class)->where('type', 'class');
    }

    /**
     * Get the teachers who have access to this classroom.
     */
    public function authorizedTeachers()
    {
        return $this->belongsToMany(User::class, 'teacher_classroom_access', 'classroom_id', 'teacher_id')
            ->withPivot('granted_by', 'granted_at')
            ->withTimestamps();
    }

    /**
     * Get the access records for this classroom.
     */
    public function accessRecords()
    {
        return $this->hasMany(TeacherClassroomAccess::class, 'classroom_id');
    }

    /**
     * Check if a teacher has access to this classroom.
     */
    public function hasTeacherAccess(int $teacherId): bool
    {
        return $this->authorizedTeachers()->where('teacher_id', $teacherId)->exists();
    }
}
