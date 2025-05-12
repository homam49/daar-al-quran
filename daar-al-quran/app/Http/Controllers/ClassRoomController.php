<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\School;
use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ClassRoomController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:teacher', 'approved']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classRooms = ClassRoom::with(['school', 'schedules'])
            ->where('user_id', Auth::id())
            ->get();
        
        return view('teacher.classrooms.index', compact('classRooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get only the schools that this teacher has joined and been approved for
        $user = Auth::user();
        $classRooms = ClassRoom::where('user_id', $user->id)->get();
        $schoolIdsFromClasses = $classRooms->pluck('school_id')->unique();
        
        // Get approved school relationships
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Combine both sources of school IDs
        $schoolIds = $schoolIdsFromClasses->merge($approvedSchoolIds)->unique();
        
        // Only get schools that the teacher is associated with
        $schools = School::whereIn('id', $schoolIds)->get();
        
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

        // Check if teacher is approved for this school
        $isApproved = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('school_id', $request->school_id)
            ->where('is_approved', true)
            ->exists();
            
        // Teacher can also create classrooms if they already have a classroom in this school
        $hasExistingClassroom = ClassRoom::where('user_id', Auth::id())
            ->where('school_id', $request->school_id)
            ->exists();
            
        if (!$isApproved && !$hasExistingClassroom) {
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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الفصل');
        }

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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا الفصل');
        }

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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا الفصل');
        }

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

        // Delete existing schedules
        $classroom->schedules()->delete();

        // Create new schedules for each selected day
        foreach ($request->days as $day) {
            ClassSchedule::create([
                'day' => $day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'class_room_id' => $classroom->id,
            ]);
        }

        return redirect()->route('classrooms.index')
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
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا الفصل');
        }

        // Delete the classroom (related records will be deleted via foreign keys)
        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'تم حذف الفصل بنجاح');
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
        
        // Check if the authenticated user owns this classroom
        if ($classroom->user_id !== auth()->id()) {
            return back()->with('error', 'غير مصرح لك بإرسال إشعارات لهذا الفصل');
        }
        
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
                'student_id' => $student->id,
                'class_room_id' => $classroom->id,
                'type' => 'class',
                'is_read' => false,
            ]);
        }
        
        return back()->with('success', "تم إرسال الإشعار بنجاح إلى {$students->count()} طالب في الفصل");
    }
}
