<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject',
        'content',
        'type',
        'sender_id',
        'sender_type',
        'student_id',
        'recipient_id',
        'class_room_id',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the sender user (teacher) of the message.
     */
    public function sender()
    {
        if ($this->sender_type === 'student') {
            return $this->belongsTo(Student::class, 'sender_id');
        }
        
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the student recipient of the message (for personal messages).
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the teacher recipient of the message (when sent by student).
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the class that the message is for (for class announcements).
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}
