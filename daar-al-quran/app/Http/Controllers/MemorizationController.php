<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemorizationUpdateRequest;
use App\Models\Student;
use App\Services\MemorizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemorizationController extends Controller
{
    protected $memorizationService;

    public function __construct(MemorizationService $memorizationService)
    {
        $this->memorizationService = $memorizationService;
    }

    /**
     * Show memorization tracking page for teacher.
     *
     * @param Student $student
     * @return \Illuminate\View\View
     */
    public function show(Student $student)
    {
        // Check if teacher has access to this student
        $this->authorize('update', [Student::class, $student]);
        
        $data = $this->memorizationService->getProgressData($student);
        
        return view('memorization.show', array_merge($data, [
            'student' => $student
        ]));
    }

    /**
     * Update memorization progress.
     *
     * @param MemorizationUpdateRequest $request
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MemorizationUpdateRequest $request, Student $student)
    {
        try {
            $teacherId = Auth::id();
            $progress = $this->memorizationService->updateProgress(
                $student, 
                $request->validated(), 
                $teacherId
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحفظ بنجاح',
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific progress information for teacher.
     *
     * @param Student $student
     * @param string $type
     * @param int $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgressInfo(Student $student, $type, $number)
    {
        // Check if teacher has access to this student
        $this->authorize('update', [Student::class, $student]);
        
        $progressInfo = $this->memorizationService->getProgressInfo($student, $type, $number);
        
        return response()->json($progressInfo);
    }

    /**
     * Show memorization progress for student (their own).
     *
     * @return \Illuminate\View\View
     */
    public function showStudent()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            abort(403, 'غير مصرح');
        }
        
        $data = $this->memorizationService->getProgressData($student);
        
        return view('memorization.student-show', array_merge($data, [
            'student' => $student
        ]));
    }

    /**
     * Get specific progress information for student (their own).
     *
     * @param string $type
     * @param int $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentProgress($type, $number)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        $progressInfo = $this->memorizationService->getProgressInfo($student, $type, $number);
        
        return response()->json($progressInfo);
    }

    /**
     * Batch update memorization progress for multiple items.
     *
     * @param Request $request
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchUpdate(Request $request, Student $student)
    {
        $this->authorize('update', [Student::class, $student]);
        $teacherId = Auth::id();
        $changes = $request->input('changes', []);
        if (is_string($changes)) {
            $changes = json_decode($changes, true);
        }
        $results = [];
        foreach ($changes as $change) {
            try {
                $progress = $this->memorizationService->updateProgress($student, $change, $teacherId);
                $results[] = [
                    'success' => true,
                    'type' => $change['type'] ?? null,
                    'number' => $change['page_number'] ?? $change['surah_number'] ?? $change['juz_number'] ?? null,
                    'status' => $change['status'] ?? null,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'type' => $change['type'] ?? null,
                    'number' => $change['page_number'] ?? $change['surah_number'] ?? $change['juz_number'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }
        }
        return response()->json(['results' => $results]);
    }
} 