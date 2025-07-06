<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherClassroomAccess extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'teacher_classroom_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'granted_by',
        'granted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'granted_at' => 'datetime',
    ];

    /**
     * Get the teacher that this access belongs to.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the classroom that this access belongs to.
     */
    public function classroom()
    {
        return $this->belongsTo(ClassRoom::class, 'classroom_id');
    }

    /**
     * Get the admin who granted this access.
     */
    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
