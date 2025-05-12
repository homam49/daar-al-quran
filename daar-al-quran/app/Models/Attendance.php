<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'note',
        'student_id',
        'class_session_id',
    ];

    /**
     * Get the student that the attendance belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the session that the attendance belongs to.
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class);
    }
}
