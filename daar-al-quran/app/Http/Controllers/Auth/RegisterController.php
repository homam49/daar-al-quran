<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $roles = Role::whereIn('name', ['admin', 'teacher'])->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users|alpha_dash',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'phone' => ['required', 'regex:/^07[7-9][0-9]{7}$/'],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يكون رقم أردني صحيح (07XXXXXXX)'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_approved' => true, // Auto-approve users by default for better UX
            'email_verified_at' => null, // Explicitly set to null to require verification
        ]);

        // Check if the user is registering as a moderator (which is not allowed)
        $role = Role::find($request->role_id);
        if ($role->name === 'moderator') {
            return redirect()->route('register')
                ->with('error', 'لا يمكن التسجيل كمشرف');
        }

        try {
            // Fire registered event to trigger verification email
            event(new Registered($user));

            // Log the user in so they can see the verification notice
            Auth::login($user);

            // Redirect to verification notice page with appropriate message
            return redirect()->route('verification.notice')
                ->with('success', 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني أولا، ثم سيتم مراجعة طلبك من قبل المشرف.');
        } catch (\Exception $e) {
            // Delete the user if email sending fails
            $user->delete();
            
            return redirect()->route('register')
                ->with('error', 'فشل إرسال بريد التحقق. يرجى التأكد من صحة عنوان البريد الإلكتروني والمحاولة مرة أخرى.');
        }
    }
}
