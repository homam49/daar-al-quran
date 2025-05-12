<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_date',
        'start_time',
        'end_time',
        'description',
        'class_room_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    /**
     * Get the attendance count for the session.
     *
     * @return int
     */
    public function getAttendanceCountAttribute()
    {
        return $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();
    }

    /**
     * Get the class that the session belongs to.
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    /**
     * Get the attendance records for the session.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get formatted session date.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        $date = $this->session_date;
        if ($date) {
            return $date->format('d-m-Y');
        }
        
        return '';
    }

    /**
     * Get formatted start time.
     *
     * @return string
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time : '';
    }

    /**
     * Get formatted end time.
     *
     * @return string
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time : '';
    }

    /**
     * Make sure end time is after start time when saving
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($session) {
            // Ensure end time is after start time
            if ($session->start_time && $session->end_time) {
                $startHour = (int)substr($session->start_time, 0, 2);
                $startMinute = (int)substr($session->start_time, 3, 2);
                
                $endHour = (int)substr($session->end_time, 0, 2);
                $endMinute = (int)substr($session->end_time, 3, 2);
                
                $startTime = $startHour * 60 + $startMinute;
                $endTime = $endHour * 60 + $endMinute;
                
                // If end time is before start time, add an hour to end time
                if ($endTime <= $startTime) {
                    $endTime = $startTime + 60;
                    $endHour = floor($endTime / 60);
                    $endMinute = $endTime % 60;
                    
                    if ($endHour >= 24) {
                        $endHour = $endHour - 24;
                    }
                    
                    $session->end_time = sprintf('%02d:%02d', $endHour, $endMinute);
                }
            }
        });
    }
}
