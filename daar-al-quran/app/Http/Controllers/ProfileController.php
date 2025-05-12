<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Check if this is a name-only update
        if ($request->has('name') && !$request->has('email')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('كلمة المرور الحالية غير صحيحة.');
                    }
                }],
            ]);
            
            $user->name = $validated['name'];
            $user->save();
            
            return $this->getRedirectResponse($user, 'تم تحديث الاسم بنجاح');
        }
        
        /* 
        // Username update has been disabled as usernames should not be changeable
        if ($request->has('username') && !$request->has('email')) {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('كلمة المرور الحالية غير صحيحة.');
                    }
                }],
            ]);
            
            $user->username = $validated['username'];
            $user->save();
            
            return $this->getRedirectResponse($user, 'تم تحديث اسم المستخدم بنجاح');
        }
        */
        
        // Check if this is a password-only update 
        if ($request->has('password') && !$request->has('email') && !$request->has('name')) {
            $validated = $request->validate([
                'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('كلمة المرور الحالية غير صحيحة.');
                    }
                }],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            
            $user->password = Hash::make($validated['password']);
            $user->save();
            
            return $this->getRedirectResponse($user, 'تم تغيير كلمة المرور بنجاح');
        }
        
        // This is a full profile update
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('كلمة المرور الحالية غير صحيحة.');
                }
            }],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Update basic profile information
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        
        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();
        
        return $this->getRedirectResponse($user, 'تم تحديث الملف الشخصي بنجاح');
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
        $redirectRoute = 'moderator.profile';
        if ($user->role && $user->role->name === 'admin') {
            $redirectRoute = 'admin.profile';
        } elseif ($user->role && $user->role->name === 'teacher') {
            $redirectRoute = 'teacher.profile';
        }
        
        return redirect()->route($redirectRoute)->with('success', $message);
    }
} 