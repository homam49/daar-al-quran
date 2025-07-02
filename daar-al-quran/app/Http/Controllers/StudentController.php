<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\StudentClassRoom;
use App\Models\Message;
use App\Services\StudentService;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected $studentService;

    /**
     * Create a new controller instance.
     */
    public function __construct(StudentService $studentService)
    {
        $this->middleware(['auth'])->except(['dashboard', 'attendance', 'messages', 'viewMessage', 'classrooms', 'markMessageRead', 'composeMessage', 'sendMessage']);
        $this->middleware(['auth:student'])->only(['dashboard', 'attendance', 'messages', 'viewMessage', 'classrooms', 'markMessageRead', 'composeMessage', 'sendMessage']);
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of students for a specific classroom.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function index(ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);

        $students = $classroom->students;
        
        return view('teacher.students.index', compact('classroom', 'students'));
    }

    /**
     * Show the form for creating a new student.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function create(ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);

        $existingStudents = $this->studentService->getAvailableStudents($classroom);

        return view('teacher.students.create', compact('classroom', 'existingStudents'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \App\Http\Requests\StoreStudentRequest  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentRequest $request, ClassRoom $classroom)
    {
        try {
        if ($request->has('existing_student_id')) {
                // Add existing student to classroom
            $student = Student::findOrFail($request->existing_student_id);
                $this->studentService->addStudentToClassroom($student, $classroom);

            return redirect()->route('classrooms.show', $classroom)
                ->with('success', 'تمت إضافة الطالب إلى الفصل بنجاح');
            } else {
                // Create new student
                $student = $this->studentService->createStudent($request->validated(), $classroom);

        return redirect()->route('classrooms.show', $classroom)
            ->with('success', 'تم إنشاء الطالب وإضافته إلى الفصل بنجاح')
                    ->with('password', $student->username)
                    ->with('username', $student->username);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        $stats = $this->studentService->getDashboardStats($student);
        
        // Get today's sessions
        $today_sessions = \App\Models\ClassSession::whereIn('class_room_id', $student->classRooms->pluck('id'))
            ->whereDate('session_date', now()->toDateString())
            ->orderBy('start_time')
            ->get();
        
        // Get recent messages
        $messages = $student->messages()
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
            
        // Get memorized count for memorization card
        $memorizedCount = \App\Models\MemorizationProgress::where('student_id', $student->id)
            ->where('status', 'memorized')
            ->count();
            
        return view('student.dashboard', array_merge($stats, [
            'student' => $student,
            'today_sessions' => $today_sessions,
            'messages' => $messages,
            'memorizedCount' => $memorizedCount
        ]));
    }

    /**
     * Display the student's attendance records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
        $student = Auth::guard('student')->user();
        $data = $this->studentService->getAttendanceData($student, $request->all());
        
        return view('student.attendance', $data);
    }

    /**
     * Display the student's classrooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function classrooms()
    {
        $student = Auth::guard('student')->user();
        $classrooms = $student->classRooms;
        
        return view('student.classrooms', compact('student', 'classrooms'));
    }

    /**
     * Display the student's messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function messages(Request $request)
    {
        $student = Auth::guard('student')->user();
        $filter = $request->get('filter', 'all');
        
        // Get both received and sent messages
        $query = Message::where(function($q) use ($student) {
            // Messages received by this student
            $q->where('student_id', $student->id)
              // Messages sent by this student
              ->orWhere(function($subQ) use ($student) {
                  $subQ->where('sender_type', 'student')
                       ->where('sender_id', $student->id);
              });
        })->with(['sender', 'recipient']);
        
        // Apply filter
        if ($filter === 'sent') {
            $query->where('sender_type', 'student')
                  ->where('sender_id', $student->id);
        } elseif ($filter === 'received') {
            $query->where('student_id', $student->id)
                  ->where('sender_type', '!=', 'student');
        }
        
        if ($request->unread) {
            $query->whereNull('read_at');
        }
        
        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('student.messages', compact('student', 'messages', 'filter'));
    }

    /**
     * Display a specific message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewMessage($id)
    {
        $student = Auth::guard('student')->user();
        
        // Get message that is either received by or sent by this student
        $message = Message::where(function($q) use ($student) {
            $q->where('student_id', $student->id)
              ->orWhere(function($subQ) use ($student) {
                  $subQ->where('sender_type', 'student')
                       ->where('sender_id', $student->id);
              });
        })->with(['sender', 'recipient'])->findOrFail($id);
        
        // Mark as read only if it's a received message
        if (!$message->read_at && $message->student_id == $student->id) {
            $message->update(['read_at' => now()]);
        }
        
        return view('student.messages.view', compact('student', 'message'));
    }
        
    /**
     * Show compose message form.
     *
     * @return \Illuminate\Http\Response
     */
    public function composeMessage()
    {
        $student = Auth::guard('student')->user();
        
        // First try to get all teachers in the student's school using direct school_id
        $teachers = \App\Models\User::where('school_id', $student->school_id)
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            })
            ->where('is_approved', true)
            ->get();
        
        // If no teachers found using school_id, try using the many-to-many relationship
        if ($teachers->isEmpty()) {
            $teachers = \App\Models\User::whereHas('teacherSchools', function($query) use ($student) {
                $query->where('schools.id', $student->school_id);
            })
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            })
            ->where('is_approved', true)
            ->get();
        }
        
        // If still no teachers found, fall back to classroom teachers
        if ($teachers->isEmpty()) {
            $teachers = \App\Models\User::whereIn('id', 
                $student->classRooms->pluck('user_id')
            )->get();
        }
        
        return view('student.messages.compose', compact('student', 'teachers'));
    }

    /**
     * Send message from student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:2000'
        ]);
        
        $student = Auth::guard('student')->user();
        
        // Verify the teacher is from the same school and is actually a teacher
        // First try direct school_id relationship
        $teacher = \App\Models\User::where('id', $request->teacher_id)
            ->where('school_id', $student->school_id)
            ->whereHas('role', function($query) {
                $query->where('name', 'teacher');
            })
            ->where('is_approved', true)
            ->first();
        
        // If not found via direct school_id, try many-to-many relationship
        if (!$teacher) {
            $teacher = \App\Models\User::where('id', $request->teacher_id)
                ->whereHas('teacherSchools', function($query) use ($student) {
                    $query->where('schools.id', $student->school_id);
                })
                ->whereHas('role', function($query) {
                    $query->where('name', 'teacher');
                })
                ->where('is_approved', true)
                ->first();
        }
            
        if (!$teacher) {
            return redirect()->back()
                ->with('error', 'المعلم المحدد غير متاح للمراسلة')
                ->withInput();
        }
        
        Message::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'sender_id' => auth('student')->id(),
            'sender_type' => 'student',
            'recipient_id' => $request->teacher_id,
            'recipient_type' => 'teacher',
            'student_id' => $student->id,
            'is_read' => false,
        ]);
        
        return redirect()->route('student.messages')
            ->with('success', 'تم إرسال الرسالة بنجاح');
    }
    
    /**
     * Mark message as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markMessageRead($id)
    {
        $student = Auth::guard('student')->user();
        
        // Only mark as read if this student is the recipient
        $message = Message::where('student_id', $student->id)->findOrFail($id);
        
        $message->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Display a listing of students across all classrooms for the authenticated teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function allStudents()
    {
        // Use TeacherService to get all accessible classrooms (not just owned ones)
        $teacherService = new \App\Services\TeacherService();
        $classrooms = $teacherService->getAccessibleClassrooms();
        
        // Get all students from teacher's accessible classrooms
        $students = collect();
        foreach ($classrooms as $classroom) {
            $students = $students->merge($classroom->students);
        }
        
        // Remove duplicates and get unique students
        $students = $students->unique('id');
        
        // Get all schools for the filter
        $schools = $classrooms->pluck('school')->unique('id')->filter();
        
        return view('teacher.students.all', compact('classrooms', 'students', 'schools'));
    }
    
    /**
     * Remove student from classroom.
     *
     * @param  int  $classroomId
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function removeFromClassroom($classroomId, $studentId)
    {
        try {
            $classroom = ClassRoom::findOrFail($classroomId);
            $this->authorize('view', $classroom);
            
            $student = Student::findOrFail($studentId);
            $this->studentService->removeStudentFromClassroom($student, $classroom);
            
            return redirect()->back()->with('success', 'تم إزالة الطالب من الفصل بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a student.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassRoom $classroom, Student $student)
    {
        $this->authorize('view', $classroom);
        
        return view('teacher.students.edit', compact('classroom', 'student'));
    }

    /**
     * Update the specified student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, ClassRoom $classroom, Student $student)
    {
        $this->authorize('view', $classroom);
        
        $updatedStudent = $this->studentService->updateStudent($student, $request->validated());

        return redirect()->route('classrooms.show', $classroom)
            ->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    /**
     * Get student credentials by student ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCredentials($id)
    {
        // Find the student
        $student = Student::findOrFail($id);
        
        // Check if the authenticated teacher has access to this student
        $teacherId = Auth::id();
        $hasAccess = $student->classRooms()
            ->whereHas('teacher', function ($query) use ($teacherId) {
                $query->where('id', $teacherId);
            })
            ->exists();
            
        if (!$hasAccess) {
            return response()->json(['error' => 'غير مصرح لك بالوصول إلى بيانات هذا الطالب'], 403);
        }
        
        // Generate new credentials only if necessary
        $credential = null;
        
        // Return the student's information
        return response()->json([
            'id' => $student->id,
                            'name' => $student->name,
            'username' => $student->username,
            'email' => $student->email,
            'note' => 'اسم المستخدم وكلمة المرور متطابقتان'
        ]);
    }

    /**
     * Display student credentials.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function viewCredentials(ClassRoom $classroom, Student $student)
    {
        $this->authorize('view', $classroom);
        
        return view('teacher.students.credentials', compact('classroom', 'student'));
    }

    /**
     * Send note to student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function sendNote(Request $request, ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
        ]);
        
        // Verify student is in this classroom
        $student = Student::findOrFail($request->student_id);
        if (!$classroom->students->contains($student)) {
            return back()->with('error', 'الطالب ليس في هذا الفصل');
        }
        
        // Create the message
        Message::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'type' => 'personal',
            'sender_id' => Auth::id(),
            'sender_type' => 'teacher',
            'student_id' => $request->student_id,
            'is_read' => false,
        ]);
        
        return redirect()->back()->with('success', 'تم إرسال الملاحظة بنجاح');
    }
}
