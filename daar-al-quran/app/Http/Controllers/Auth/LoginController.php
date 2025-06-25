<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->input('login'),
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Check if email is verified first
            if (!$user->hasVerifiedEmail()) {
                $email = $user->email;
                Auth::logout();
                return redirect()->route('verification.notice')->with('email', $email);
            }

            if (!$user->is_approved && $user->role->name != 'moderator') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'حسابك لم يتم الموافقة عليه بعد من قبل الإدارة');
            }

            // Redirect based on role
            if ($user->role->name === 'moderator') {
                return redirect()->route('moderator.dashboard');
            } elseif ($user->role->name === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role->name === 'teacher') {
                return redirect()->route('teacher.dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'login' => 'البيانات المدخلة غير صحيحة.',
        ])->withInput($request->only('login'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
