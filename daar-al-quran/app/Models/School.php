<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'code',
        'deletion_code',
        'admin_id'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            // Do not override admin-provided deletion code
            // $school->deletion_code = static::generateDeletionCode();
        });

        static::deleting(function ($school) {
            // Delete all associated classrooms (will cascade to sessions and attendance)
            $school->classRooms()->each(function ($classroom) {
                $classroom->delete();
            });

            // Delete all associated students (that are not in any other school)
            $school->students()->delete();
        });
    }

    /**
     * Generate a unique deletion code for the school.
     *
     * @return string
     */
    public static function generateDeletionCode()
    {
        return Str::random(8);
    }

    /**
     * Get the admin user that created the school.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the classes for the school.
     */
    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }

    /**
     * Get the students for the school.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the teachers assigned to this school.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'school_teacher', 'school_id', 'user_id')
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            });
    }
}
