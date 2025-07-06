<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\School;
use App\Services\AdminService;
use App\Http\Requests\ApproveTeacherRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Http\Requests\UpdateUsernameRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private $adminService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AdminService $adminService)
    {
        $this->middleware(['auth', 'role:admin', 'approved']);
        $this->adminService = $adminService;
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $stats = $this->adminService->getDashboardStats();
        
        return view('admin.dashboard', $stats);
    }

    /**
     * Display a list of all teachers.
     *
     * @return \Illuminate\View\View
     */
    public function teachers()
    {
        $data = $this->adminService->getTeachersData();
        
        return view('admin.teachers', $data);
    }

    /**
     * Approve a teacher's request to join a school.
     *
     * @param  ApproveTeacherRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveTeacherSchool(ApproveTeacherRequest $request)
    {
        $success = $this->adminService->approveTeacherForSchool(
            $request->user_id,
            $request->school_id
        );
        
        if (!$success) {
            return back()->with('error', 'ليس لديك صلاحية للوصول إلى هذه المدرسة');
        }
            
        return back()->with('success', 'تمت الموافقة على انضمام المعلم للمدرسة بنجاح');
    }

    /**
     * Reject a teacher's request to join a school.
     *
     * @param  ApproveTeacherRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectTeacherSchool(ApproveTeacherRequest $request)
    {
        $success = $this->adminService->rejectTeacherForSchool(
            $request->user_id,
            $request->school_id
        );
        
        if (!$success) {
            return back()->with('error', 'ليس لديك صلاحية للوصول إلى هذه المدرسة');
        }
            
        return back()->with('success', 'تم رفض طلب انضمام المعلم للمدرسة بنجاح');
    }

    /**
     * Approve a specific teacher.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveTeacher(User $user)
    {
        // Check if the user is a teacher
        if ($user->role->name != 'teacher') {
            return back()->with('error', 'هذا المستخدم ليس معلماً');
        }

        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'تمت الموافقة على المعلم بنجاح');
    }

    /**
     * Delete a specific teacher from the admin's school.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteTeacher(User $user)
    {
        // Check if the user is a teacher
        if ($user->role->name != 'teacher') {
            return back()->with('error', 'هذا المستخدم ليس معلماً');
        }
        
        $this->adminService->deleteTeacher($user);
        
        return redirect()->route('admin.teachers')->with('success', 'تم إزالة المعلم من مدارسِك وحذف فصوله بنجاح');
    }

    /**
     * Display details for a specific teacher.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function showTeacher(User $user)
    {
        // Check if the user is actually a teacher
        if ($user->role->name != 'teacher') {
            return redirect()->route('admin.teachers')->with('error', 'المستخدم المحدد ليس معلماً');
        }
        
        // Get admin's schools
        $adminSchools = School::where('admin_id', Auth::id())->pluck('id')->toArray();
        
        // Check if the teacher belongs to any of the admin's schools
        $teacherInAdminSchool = DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->whereIn('school_id', $adminSchools)
            ->exists();
        
        if (!$teacherInAdminSchool) {
            return redirect()->route('admin.teachers')->with('error', 'غير مصرح لك بعرض هذا المعلم');
        }
        
        // Get classrooms created by this teacher in admin's schools only
        $classrooms = $user->classRooms()
            ->whereIn('school_id', $adminSchools)
            ->with('school')
            ->get();
        
        return view('admin.show-teacher', compact('user', 'classrooms'));
    }

    
    /**
     * Display the admin reports page.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        $schools = School::where('admin_id', Auth::id())->get();
        return view('admin.reports', compact('schools'));
    }
    
    /**
     * Display a list of all students in the admin's schools.
     *
     * @return \Illuminate\View\View
     */
    public function students()
    {
        $students = $this->adminService->getStudents();
        $classrooms = $this->adminService->getClassrooms();
        
        return view('admin.students', compact('students', 'classrooms'));
    }
    
    /**
     * Delete a student from the admin's school.
     *
     * @param  int  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteStudent($student)
    {
        $student = Student::find($student);
        
        if (!$student) {
            return back()->with('error', 'الطالب غير موجود');
        }
        
        $success = $this->adminService->deleteStudent($student);
        
        if (!$success) {
            return back()->with('error', 'ليس لديك صلاحية حذف هذا الطالب');
        }
        
        return back()->with('success', 'تم حذف الطالب بنجاح');
    }
    
    /**
     * Update the admin's password.
     *
     * @param  UpdatePasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->adminService->updatePassword($request->validated());

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Update the admin's username.
     *
     * @param  UpdateUsernameRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUsername(UpdateUsernameRequest $request)
    {
        $this->adminService->updateUsername($request->validated());

        return back()->with('success', 'تم تحديث اسم المستخدم بنجاح');
    }

    /**
     * Update the admin's name.
     *
     * @param  UpdateNameRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateName(UpdateNameRequest $request)
    {
        $this->adminService->updateName($request->validated());

        return back()->with('success', 'تم تحديث الاسم بنجاح');
    }

    /**
     * Display a list of classrooms.
     *
     * @return \Illuminate\View\View
     */
    public function classrooms(Request $request)
    {
        $classrooms = $this->adminService->getClassrooms();
        
        return view('admin.classrooms', compact('classrooms'));
    }

    /**
     * Grant classroom access to a teacher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function grantClassroomAccess(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'classroom_id' => 'required|exists:class_rooms,id',
        ]);

        $success = $this->adminService->grantClassroomAccess(
            $request->teacher_id,
            $request->classroom_id
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لإدارة هذا الفصل أو هذا المعلم'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم منح المعلم حق الوصول للفصل بنجاح'
        ]);
    }

    /**
     * Revoke classroom access from a teacher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeClassroomAccess(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'classroom_id' => 'required|exists:class_rooms,id',
        ]);

        $success = $this->adminService->revokeClassroomAccess(
            $request->teacher_id,
            $request->classroom_id
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لإدارة هذا الفصل أو هذا المعلم'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء حق الوصول للفصل بنجاح'
        ]);
    }

    /**
     * Get classroom access data for a teacher.
     *
     * @param  int  $teacher_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTeacherClassroomAccess($teacher_id)
    {
        try {
            // Validate that the teacher exists
            $teacher = User::find($teacher_id);
            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'المعلم غير موجود'
                ], 404);
            }

            // Check if admin has any schools
            $adminSchools = School::where('admin_id', Auth::id())->get();
            if ($adminSchools->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد مدارس تابعة لك'
                ], 400);
            }

            $data = $this->adminService->getTeacherClassroomAccess($teacher_id);

            // Add debug info
            $data['debug'] = [
                'admin_id' => Auth::id(),
                'admin_schools_count' => $adminSchools->count(),
                'teacher_id' => $teacher_id,
                'classrooms_count' => count($data['classrooms'])
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في الخادم: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }
}

