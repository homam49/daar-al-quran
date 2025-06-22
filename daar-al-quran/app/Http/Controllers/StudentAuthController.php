<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Message;
use App\Notifications\StudentVerifyEmail;

class StudentAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:student')->except(['showLoginForm', 'login']);
    }

    /**
     * Show the student login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        
        return view('student.auth.login');
    }

    /**
     * Handle a login request from the student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // Check if the login is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find the student by email or username
        $student = Student::where($loginField, $request->login)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return back()->withErrors([
                'login' => 'البيانات المدخلة غير صحيحة.',
            ]);
        }

        // Login the student
        Auth::guard('student')->login($student);

        // Check if this is the first login
        if ($student->first_login) {
            return redirect()->route('student.complete-profile')
                ->with('warning', 'مرحباً بك في أول تسجيل دخول لك. يرجى استكمال ملفك الشخصي وتحديث بياناتك.');
        }

        // Check if email needs verification - ALWAYS redirect to profile page if not verified
        if ($student->email && !$student->email_verified_at) {
            return redirect()->route('student.complete-profile')
                ->with('warning', 'البريد الإلكتروني غير مؤكد. يرجى التحقق من البريد أو تغييره إذا كان خطأ.');
        }

        return redirect()->route('student.dashboard');
    }

    /**
     * Log the student out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }

    /**
     * Show the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        
        // Check if this is a first login or email is not set
        if ($student->first_login || !$student->email) {
            return redirect()->route('student.complete-profile')
                ->with('warning', 'يرجى استكمال ملفك الشخصي وإضافة بريدك الإلكتروني لاستخدام النظام');
        }
        
        // Check if email is verified - redirect to profile page instead of verification notice
        if ($student->email && !$student->email_verified_at) {
            return redirect()->route('student.complete-profile')
                ->with('warning', 'البريد الإلكتروني غير مؤكد. يرجى التحقق من البريد أو تغييره إذا كان خطأ.');
        }
        
        $attendances = Attendance::where('student_id', $student->id)
            ->with('classSession')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $messages = Message::where('student_id', $student->id)
            ->orWhere(function($query) use ($student) {
                $query->whereIn('class_room_id', $student->classRooms->pluck('id'))
                    ->where('type', 'class');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('student.dashboard', compact('student', 'attendances', 'messages'));
    }
    
    /**
     * Show the complete profile form.
     *
     * @return \Illuminate\View\View
     */
    public function showCompleteProfileForm()
    {
        $student = Auth::guard('student')->user();
        
        // Add a warning message about email verification if needed
        if ($student->email && !$student->email_verified_at) {
            session()->flash('warning', 'البريد الإلكتروني غير مؤكد. يرجى التحقق من البريد أو تغييره إذا أدخلت بريدًا خاطئًا.');
        }
        
        return view('student.complete-profile', compact('student'));
    }
    
    /**
     * Update the student's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $request->validate([
            'email' => 'required|email|unique:students,email,' . $student->id,
            'username' => 'required|string|min:4|unique:students,username,' . $student->id,
            'phone' => ['nullable', 'regex:/^07[7-9][0-9]{7}$/'],
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);
        
        $updateData = [
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'address' => $request->address,
            'first_login' => false,
        ];
        
        // Handle email change - reset verification if email changed
        $emailChanged = $student->email !== $request->email;
        if ($emailChanged) {
            $updateData['email_verified_at'] = null;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }
        
        $student->update($updateData);
        
        // Update the session
        Auth::guard('student')->login($student);
        
        // Send verification email if email changed or added
        if ($emailChanged) {
            $student->notify(new StudentVerifyEmail($student));
            return redirect()->route('student.complete-profile')
                ->with('success', 'تم تحديث البيانات بنجاح. تم إرسال رابط تأكيد إلى بريدك الإلكتروني، يرجى التحقق منه لتأكيد عنوانك.');
        }
        
        // If email is verified, go to dashboard. Otherwise stay on complete profile page
        if ($student->email_verified_at) {
            return redirect()->route('student.dashboard')
                ->with('success', 'تم تحديث البيانات بنجاح');
        } else {
            return redirect()->route('student.complete-profile')
                ->with('warning', 'يرجى التحقق من بريدك الإلكتروني لتأكيد عنوانك قبل الوصول إلى لوحة التحكم.');
        }
    }
}
