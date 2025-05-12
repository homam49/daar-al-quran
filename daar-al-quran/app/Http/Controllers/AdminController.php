<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin', 'approved']);
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Get the single school owned by this admin
        $school = School::where('admin_id', Auth::id())->first();
        $schools_count = $school ? 1 : 0;
        
        // If the admin has a school, get the details for the dashboard
        if ($school) {
            // Calculate classrooms count for the school
            $classrooms_count = $school->classRooms()->count();
            
            // Get teachers in admin's school
            $teacherRole = Role::where('name', 'teacher')->first();
            $teachers_count = DB::table('school_teacher')
                ->join('users', 'users.id', '=', 'school_teacher.user_id')
                ->where('school_teacher.school_id', $school->id)
                ->where('users.role_id', $teacherRole->id)
                ->where('users.is_approved', true)
                ->distinct('users.id')
                ->count('users.id');
            
            // Get total students count
            $students_count = $school->students()->count();
            
            // We don't need system pending teachers on the admin dashboard
            $system_pending_teachers = collect([]);
                
            // Get teachers waiting for school approval
            $school_pending_teachers = DB::table('school_teacher')
                ->join('users', 'users.id', '=', 'school_teacher.user_id')
                ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
                ->where('users.role_id', $teacherRole->id)
                ->where('users.is_approved', true) // Teacher is approved in the system
                ->where('school_teacher.is_approved', false) // But not approved for this school
                ->where('schools.id', $school->id)
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.created_at as user_created_at',
                    'schools.id as school_id',
                    'schools.name as school_name',
                    'school_teacher.created_at as joined_at'
                )
                ->get();
            
            // Show only school pending teachers
            $pending_teachers = [
                'system' => $system_pending_teachers,
                'school' => $school_pending_teachers
            ];
        } else {
            // No school yet, set defaults
            $classrooms_count = 0;
            $teachers_count = 0;
            $students_count = 0;
            $pending_teachers = [
                'system' => collect([]),
                'school' => collect([])
            ];
        }
        
        return view('admin.dashboard', compact(
            'school',
            'schools_count', 
            'teachers_count', 
            'students_count', 
            'classrooms_count', 
            'pending_teachers'
        ));
    }

    /**
     * Display a list of all teachers.
     *
     * @return \Illuminate\View\View
     */
    public function teachers()
    {
        // Get all schools owned by this admin
        $schools = School::where('admin_id', Auth::id())->get();
        $schoolIds = $schools->pluck('id')->toArray();
        
        // Get the teacher role ID
        $teacherRole = Role::where('name', 'teacher')->first();
        
        // Get pending teacher approvals for this admin's schools
        $pendingTeachers = DB::table('school_teacher')
            ->join('users', 'users.id', '=', 'school_teacher.user_id')
            ->join('schools', 'schools.id', '=', 'school_teacher.school_id')
            ->where('users.role_id', $teacherRole->id)
            ->where('users.is_approved', true) // Teacher is approved in the system
            ->where('school_teacher.is_approved', false) // But not approved for this school
            ->whereIn('school_teacher.school_id', $schoolIds)
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.created_at as user_created_at',
                'schools.id as school_id',
                'schools.name as school_name',
                'school_teacher.created_at as joined_at'
            )
            ->get();
        
        // Retrieve all teachers who are in any of the admin's schools
        $teachers = User::where('role_id', $teacherRole->id)
            ->whereHas('teacherSchools', function($query) use ($schoolIds) {
                $query->whereIn('schools.id', $schoolIds)
                      ->where('school_teacher.is_approved', true);
            })
            ->get();
        
        // Add school info to each teacher
        foreach ($teachers as $teacher) {
            // Get the first school this teacher is associated with (from this admin's schools)
            $schoolData = $teacher->teacherSchools()
                ->whereIn('schools.id', $schoolIds)
                ->where('school_teacher.is_approved', true)
                ->first();
            
            if ($schoolData) {
                $teacher->school_name = $schoolData->name;
                $teacher->school_id = $schoolData->id;
            }
        }
        
        return view('admin.teachers', compact('teachers', 'schools', 'pendingTeachers'));
    }

    /**
     * Approve a teacher's request to join a school.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveTeacherSchool(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'school_id' => 'required|exists:schools,id',
        ]);
        
        $userId = $request->input('user_id');
        $schoolId = $request->input('school_id');
        
        // Check if the school belongs to this admin
        $school = School::where('id', $schoolId)
            ->where('admin_id', Auth::id())
            ->first();
            
        if (!$school) {
            return back()->with('error', 'Ù„ÙŠØ³ Ù„Ø¯ÙŠÙƒ ØµÙ„Ø§Ø­ÙŠØ© Ù„Ù„ÙˆØµÙˆÙ„ Ø¥Ù„Ù‰ Ù‡Ø°Ù‡ Ø§Ù„Ù…Ø¯Ø±Ø³Ø©');
        }
        
        // Update the approval status
        DB::table('school_teacher')
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->update([
                'is_approved' => true,
                'updated_at' => now()
            ]);
            
        return back()->with('success', 'ØªÙ…Øª Ø§Ù„Ù…ÙˆØ§ÙÙ‚Ø© Ø¹Ù„Ù‰ Ø§Ù†Ø¶Ù…Ø§Ù… Ø§Ù„Ù…Ø¹Ù„Ù… Ù„Ù„Ù…Ø¯Ø±Ø³Ø© Ø¨Ù†Ø¬Ø§Ø­');
    }

    /**
     * Reject a teacher's request to join a school.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectTeacherSchool(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'school_id' => 'required|exists:schools,id',
        ]);
        
        $userId = $request->input('user_id');
        $schoolId = $request->input('school_id');
        
        // Check if the school belongs to this admin
        $school = School::where('id', $schoolId)
            ->where('admin_id', Auth::id())
            ->first();
            
        if (!$school) {
            return back()->with('error', 'Ù„ÙŠØ³ Ù„Ø¯ÙŠÙƒ ØµÙ„Ø§Ø­ÙŠØ© Ù„Ù„ÙˆØµÙˆÙ„ Ø¥Ù„Ù‰ Ù‡Ø°Ù‡ Ø§Ù„Ù…Ø¯Ø±Ø³Ø©');
        }
        
        // Delete the school_teacher relationship
        DB::table('school_teacher')
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->delete();
            
        return back()->with('success', 'ØªÙ… Ø±ÙØ¶ Ø·Ù„Ø¨ Ø§Ù†Ø¶Ù…Ø§Ù… Ø§Ù„Ù…Ø¹Ù„Ù… Ù„Ù„Ù…Ø¯Ø±Ø³Ø© Ø¨Ù†Ø¬Ø§Ø­');
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
        if ($user->role->name !== 'teacher') {
            return back()->with('error', 'Ù‡Ø°Ø§ Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù… Ù„ÙŠØ³ Ù…Ø¹Ù„Ù…Ù‹Ø§');
        }

        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'ØªÙ…Øª Ø§Ù„Ù…ÙˆØ§ÙÙ‚Ø© Ø¹Ù„Ù‰ Ø§Ù„Ù…Ø¹Ù„Ù… Ø¨Ù†Ø¬Ø§Ø­');
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
        if ($user->role->name !== 'teacher') {
            return back()->with('error', 'Ù‡Ø°Ø§ Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù… Ù„ÙŠØ³ Ù…Ø¹Ù„Ù…Ù‹Ø§');
        }
        
        // Get all schools owned by this admin
        $adminSchools = School::where('admin_id', Auth::id())->pluck('id')->toArray();
        
        // Check if the teacher belongs to any of the admin's schools
        $teacherSchools = DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->whereIn('school_id', $adminSchools)
            ->get();
        
        if ($teacherSchools->isEmpty()) {
            return back()->with('error', 'Ù‡Ø°Ø§ Ø§Ù„Ù…Ø¹Ù„Ù… Ù„ÙŠØ³ ÙÙŠ Ø£ÙŠ Ù…Ù† Ù…Ø¯Ø§Ø±Ø³Ùƒ');
        }
        
        // Get all classrooms created by this teacher in admin's schools
        $classrooms = $user->classRooms()
            ->whereIn('school_id', $adminSchools)
            ->get();
        
        // Delete all classrooms (this will cascade to sessions and attendances)
        foreach ($classrooms as $classroom) {
            $classroom->delete();
        }
        
        // Remove teacher from admin's schools
        foreach ($teacherSchools as $teacherSchool) {
            DB::table('school_teacher')
                ->where('user_id', $user->id)
                ->where('school_id', $teacherSchool->school_id)
                ->delete();
        }
        
        return back()->with('success', 'ØªÙ… Ø¥Ø²Ø§Ù„Ø© Ø§Ù„Ù…Ø¹Ù„Ù… Ù…Ù† Ù…Ø¯Ø§Ø±Ø³Ùƒ ÙˆØ­Ø°Ù ÙØµÙˆÙ„Ù‡ Ø¨Ù†Ø¬Ø§Ø­');
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
        if ($user->role->name !== 'teacher') {
            return redirect()->route('admin.teachers')->with('error', 'Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù… Ø§Ù„Ù…Ø­Ø¯Ø¯ Ù„ÙŠØ³ Ù…Ø¹Ù„Ù…Ù‹Ø§');
        }
        
        // Get admin's schools
        $adminSchools = School::where('admin_id', Auth::id())->pluck('id')->toArray();
        
        // Check if the teacher belongs to any of the admin's schools
        $teacherInAdminSchool = DB::table('school_teacher')
            ->where('user_id', $user->id)
            ->whereIn('school_id', $adminSchools)
            ->exists();
        
        if (!$teacherInAdminSchool) {
            return redirect()->route('admin.teachers')->with('error', 'ØºÙŠØ± Ù…ØµØ±Ø­ Ù„Ùƒ Ø¨Ø¹Ø±Ø¶ Ù‡Ø°Ø§ Ø§Ù„Ù…Ø¹Ù„Ù…');
        }
        
        // Get the teacher's schools that the admin manages
        $teacherSchools = School::where('admin_id', Auth::id())
            ->whereHas('teachers', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get();
        
        // Get classrooms created by this teacher in admin's schools
        $classrooms = $user->classRooms()
            ->whereIn('school_id', $adminSchools)
            ->with('school')
            ->get();
        
        return view('admin.show-teacher', compact('user', 'teacherSchools', 'classrooms'));
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
        // Get the admin's single school
        $school = School::where('admin_id', Auth::id())->first();
        
        if (!$school) {
            return view('admin.students', ['students' => [], 'classrooms' => []]);
        }
        
        // Get all classrooms from the school for filtering
        $classrooms = $school->classRooms;
        
        // Get all students from the school
        $students = $school->students()->with('classRooms')->get();
        
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
        // Get the admin's school
        $school = School::where('admin_id', Auth::id())->first();
        
        if (!$school) {
            return back()->with('error', 'Ù„ÙŠØ³ Ù„Ø¯ÙŠÙƒ Ù…Ø¯Ø±Ø³Ø© Ù„Ø¥Ø¯Ø§Ø±Ø© Ø§Ù„Ø·Ù„Ø§Ø¨');
        }
        
        // Find the student
        $student = \App\Models\Student::find($student);
        
        if (!$student) {
            return back()->with('error', 'Ø§Ù„Ø·Ø§Ù„Ø¨ ØºÙŠØ± Ù…ÙˆØ¬ÙˆØ¯');
        }
        
        // Check if the student belongs to the admin's school
        if ($student->school_id !== $school->id) {
            return back()->with('error', 'Ù„ÙŠØ³ Ù„Ø¯ÙŠÙƒ ØµÙ„Ø§Ø­ÙŠØ© Ø­Ø°Ù Ù‡Ø°Ø§ Ø§Ù„Ø·Ø§Ù„Ø¨');
        }
        
        // Detach the student from all classrooms first
        $student->classRooms()->detach();
        
        // Delete the student
        $student->delete();
        
        return back()->with('success', 'ØªÙ… Ø­Ø°Ù Ø§Ù„Ø·Ø§Ù„Ø¨ Ø¨Ù†Ø¬Ø§Ø­');
    }
    
    /**
     * Update the admin's password.
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
            return back()->with('error', 'ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ± Ø§Ù„Ø­Ø§Ù„ÙŠØ© ØºÙŠØ± ØµØ­ÙŠØ­Ø©');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'ØªÙ… ØªØºÙŠÙŠØ± ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ± Ø¨Ù†Ø¬Ø§Ø­');
    }

    /**
     * Update the admin's username.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:4|max:255|unique:users,username,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->save();

        return back()->with('success', 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ø§Ø³Ù… Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù… Ø¨Ù†Ø¬Ø§Ø­');
    }

    /**
     * Update the admin's name.
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
            return back()->withErrors(['current_password' => 'ÙƒÙ„Ù…Ø© Ø§Ù„Ù…Ø±ÙˆØ± Ø§Ù„Ø­Ø§Ù„ÙŠØ© ØºÙŠØ± ØµØ­ÙŠØ­Ø©.']);
        }

        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ø§Ù„Ø§Ø³Ù… Ø¨Ù†Ø¬Ø§Ø­.');
    }

    /**
     * Display a list of classrooms.
     *
     * @return \Illuminate\View\View
     */
    public function classrooms(Request $request)
    {
        // Get the admin's single school
        $school = School::where('admin_id', Auth::id())->first();
        
        if (!$school) {
            return view('admin.classrooms', ['classrooms' => []]);
        }
        
        // Get all classrooms with their teachers and student counts
        $classrooms = $school->classRooms()
            ->with(['teacher', 'school', 'schedules'])
            ->withCount('students')
            ->get();
        
        return view('admin.classrooms', compact('classrooms'));
    }
}

