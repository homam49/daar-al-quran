<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
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
     * Display teacher's dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $classRooms = ClassRoom::where('user_id', Auth::id())->get();
        $schoolIds = $classRooms->pluck('school_id')->unique();
        
        // Get schools where teacher is approved
        $approvedSchools = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Count unique approved schools
        $schools_count = count(array_unique(array_merge($schoolIds->toArray(), $approvedSchools)));
        
        // Get pending school approvals
        $pendingSchools = DB::table('school_teacher')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('school_teacher.user_id', Auth::id())
            ->where('school_teacher.is_approved', false)
            ->select('schools.id', 'schools.name')
            ->get();
        
        // Count classrooms
        $classrooms_count = $classRooms->count();
        
        // Count unique students across all classrooms
        $students_count = Student::whereHas('classRooms', function($query) use ($classRooms) {
            $query->whereIn('class_rooms.id', $classRooms->pluck('id'));
        })->distinct('id')->count();
        
        // Count sessions
        $sessions_count = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))->count();
        
        // Get memorization statistics
        $teacherStudentIds = Student::whereHas('classRooms', function($query) use ($classRooms) {
            $query->whereIn('class_rooms.id', $classRooms->pluck('id'));
        })->pluck('id');
        
        $memorization_records = \App\Models\MemorizationProgress::whereIn('student_id', $teacherStudentIds)->get();
        
        $memorization_stats = [
            'total_memorized' => $memorization_records->where('status', 'memorized')->count(),
            'in_progress' => $memorization_records->where('status', 'in_progress')->count(),
            'total_students_tracking' => $memorization_records->unique('student_id')->count(),
            'pages_memorized' => $memorization_records->where('type', 'page')->where('status', 'memorized')->count(),
            'surahs_memorized' => $memorization_records->where('type', 'surah')->where('status', 'memorized')->count(),
            'pages_in_progress' => $memorization_records->where('type', 'page')->where('status', 'in_progress')->count(),
            'surahs_in_progress' => $memorization_records->where('type', 'surah')->where('status', 'in_progress')->count(),
            'total_content_items' => $memorization_records->count(),
        ];
        
        // Get today's sessions
        $today = now()->format('Y-m-d');
        $today_sessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->whereDate('session_date', $today)
            ->orderBy('start_time')
            ->with(['classRoom.school']) // Correctly eager load classRoom and school relationships
            ->get();
        
        // Format the sessions for proper display
        foreach ($today_sessions as $session) {
            // Make sure the classRoom relationship exists
            if (!$session->classRoom) {
                $session->classRoom = ClassRoom::find($session->class_room_id);
            }
        }
        
        // Get recent sessions
        $recent_sessions = ClassSession::whereIn('class_room_id', $classRooms->pluck('id'))
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(4)
            ->with(['classRoom.school']) // Correctly eager load classRoom and school relationships
            ->get();
        
        // Format the recent sessions for proper display
        foreach ($recent_sessions as $session) {
            // Make sure the classRoom relationship exists
            if (!$session->classRoom) {
                $session->classRoom = ClassRoom::find($session->class_room_id);
            }
            // Add attendance count attribute
            $session->attendance_count = $session->attendances()->count();
        }
        
        return view('teacher.dashboard', compact(
            'schools_count', 
            'classrooms_count', 
            'students_count', 
            'sessions_count',
            'memorization_stats',
            'today_sessions',
            'recent_sessions',
            'classRooms',
            'pendingSchools'
        ));
    }

    /**
     * Show the form for joining a school.
     *
     * @return \Illuminate\View\View
     */
    public function showJoinSchoolForm()
    {
        return view('teacher.join-school');
    }

    /**
     * Join a school using the provided code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function joinSchool(Request $request)
    {
        $request->validate([
            'school_code' => 'required|string|exists:schools,code',
        ]);
        
        $school = School::where('code', $request->school_code)->first();
        
        if (!$school) {
            return back()->with('error', 'رمز المدرسة غير صحيح');
        }
        
        $user = Auth::user();
        
        // Check if teacher is already associated with this school
        $alreadyJoined = DB::table('school_teacher')
            ->where('school_id', $school->id)
            ->where('user_id', $user->id)
            ->exists();
        
        if (!$alreadyJoined) {
            // Add teacher to the school in the pivot table with is_approved set to false
            DB::table('school_teacher')->insert([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'is_approved' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Temporarily store the school information in the session
        session(['pending_school_id' => $school->id]);
        session(['pending_school_name' => $school->name]);
        
        return redirect()->route('teacher.dashboard')
            ->with('info', 'تم تقديم طلب الانضمام إلى المدرسة بنجاح. يرجى انتظار موافقة مدير المدرسة.');
    }

    /**
     * Display the list of schools.
     *
     * @return \Illuminate\Http\Response
     */
    public function schools()
    {
        // Get schools where the teacher has created classrooms
        $classRooms = ClassRoom::where('user_id', Auth::id())->get();
        $schoolIds = $classRooms->pluck('school_id')->unique();
        
        // Get schools where the teacher is approved
        $approvedSchoolIds = DB::table('school_teacher')
            ->where('user_id', Auth::id())
            ->where('is_approved', true)
            ->pluck('school_id')
            ->toArray();
        
        // Merge and get unique school IDs
        $allSchoolIds = array_unique(array_merge($schoolIds->toArray(), $approvedSchoolIds));
        
        // Get the schools
        $schools = School::whereIn('id', $allSchoolIds)->get();
        
        // Get pending schools
        $pendingSchools = DB::table('school_teacher')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('school_teacher.user_id', Auth::id())
            ->where('school_teacher.is_approved', false)
            ->select('schools.id', 'schools.name', 'school_teacher.created_at')
            ->get();
        
        return view('teacher.schools', compact('schools', 'pendingSchools'));
    }

    /**
     * Update the teacher's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Update the teacher's name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'تم تحديث الاسم بنجاح.');
    }

    /**
     * Display reports for the teacher.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        $classRooms = ClassRoom::where('user_id', Auth::id())->get();
        return view('teacher.reports', compact('classRooms'));
    }

    /**
     * Display attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function attendanceReport(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:class_rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $classroom = ClassRoom::findOrFail($request->classroom_id);
        $this->authorize('view', $classroom);

        $sessions = ClassSession::where('class_room_id', $classroom->id)
            ->whereBetween('session_date', [$request->start_date, $request->end_date])
            ->orderBy('session_date')
            ->get();

        return view('teacher.reports.attendance', compact('classroom', 'sessions'));
    }

    /**
     * Display performance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function performanceReport(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:class_rooms,id',
            'period' => 'required|in:month,semester,year',
        ]);

        $classroom = ClassRoom::findOrFail($request->classroom_id);
        $this->authorize('view', $classroom);

        // Logic for getting performance data based on period
        $startDate = now();
        $endDate = now();

        if ($request->period === 'month') {
            $startDate = now()->startOfMonth();
        } elseif ($request->period === 'semester') {
            $startDate = now()->subMonths(4);
        } elseif ($request->period === 'year') {
            $startDate = now()->subYear();
        }

        $sessions = ClassSession::where('class_room_id', $classroom->id)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->orderBy('session_date')
            ->get();

        // If a specific student is selected
        $student = null;
        if ($request->filled('student_id')) {
            $student = Student::findOrFail($request->student_id);
        }

        return view('teacher.reports.performance', compact('classroom', 'sessions', 'student'));
    }

    /**
     * Export a report.
     *
     * @param  string  $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportReport($type)
    {
        // This would typically use a package like maatwebsite/excel for exports
        // For now, we'll just return a response indicating it's not implemented
        return back()->with('info', 'سيتم تفعيل تصدير التقارير قريبًا');
    }

    /**
     * Get a list of students for a classroom in JSON format.
     *
     * @param  int  $classroom
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentsList($classroom)
    {
        $classroom = ClassRoom::findOrFail($classroom);
        $this->authorize('view', $classroom);

        $students = $classroom->students;
        return response()->json($students);
    }
} 