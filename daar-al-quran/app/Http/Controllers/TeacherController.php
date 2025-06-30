<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TeacherService;
use App\Http\Requests\JoinSchoolRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Models\ClassRoom;
use App\Models\School;
use App\Models\ClassSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    private $teacherService;

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
     * Display teacher's dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $stats = $this->teacherService->getDashboardStats();
        
        return view('teacher.dashboard', $stats);
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
     * @param  JoinSchoolRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function joinSchool(JoinSchoolRequest $request)
    {
        $result = $this->teacherService->joinSchool($request->school_code);
        
        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }
        
        return redirect()->route('teacher.dashboard')
            ->with('info', $result['message']);
    }

    /**
     * Display the list of schools.
     *
     * @return \Illuminate\Http\Response
     */
    public function schools()
    {
        $data = $this->teacherService->getSchoolsData();
        
        return view('teacher.schools', $data);
    }

    /**
     * Update the teacher's password.
     *
     * @param  UpdatePasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->teacherService->updatePassword($request->validated());

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Update the teacher's name.
     *
     * @param  UpdateNameRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateName(UpdateNameRequest $request)
    {
        $this->teacherService->updateName($request->validated());

        return back()->with('success', 'تم تحديث الاسم بنجاح');
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
     * Generate PDF with student credentials for a specific classroom
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classroomId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateClassroomCredentialsPdf(Request $request, $classroomId)
    {
        try {
            $classroom = ClassRoom::where('user_id', Auth::id())
                ->findOrFail($classroomId);

            $studentIds = $request->input('student_ids', []);
            
            $pdfContent = $this->teacherService->generateStudentCredentialsPdf(
                $studentIds, 
                $classroomId
            );
            
            $filename = 'student_credentials_' . $classroom->name . '_' . date('Y-m-d') . '.pdf';
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في إنشاء ملف PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF with credentials for selected students across classrooms
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateSelectedCredentialsPdf(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id'
        ]);

        try {
            $pdfContent = $this->teacherService->generateStudentCredentialsPdf(
                $request->student_ids
            );
            
            $filename = 'selected_student_credentials_' . date('Y-m-d') . '.pdf';
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في إنشاء ملف PDF: ' . $e->getMessage());
        }
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