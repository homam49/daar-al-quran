<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\School;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Services\TeacherService;

class ClassRoomController extends Controller
{
    protected $teacherService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeacherService $teacherService)
    {
        $this->middleware(['auth', 'role:teacher', 'approved']);
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classRooms = $this->teacherService->getAccessibleClassrooms();
        
        return view('teacher.classrooms.index', compact('classRooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schools = $this->teacherService->getTeacherSchools()['schools'];
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        
        return view('teacher.classrooms.create', compact('schools', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'school_id' => 'required|exists:schools,id',
            'days' => 'required|array|min:1',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        // Check if teacher has access to this school
        if (!$this->teacherService->hasAccessToSchool($request->school_id)) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'لم تتم الموافقة على انضمامك لهذه المدرسة بعد من قبل المدير');
        }

        $classRoom = ClassRoom::create([
            'name' => $request->name,
            'description' => $request->description,
            'school_id' => $request->school_id,
            'user_id' => Auth::id(),
        ]);

        // Create schedules for each selected day
        foreach ($request->days as $day) {
            ClassSchedule::create([
                'day' => $day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'class_room_id' => $classRoom->id,
            ]);
        }

        return redirect()->route('classrooms.index')
            ->with('success', 'تم إنشاء الفصل بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function show(ClassRoom $classroom)
    {
        $this->authorize('view', $classroom);

        $classroom->load(['school', 'schedules', 'students', 'sessions']);
        
        // Get existing students in the SAME school that aren't already in this classroom
        $existingStudents = \App\Models\Student::where('school_id', $classroom->school_id)
            ->whereDoesntHave('classRooms', function ($query) use ($classroom) {
                $query->where('class_rooms.id', $classroom->id);
            })
            ->get();
        
        return view('teacher.classrooms.show', compact('classroom', 'existingStudents'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassRoom $classroom)
    {
        $this->authorize('update', $classroom);

        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $selectedDays = $classroom->schedules->pluck('day')->toArray();
        
        // Get the start and end time from the first schedule (assuming they're all the same)
        $startTime = $classroom->schedules->isNotEmpty() ? $classroom->schedules->first()->start_time : null;
        $endTime = $classroom->schedules->isNotEmpty() ? $classroom->schedules->first()->end_time : null;
        
        return view('teacher.classrooms.edit', compact('classroom', 'days', 'selectedDays', 'startTime', 'endTime'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassRoom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days' => 'required|array|min:1',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $classroom->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Delete existing schedules and create new ones
        $classroom->schedules()->delete();
        
        foreach ($request->days as $day) {
            ClassSchedule::create([
                'day' => $day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'class_room_id' => $classroom->id,
            ]);
        }

        return redirect()->route('classrooms.show', $classroom)
            ->with('success', 'تم تحديث الفصل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassRoom $classroom)
    {
        $this->authorize('delete', $classroom);

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'تم حذف الفصل بنجاح');
    }

    /**
     * Add a student to the classroom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClassRoom  $classroom
     * @return \Illuminate\Http\Response
     */
    public function addStudent(Request $request, ClassRoom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = \App\Models\Student::findOrFail($request->student_id);
        
        // Check if student is in the same school
        if ($student->school_id != $classroom->school_id) {
            return back()->with('error', 'الطالب ليس من نفس المدرسة');
        }
        
        // Check if student is already in the classroom
        if ($classroom->students()->where('student_id', $student->id)->exists()) {
            return back()->with('error', 'الطالب موجود بالفعل في هذا الفصل');
        }

        $classroom->students()->attach($student->id);

        return back()->with('success', 'تم إضافة الطالب للفصل بنجاح');
    }

    /**
     * Remove a student from the classroom.
     *
     * @param  \App\Models\ClassRoom  $classroom
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function removeStudent(ClassRoom $classroom, \App\Models\Student $student)
    {
        $this->authorize('update', $classroom);

        $classroom->students()->detach($student->id);

        return back()->with('success', 'تم إزالة الطالب من الفصل بنجاح');
    }

    /**
     * Broadcast a message to all students in a classroom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function broadcastMessage(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:class_rooms,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        // Find the classroom
        $classroom = ClassRoom::findOrFail($request->classroom_id);
        
        // Check if the authenticated user has access to this classroom
        $this->authorize('update', $classroom);
        
        // Get all students in the classroom
        $students = $classroom->students;
        
        if ($students->isEmpty()) {
            return back()->with('error', 'لا يوجد طلاب في هذا الفصل');
        }
        
        // Create a message for each student
        foreach ($students as $student) {
            Message::create([
                'subject' => $request->subject,
                'content' => $request->content,
                'sender_id' => auth()->id(),
                'sender_type' => 'teacher',
                'student_id' => $student->id,
                'recipient_id' => null, // Broadcast message, no specific recipient
                'class_room_id' => $classroom->id,
                'type' => 'class',
                'is_read' => false,
            ]);
        }
        
        return back()->with('success', "تم إرسال الإشعار بنجاح إلى {$students->count()} طالب في الفصل");
    }
}
