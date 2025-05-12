<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class ModeratorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:moderator']);
    }

    /**
     * Display the moderator dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $totalUsers = User::whereNotNull('email_verified_at')->count();
        $pendingUsers = User::where('is_approved', false)
            ->whereNotNull('email_verified_at')
            ->count();
        $unverifiedUsers = User::where('is_approved', false)
            ->whereNull('email_verified_at')
            ->count();
        $schoolsCount = School::count();
        
        return view('moderator.dashboard', compact('totalUsers', 'pendingUsers', 'unverifiedUsers', 'schoolsCount'));
    }

    /**
     * Display a list of all users.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::with('role')
            ->whereNotNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('moderator.users', compact('users'));
    }

    /**
     * Approve a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveUser(User $user)
    {
        $user->is_approved = true;
        $user->save();

        return back()->with('success', 'تمت الموافقة على المستخدم بنجاح');
    }

    /**
     * Delete a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting other moderators or oneself
        if ($user->role->name === 'moderator') {
            return back()->with('error', 'لا يمكن حذف المشرفين');
        }

        $user->delete();

        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Display pending user approvals.
     *
     * @return \Illuminate\View\View
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('is_approved', false)
            ->whereNotNull('email_verified_at')
            ->with('role')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $unverifiedUsers = User::where('is_approved', false)
            ->whereNull('email_verified_at')
            ->count();
            
        return view('moderator.pending-users', compact('pendingUsers', 'unverifiedUsers'));
    }

    /**
     * Display a list of all schools.
     *
     * @return \Illuminate\View\View
     */
    public function schools()
    {
        $schools = School::with('admin')->latest()->get();
        return view('moderator.schools', compact('schools'));
    }

    /**
     * Display form to create a new school.
     *
     * @return \Illuminate\View\View
     */
    public function createSchool()
    {
        return view('moderator.create-school');
    }

    /**
     * Display system logs.
     *
     * @return \Illuminate\View\View
     */
    public function systemLogs()
    {
        // You may need to implement actual log retrieval based on your system
        $logs = [];
        
        return view('moderator.system-logs', compact('logs'));
    }

    /**
     * Display form to create a new user.
     *
     * @return \Illuminate\View\View
     */
    public function createUser()
    {
        $roles = Role::all();
        
        return view('moderator.create-user', compact('roles'));
    }

    /**
     * Display reports generation page.
     *
     * @return \Illuminate\View\View
     */
    public function generateReports()
    {
        return view('moderator.reports');
    }

    /**
     * Display system backup page.
     *
     * @return \Illuminate\View\View
     */
    public function systemBackup()
    {
        return view('moderator.backup');
    }

    /**
     * Show a specific user's details.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function showUser(User $user)
    {
        return view('moderator.user-details', compact('user'));
    }

    /**
     * Reject a user's registration.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectUser(User $user)
    {
        // Prevent rejecting other moderators
        if ($user->role->name === 'moderator') {
            return back()->with('error', 'لا يمكن رفض المشرفين');
        }

        $user->delete();

        return back()->with('success', 'تم رفض المستخدم بنجاح');
    }

    /**
     * Display the school details
     */
    public function showSchool(School $school)
    {
        return view('moderator.show-school', compact('school'));
    }

    /**
     * Show the form for editing a school
     */
    public function editSchool(School $school)
    {
        $admins = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->where('is_approved', true)->get();
        
        return view('moderator.edit-school', compact('school', 'admins'));
    }

    /**
     * Update the school in storage
     */
    public function updateSchool(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $school->update($validated);

        return redirect()->route('moderator.schools')->with('success', 'تم تحديث بيانات المدرسة بنجاح');
    }

    /**
     * Store a newly created school
     */
    public function storeSchool(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        // Generate a unique school code
        $code = 'SCH-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $validated['code'] = $code;

        School::create($validated);

        return redirect()->route('moderator.schools')->with('success', 'تم إضافة المدرسة بنجاح');
    }

    /**
     * Delete a school
     */
    public function deleteSchool(School $school)
    {
        // Check if school has associated teachers or students
        if ($school->teachers()->count() > 0 || $school->students()->count() > 0) {
            return redirect()->route('moderator.schools')->with('error', 'لا يمكن حذف المدرسة لأنها تحتوي على معلمين أو طلاب');
        }

        $school->delete();
        return redirect()->route('moderator.schools')->with('success', 'تم حذف المدرسة بنجاح');
    }

    /**
     * Display the reports page
     */
    public function reports()
    {
        return view('moderator.reports');
    }
}
