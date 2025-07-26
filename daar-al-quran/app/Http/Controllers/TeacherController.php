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
use App\Models\Student;
use App\Models\MemorizationProgress;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
        $data = $this->teacherService->getSchools();
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
     * Get reports data for the teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function reports()
    {
        $classrooms = $this->teacherService->getAccessibleClassrooms();
        return view('teacher.reports', compact('classrooms'));
    }

    /**
     * Generate attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendanceReport(Request $request)
    {
        $filters = $request->only(['classroom_id', 'date_from', 'date_to']);
        $data = $this->teacherService->getAttendanceReportData($filters);
        
        return view('teacher.reports.attendance', $data);
    }

    /**
     * Generate performance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function performanceReport(Request $request)
    {
        $filters = $request->only(['classroom_id', 'date_from', 'date_to']);
        $data = $this->teacherService->getPerformanceReportData($filters);
        
        return view('teacher.reports.performance', $data);
    }

    /**
     * Export report in specified format.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function exportReport($type)
    {
        // Implementation for report export
        return response()->json(['message' => 'Report export functionality to be implemented']);
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
     * Generate CSV file with student information and memorization progress
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateStudentsCsv(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'days' => 'integer|min:1|max:365'
        ]);

        try {
            $days = $request->input('days', 60);
            $students = Student::whereIn('id', $request->student_ids)->get();
            
            $filename = 'students_report_' . date('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($students, $days) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Headers
                fputcsv($file, [
                    'اسم الطالب',
                    'العمر',
                    'الصفحات المحفوظة في آخر 6 أيام',
                    'الصفحات المحفوظة في آخر ' . $days . ' يوم',
                    'نسبة الحضور (%)',
                    'المدرسة',
                    'الفصول'
                ]);
                
                foreach ($students as $student) {
                    // Calculate memorization progress for last 6 days
                    $sixDaysAgo = Carbon::now()->subDays(6);
                    $pagesLast6Days = MemorizationProgress::where('student_id', $student->id)
                        ->where('type', 'page')
                        ->where('status', 'memorized')
                        ->where('updated_at', '>=', $sixDaysAgo)
                        ->count();

                    // Calculate memorization progress for specified days
                    $daysAgo = Carbon::now()->subDays($days);
                    $pagesLastDays = MemorizationProgress::where('student_id', $student->id)
                        ->where('type', 'page')
                        ->where('status', 'memorized')
                        ->where('updated_at', '>=', $daysAgo)
                        ->count();

                    // Calculate attendance percentage
                    $totalSessions = Attendance::where('student_id', $student->id)->count();
                    $attendedSessions = Attendance::where('student_id', $student->id)
                        ->where('status', 'present')
                        ->count();
                    
                    $attendancePercentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0;

                    // Get student's classes
                    $classes = $student->classRooms->pluck('name')->implode(', ');

                    fputcsv($file, [
                        $student->name,
                        $student->age . ' سنة',
                        $pagesLast6Days,
                        $pagesLastDays,
                        $attendancePercentage . '%',
                        $student->school->name ?? 'غير محدد',
                        $classes ?: 'غير محدد'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
                
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ في إنشاء ملف CSV: ' . $e->getMessage());
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