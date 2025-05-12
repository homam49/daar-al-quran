<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UnifiedProfileController extends Controller
{
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        
        // Determine the user's role and set the appropriate variables
        $role = $user->role->name;
        
        switch ($role) {
            case 'admin':
                $layout = 'admin';
                $contentSection = 'admin-content';
                $passwordUpdateRoute = route('admin.password.update');
                $nameUpdateRoute = route('admin.name.update');
                break;
            case 'teacher':
                $layout = 'teacher';
                $contentSection = 'teacher-content';
                $passwordUpdateRoute = route('teacher.password.update');
                $nameUpdateRoute = route('teacher.name.update');
                break;
            case 'moderator':
            default:
                $layout = 'moderator';
                $contentSection = 'moderator-content';
                $passwordUpdateRoute = route('profile.update'); 
                $nameUpdateRoute = route('profile.update');
                break;
        }
        
        // Load related data for role-specific information
        if ($role === 'admin') {
            $user->load('adminSchools');
        } elseif ($role === 'teacher') {
            $user->load('teacherSchools', 'classRooms');
        }
        
        return view('profile.unified', compact(
            'user', 
            'layout', 
            'contentSection', 
            'passwordUpdateRoute', 
            'nameUpdateRoute'
        ));
    }

    /**
     * Display the student's profile.
     *
     * @return \Illuminate\View\View
     */
    public function showStudent()
    {
        $student = Auth::guard('student')->user();
        $student->load('classRooms'); // Eager load relationships
        
        // Set variables for the unified view
        $layout = 'student';
        $contentSection = 'student-content';
        $passwordUpdateRoute = route('student.profile.password.update');
        
        return view('profile.unified', compact(
            'student',
            'layout',
            'contentSection',
            'passwordUpdateRoute'
        ));
    }
    
    /**
     * Update the user's password.
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

        return $this->getRedirectResponse($user, 'تم تغيير كلمة المرور بنجاح');
    }
    
    /**
     * Update the student's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStudentPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $student = Auth::guard('student')->user();

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        $student->password = Hash::make($request->password);
        $student->save();

        return redirect()->route('student.profile')->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
    
    /**
     * Update the user's name.
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

        return $this->getRedirectResponse($user, 'تم تحديث الاسم بنجاح');
    }
    
    /**
     * Update the user's phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->phone = $request->phone;
        $user->save();

        return $this->getRedirectResponse($user, 'تم تحديث رقم الهاتف بنجاح');
    }
    
    /**
     * Update the user's address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAddress(Request $request)
    {
        $request->validate([
            'address' => 'nullable|string|max:255',
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->address = $request->address;
        $user->save();

        return $this->getRedirectResponse($user, 'تم تحديث العنوان بنجاح');
    }
    
    /**
     * Update the user's personal information (name, phone, address) in one go.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePersonalInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return $this->getRedirectResponse($user, 'تم تحديث البيانات الشخصية بنجاح');
    }
    
    /**
     * Update the student's information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStudentInfo(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'current_password' => ['required', function ($attribute, $value, $fail) use ($student) {
                if (!Hash::check($value, $student->password)) {
                    $fail('كلمة المرور الحالية غير صحيحة.');
                }
            }],
        ]);
        
        // Update student data
        $student->phone = $request->phone;
        $student->address = $request->address;
        $student->save();
        
        return redirect()->route('student.profile')->with('success', 'تم تحديث البيانات الشخصية بنجاح');
    }
    
    /**
     * Get the appropriate redirect response based on user role.
     *
     * @param  \App\Models\User  $user
     * @param  string  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function getRedirectResponse($user, $message = 'تم تحديث الملف الشخصي بنجاح')
    {
        $role = $user->role->name;
        
        switch ($role) {
            case 'admin':
                $redirectRoute = 'admin.profile';
                break;
            case 'teacher':
                $redirectRoute = 'teacher.profile';
                break;
            case 'moderator':
            default:
                $redirectRoute = 'moderator.profile';
                break;
        }
        
        return redirect()->route($redirectRoute)->with('success', $message);
    }
} 