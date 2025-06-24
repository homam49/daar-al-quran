<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\StudentVerifyEmail;

class Student extends Authenticatable implements CanResetPasswordContract, MustVerifyEmail
{
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'birth_year',
        'phone',
        'address',
        'email',
        'password',
        'school_id',
        'username',
        'first_login',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'first_login' => 'boolean',
        'email' => 'string',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute()
    {
        return $this->middle_name
            ? "{$this->first_name} {$this->middle_name} {$this->last_name}"
            : "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the age of the student.
     */
    public function getAgeAttribute()
    {
        return date('Y') - $this->birth_year;
    }

    /**
     * Get the school that the student belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the classes for the student.
     */
    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'student_class_rooms');
    }

    /**
     * Get the attendance records for the student.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the personal messages for the student.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the memorization progress records for the student.
     */
    public function memorizationProgress()
    {
        return $this->hasMany(MemorizationProgress::class);
    }

    /**
     * Set the student's password.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\StudentResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new StudentVerifyEmail($this));
    }
}
