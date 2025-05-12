<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\StudentClassRoom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of students for a specific classroom.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function index(ClassRoom $classroom)
    {
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

        // Get existing students in the SAME school that aren't already in this classroom
        $existingStudents = Student::where('school_id', $classroom->school_id)
            ->whereDoesntHave('classRooms', function ($query) use ($classroom) {
                $query->where('class_rooms.id', $classroom->id);
            })
            ->get();

        return view('teacher.students.create', compact('classroom', 'existingStudents'));
    }

    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ClassRoom $classroom)
    {
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

        // Check if we're adding an existing student
        if ($request->has('existing_student_id')) {
            $request->validate([
                'existing_student_id' => 'required|exists:students,id',
            ]);

            $student = Student::findOrFail($request->existing_student_id);

            // Check if the student is in the same school
            if ($student->school_id != $classroom->school_id) {
                return back()->with('error', 'الطالب ليس من نفس المدرسة');
            }

            // Check if the student is already in the class
            if ($classroom->students->contains($student->id)) {
                return back()->with('error', 'الطالب موجود بالفعل في هذا الفصل');
            }

            // Add the student to the class
            StudentClassRoom::create([
                'student_id' => $student->id,
                'class_room_id' => $classroom->id,
            ]);

            return redirect()->route('teacher.classroom.students', $classroom)
                ->with('success', 'تمت إضافة الطالب إلى الفصل بنجاح');
        }

        // Otherwise, create a new student
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_year' => 'required|numeric|digits:4',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|unique:students,email',
        ]);

        // Generate a random 6-character credential (uppercase) to be used for both username and password
        $credential = strtoupper(Str::random(6));
        
        // Create the student
        $student = Student::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'birth_year' => $request->birth_year,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'username' => $credential,
            'password' => $credential, // Will be hashed by the model's setPasswordAttribute method
            'school_id' => $classroom->school_id,
            'first_login' => true,
        ]);

        // Add the student to the class
        StudentClassRoom::create([
            'student_id' => $student->id,
            'class_room_id' => $classroom->id,
        ]);

        return redirect()->route('teacher.classroom.students', $classroom)
            ->with('success', 'تم إنشاء الطالب وإضافته إلى الفصل بنجاح')
            ->with('password', $credential)
            ->with('username', $credential);
    }

    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        
        // Get classroom count
        $classroom_count = $student->classRooms->count();
        
        // Calculate attendance statistics
        $attendances = $student->attendances;
        $attendance_count = $attendances->whereIn('status', ['present', 'late'])->count();
        $present_count = $attendances->where('status', 'present')->count();
        $late_count = $attendances->where('status', 'late')->count();
        $absent_count = $attendances->where('status', 'absent')->count();
        
        // Calculate attendance percentage
        $total_attendances = $attendances->count();
        $attendance_percentage = $total_attendances > 0 
            ? round(($attendance_count / $total_attendances) * 100) 
            : 0;
        
        // Get today's sessions
        $today_sessions = \App\Models\ClassSession::whereIn('class_room_id', $student->classRooms->pluck('id'))
            ->whereDate('session_date', now()->toDateString())
            ->orderBy('start_time')
            ->get();
        
        // Get unread messages count
        $unread_messages = $student->messages()->whereNull('read_at')->count();
        
        // Get recent messages
        $messages = $student->messages()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('student.dashboard', compact(
            'student',
            'classroom_count',
            'attendance_count',
            'present_count',
            'late_count',
            'absent_count',
            'attendance_percentage',
            'today_sessions',
            'unread_messages',
            'messages'
        ));
    }

    /**
     * Display the student's attendance records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
        $student = auth('student')->user();
        
        // Get the student's classrooms
        $classrooms = $student->classRooms;
        
        // Build query for attendances
        $query = $student->attendances()->with(['classSession', 'classSession.classRoom']);
        
        // Filter by classroom if provided
        if ($request->has('classroom_id') && $request->classroom_id) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('class_room_id', $request->classroom_id);
            });
        }
        
        // Filter by month if provided
        if ($request->has('month') && $request->month) {
            $month = $request->month;
            $query->whereHas('classSession', function ($q) use ($month) {
                $q->whereMonth('session_date', $month);
            });
        }
        
        // Get attendances with pagination
        $attendances = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate statistics
        $present_count = $student->attendances->where('status', 'present')->count();
        $late_count = $student->attendances->where('status', 'late')->count();
        $absent_count = $student->attendances->where('status', 'absent')->count();
        
        // Calculate attendance percentage
        $total_attendances = $present_count + $late_count + $absent_count;
        $attendance_percentage = $total_attendances > 0 
            ? round((($present_count + $late_count) / $total_attendances) * 100) 
            : 0;
        
        // Define months for filter
        $months = [
            '1' => 'يناير',
            '2' => 'فبراير',
            '3' => 'مارس',
            '4' => 'أبريل',
            '5' => 'مايو',
            '6' => 'يونيو',
            '7' => 'يوليو',
            '8' => 'أغسطس',
            '9' => 'سبتمبر',
            '10' => 'أكتوبر',
            '11' => 'نوفمبر',
            '12' => 'ديسمبر'
        ];
        
        return view('student.attendance', compact(
            'student',
            'attendances',
            'classrooms',
            'present_count',
            'late_count',
            'absent_count',
            'attendance_percentage',
            'months'
        ));
    }

    /**
     * Display the student's classrooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function classrooms()
    {
        $student = auth('student')->user();
        $classrooms = $student->classRooms;
        
        // Prepare schedule information for each classroom
        foreach ($classrooms as $classroom) {
            // Get classroom schedules
            $schedules = \App\Models\ClassSchedule::where('class_room_id', $classroom->id)
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();
                
            // Format days and times for display
            $classScheduleInfo = [];
            if ($schedules->count() > 0) {
                // Get days and format them
                $daysMap = [
                    'Sunday' => 'الأحد',
                    'Monday' => 'الاثنين',
                    'Tuesday' => 'الثلاثاء',
                    'Wednesday' => 'الأربعاء',
                    'Thursday' => 'الخميس',
                    'Friday' => 'الجمعة',
                    'Saturday' => 'السبت'
                ];
                
                $days = [];
                $times = [];
                
                foreach ($schedules as $schedule) {
                    $days[] = $daysMap[$schedule->day] ?? $schedule->day;
                    
                    // Format time
                    $startTime = date('g:i A', strtotime($schedule->start_time));
                    $endTime = date('g:i A', strtotime($schedule->end_time));
                    $times[] = "{$startTime} - {$endTime}";
                }
                
                $classroom->days = $days;
                $classroom->start_time = $schedules->first()->start_time ?? null;
                $classroom->end_time = $schedules->first()->end_time ?? null;
                $classroom->scheduleInfo = [
                    'days' => $days,
                    'times' => array_unique($times)
                ];
            }
        }
        
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
        $student = auth('student')->user();
        
        // Get the filter parameter from the request
        $filter = $request->input('filter', 'all'); // Default to 'all' if not specified
        
        // Get incoming messages (from teachers)
        $incomingQuery = $student->messages()
            ->where('sender_type', 'teacher');
        
        // Get outgoing messages (sent by this student)
        $outgoingQuery = Message::where('sender_id', $student->id)
            ->where('sender_type', 'student');
        
        // Apply filters
        $incomingMessages = collect();
        $outgoingMessages = collect();
        
        if ($filter === 'all' || $filter === 'received') {
            $incomingMessages = $incomingQuery->get();
        }
        
        if ($filter === 'all' || $filter === 'sent') {
            $outgoingMessages = $outgoingQuery->get();
        }
        
        // For read/unread filtering
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $incomingMessages = $incomingMessages->whereNull('read_at');
                // No outgoing messages in this case (all outgoing are considered read)
                $outgoingMessages = collect();
            } elseif ($request->status === 'read') {
                $incomingMessages = $incomingMessages->whereNotNull('read_at');
            }
        }
        
        // Combine and sort
        $allMessages = $incomingMessages->concat($outgoingMessages);
        
        // Sort by date
        if ($request->has('sort') && $request->sort === 'oldest') {
            $allMessages = $allMessages->sortBy('created_at');
        } else {
            $allMessages = $allMessages->sortByDesc('created_at');
        }
        
        // Paginate the collection using the proper method for collections
        $page = request('page', 1); // Get the current page from the request
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = $allMessages->slice($offset, $perPage)->values();
        
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $allMessages->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
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
        $student = auth('student')->user();
        $message = $student->messages()->findOrFail($id);
        
        // Mark message as read if it hasn't been read yet
        if (!$message->read_at) {
            $message->read_at = now();
            $message->save();
        }
        
        // Get other messages for sidebar
        $otherMessages = $student->messages()
            ->where('id', '!=', $message->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('student.message-view', compact('student', 'message', 'otherMessages'));
    }

    /**
     * Store a newly created student in a classroom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeInClassroom(Request $request, $classroomId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_year' => 'required|numeric|min:' . (date('Y') - 100) . '|max:' . date('Y'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        
        // Generate a random 6-character credential (uppercase) to be used for both username and password
        $credential = strtoupper(Str::random(6));
        
        // Create the student record
        $student = Student::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'birth_year' => $request->birth_year,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => null,
            'username' => $credential,
            'password' => $credential,
            'first_login' => true,
            'school_id' => $classroom->school_id,
        ]);
        
        // Attach the student to the classroom
        $classroom->students()->attach($student->id);
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم إضافة الطالب بنجاح. اسم المستخدم وكلمة المرور هي: ' . $credential)
            ->with('password', $credential)
            ->with('username', $credential);
    }
    
    /**
     * Attach an existing student to the classroom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attachToClassroom(Request $request, $classroomId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);
        
        // Check if student is already in this classroom
        if ($classroom->students->contains($request->student_id)) {
            return back()->with('error', 'الطالب موجود بالفعل في هذا الفصل');
        }
        
        // Check if student is in the same school
        $student = Student::findOrFail($request->student_id);
        if ($student->school_id != $classroom->school_id) {
            return back()->with('error', 'يمكنك فقط إضافة طلاب من نفس المدرسة');
        }
        
        // Add student to classroom
        $classroom->students()->attach($request->student_id);
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم إضافة الطالب إلى الفصل بنجاح');
    }
    
    /**
     * Remove a student from the classroom.
     *
     * @param  int  $classroomId
     * @param  int  $studentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromClassroom($classroomId, $studentId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        // Check if student is in this classroom
        if (!$classroom->students->contains($studentId)) {
            return back()->with('error', 'الطالب غير موجود في هذا الفصل');
        }
        
        // Detach the student from the classroom
        $classroom->students()->detach($studentId);
        
        $student = Student::find($studentId);
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم إزالة الطالب ' . $student->full_name . ' من الفصل بنجاح');
    }
    
    /**
     * Send a note to a student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendNote(Request $request, $classroomId)
    {
        $classroom = ClassRoom::findOrFail($classroomId);
        
        // Check if the teacher owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        // Check if student is in this classroom
        if (!$classroom->students->contains($request->student_id)) {
            return back()->with('error', 'الطالب غير موجود في هذا الفصل');
        }
        
        // Create the message
        Message::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'sender_id' => Auth::id(),
            'student_id' => $request->student_id,
            'class_room_id' => $classroom->id,
            'type' => 'personal',
        ]);
        
        return redirect()->route('classrooms.show', $classroom->id)
            ->with('success', 'تم إرسال الملاحظة بنجاح');
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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

        // Check if student belongs to the classroom
        if (!$classroom->students->contains($student->id)) {
            abort(403, 'هذا الطالب ليس مسجلاً في هذا الفصل');
        }
        
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
    public function update(Request $request, ClassRoom $classroom, Student $student)
    {
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        // Check if student belongs to the classroom
        if (!$classroom->students->contains($student->id)) {
            abort(403, 'هذا الطالب ليس مسجلاً في هذا الفصل');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_year' => 'required|numeric|min:' . (date('Y') - 100) . '|max:' . date('Y'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $student->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'birth_year' => $request->birth_year,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('teacher.classroom.students', $classroom)
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
            'name' => $student->full_name,
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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }
        
        // Check if the student belongs to this classroom
        if (!$classroom->students->contains($student->id)) {
            return back()->with('error', 'هذا الطالب ليس في هذا الفصل');
        }
        
        return view('teacher.students.credentials', [
            'classroom' => $classroom,
            'student' => $student
        ]);
    }

    /**
     * Mark a message as read via AJAX.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markMessageRead($id)
    {
        try {
            $student = auth('student')->user();
            $message = Message::where(function($query) use ($student, $id) {
                // Messages sent to this student
                $query->where('student_id', $student->id)
                      ->where('id', $id);
            })->orWhere(function($query) use ($student, $id) {
                // Messages sent by this student
                $query->where('sender_id', $student->id)
                      ->where('sender_type', 'student')
                      ->where('id', $id);
            })->firstOrFail();
            
            // Only update if message is not already read
            if (!$message->read_at) {
                $message->read_at = now();
                $message->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم تمييز الرسالة كمقروءة'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تمييز الرسالة كمقروءة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new message to a teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function composeMessage()
    {
        $student = auth('student')->user();
        
        // Get all classrooms the student belongs to with their teachers
        $classrooms = $student->classRooms()->with('user')->get();
        
        // Get unique teachers from these classrooms
        $teachers = $classrooms->pluck('user')->unique('id')->filter()->values();
        
        return view('student.compose-message', compact('student', 'teachers', 'classrooms'));
    }
    
    /**
     * Store a newly created message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $student = auth('student')->user();
        
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_id' => 'required|exists:users,id',
        ]);
        
        // Verify that the teacher teaches one of the student's classes
        $isTeacherOfStudent = $student->classRooms()
            ->where('user_id', $request->recipient_id)
            ->exists();
            
        if (!$isTeacherOfStudent) {
            return back()->with('error', 'لا يمكنك إرسال رسالة إلى معلم ليس من معلميك');
        }
        
        // Create the message
        Message::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'type' => 'personal',
            'sender_id' => $student->id,
            'sender_type' => 'student',
            'recipient_id' => $request->recipient_id,
            'is_read' => false,
        ]);
        
        return redirect()->route('student.messages')
            ->with('success', 'تم إرسال الرسالة بنجاح');
    }

    /**
     * Display a listing of students across all classrooms for the authenticated teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function allStudents()
    {
        $teacher = Auth::user();
        
        // Get all classrooms owned by this teacher
        $classrooms = ClassRoom::where('user_id', $teacher->id)->get();
        
        // Get unique student IDs from all classrooms
        $studentIds = [];
        foreach ($classrooms as $classroom) {
            $studentIds = array_merge($studentIds, $classroom->students->pluck('id')->toArray());
        }
        
        // Remove duplicates
        $studentIds = array_unique($studentIds);
        
        // Get all students with their school relationship loaded
        $students = Student::whereIn('id', $studentIds)->with('school')->get();
        
        // Get unique schools from the students for the filter
        $schools = $students->pluck('school')->unique('id')->sortBy('name');
        
        return view('teacher.students.all', compact('students', 'classrooms', 'schools'));
    }
}
