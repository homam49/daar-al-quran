<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\StudentClassRoom;
use Illuminate\Support\Str;

class StudentService
{
    /**
     * Create a new student and add to classroom.
     *
     * @param array $data
     * @param ClassRoom $classroom
     * @return Student
     */
    public function createStudent(array $data, ClassRoom $classroom): Student
    {
        // Generate random credentials
        $credential = strtoupper(Str::random(6));
        
        // Create the student
        $student = Student::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'birth_year' => $data['birth_year'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
            'username' => $credential,
            'password' => $credential,
            'school_id' => $classroom->school_id,
            'first_login' => true,
        ]);

        // Add to classroom
        $this->addStudentToClassroom($student, $classroom);

        return $student;
    }

    /**
     * Add existing student to classroom.
     *
     * @param Student $student
     * @param ClassRoom $classroom
     * @return void
     */
    public function addStudentToClassroom(Student $student, ClassRoom $classroom): void
    {
        StudentClassRoom::create([
            'student_id' => $student->id,
            'class_room_id' => $classroom->id,
        ]);
    }

    /**
     * Update student information.
     *
     * @param Student $student
     * @param array $data
     * @return Student
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $student->update([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'birth_year' => $data['birth_year'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        return $student->fresh();
    }

    /**
     * Remove student from classroom.
     *
     * @param Student $student
     * @param ClassRoom $classroom
     * @return void
     */
    public function removeStudentFromClassroom(Student $student, ClassRoom $classroom): void
    {
        StudentClassRoom::where('student_id', $student->id)
            ->where('class_room_id', $classroom->id)
            ->delete();
    }

    /**
     * Get students available to add to a classroom.
     *
     * @param ClassRoom $classroom
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableStudents(ClassRoom $classroom)
    {
        return Student::where('school_id', $classroom->school_id)
            ->whereDoesntHave('classRooms', function ($query) use ($classroom) {
                $query->where('class_rooms.id', $classroom->id);
            })
            ->get();
    }

    /**
     * Get student dashboard statistics.
     *
     * @param Student $student
     * @return array
     */
    public function getDashboardStats(Student $student): array
    {
        $attendances = $student->attendances;
        $attendance_count = $attendances->whereIn('status', ['present', 'late'])->count();
        $total_attendances = $attendances->count();
        
        return [
            'classroom_count' => $student->classRooms->count(),
            'attendance_count' => $attendance_count,
            'present_count' => $attendances->where('status', 'present')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'absent_count' => $attendances->where('status', 'absent')->count(),
            'attendance_percentage' => $total_attendances > 0 
                ? round(($attendance_count / $total_attendances) * 100) 
                : 0,
            'unread_messages' => $student->messages()->whereNull('read_at')->count(),
        ];
    }
} 