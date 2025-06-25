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
            'unread_messages' => $student->messages()->whereNull('read_at')->where('sender_type', '!=', 'student')->count(),
        ];
    }

    /**
     * Get attendance data for student view.
     *
     * @param Student $student
     * @param array $filters
     * @return array
     */
    public function getAttendanceData(Student $student, array $filters = []): array
    {
        $query = $student->attendances()->with(['classSession.classRoom']);
        
        // Filter by classroom if provided
        if (!empty($filters['classroom_id'])) {
            $query->whereHas('classSession', function($q) use ($filters) {
                $q->where('classroom_id', $filters['classroom_id']);
            });
        }
        
        // Filter by month if provided
        if (!empty($filters['month'])) {
            $query->whereMonth('created_at', $filters['month']);
        }
        
        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }
        
        $attendances = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics for all attendances
        $allAttendances = $student->attendances;
        $present_count = $allAttendances->where('status', 'present')->count();
        $late_count = $allAttendances->where('status', 'late')->count();
        $absent_count = $allAttendances->where('status', 'absent')->count();
        $total_count = $allAttendances->count();
        
        $attendance_percentage = $total_count > 0 
            ? round((($present_count + $late_count) / $total_count) * 100, 1) 
            : 0;
        
        // Months for filter
        $months = [
            '1' => 'يناير', '2' => 'فبراير', '3' => 'مارس', '4' => 'أبريل',
            '5' => 'مايو', '6' => 'يونيو', '7' => 'يوليو', '8' => 'أغسطس',
            '9' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر'
        ];
        
        return [
            'student' => $student,
            'attendances' => $attendances,
            'present_count' => $present_count,
            'late_count' => $late_count,
            'absent_count' => $absent_count,
            'attendance_percentage' => $attendance_percentage,
            'classrooms' => $student->classRooms,
            'months' => $months
        ];
    }
} 