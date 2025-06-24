<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemorizationProgress;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Services\MemorizationService;
use App\Http\Requests\MemorizationUpdateRequest;
use Illuminate\Support\Facades\Auth;

class MemorizationController extends Controller
{
    protected $memorizationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(MemorizationService $memorizationService)
    {
        $this->middleware(['auth']);
        $this->memorizationService = $memorizationService;
    }

    /**
     * Display memorization progress for a student (Teacher view).
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        // Authorization check
        $this->authorizeTeacherAccess($student);

        // Get progress data using service
        $data = $this->memorizationService->getProgressData($student);

        return view('memorization.show', array_merge($data, ['student' => $student]));
    }

    /**
     * Update memorization status for a student.
     *
     * @param  \App\Http\Requests\MemorizationUpdateRequest  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(MemorizationUpdateRequest $request, Student $student)
    {
        try {
            $progress = $this->memorizationService->updateProgress(
                $student,
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الحفظ بنجاح',
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get memorization progress data for AJAX requests.
     *
     * @param  \App\Models\Student  $student
     * @param  string  $type
     * @param  int  $number
     * @return \Illuminate\Http\Response
     */
    public function getProgress(Student $student, $type, $number)
    {
        // Authorization check
        $this->authorizeTeacherAccess($student);

        return response()->json(
            $this->memorizationService->getProgressInfo($student, $type, $number)
        );
    }

    /**
     * Display student's own memorization progress (Student view).
     *
     * @return \Illuminate\Http\Response
     */
    public function showStudent()
    {
        $student = auth('student')->user();
        
        if (!$student) {
            abort(401, 'يجب تسجيل الدخول كطالب');
        }

        // Get progress data using service
        $data = $this->memorizationService->getProgressData($student);

        return view('memorization.student-show', array_merge($data, ['student' => $student]));
    }

    /**
     * Get memorization progress data for student AJAX requests.
     *
     * @param  string  $type
     * @param  int  $number
     * @return \Illuminate\Http\Response
     */
    public function getStudentProgress($type, $number)
    {
        $student = auth('student')->user();
        
        if (!$student) {
            return response()->json([
                'error' => 'يجب تسجيل الدخول كطالب'
            ], 401);
        }

        return response()->json(
            $this->memorizationService->getProgressInfo($student, $type, $number)
        );
    }

    /**
     * Check if teacher has access to student.
     *
     * @param Student $student
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeTeacherAccess(Student $student): void
    {
        if (!Auth::user()->hasRole('teacher')) {
            abort(403, 'غير مصرح بالوصول');
        }

        $teacherClassrooms = ClassRoom::where('user_id', Auth::id())->pluck('id');
        $studentInTeacherClasses = $student->classRooms()->whereIn('class_room_id', $teacherClassrooms)->exists();
        
        if (!$studentInTeacherClasses) {
            abort(403, 'لا يمكنك الوصول لبيانات هذا الطالب');
        }
    }
}
