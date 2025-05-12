<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['verify', 'show', 'resendForGuest']);
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend', 'resendForGuest');
    }
    
    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        if ($request->user()) {
            return $request->user()->hasVerifiedEmail()
                ? redirect($this->redirectPath())
                : view('auth.verify');
        }
        
        // Show verification page for guests
        return view('auth.verify-guest');
    }
    
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $user = $request->user();
        
        // If user is not logged in, retrieve user by ID
        if (!$user) {
            $userId = $request->route('id');
            $user = \App\Models\User::find($userId);
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'مستخدم غير موجود');
            }
        }

        if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'تم التحقق من البريد الإلكتروني بالفعل. يمكنك تسجيل الدخول الآن.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Check if user is logged in
        if (Auth::check()) {
            // Email verified but still needs moderator approval
            if (!$user->is_approved) {
                auth()->logout();
                return redirect()->route('login')
                    ->with('success', 'تم تأكيد البريد الإلكتروني بنجاح. يرجى الانتظار حتى تتم مراجعة حسابك من قبل المشرف.');
            }
            
            return redirect($this->redirectPath())->with('verified', true);
        } else {
            // User wasn't logged in, redirect to login
            return redirect()->route('login')
                ->with('success', 'تم تأكيد البريد الإلكتروني بنجاح. يمكنك الآن تسجيل الدخول.');
        }
    }
    
    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
    
    /**
     * Resend verification email for non-logged in users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendForGuest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'لم يتم العثور على مستخدم بهذا البريد الإلكتروني.');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('info', 'البريد الإلكتروني محقق بالفعل. يرجى تسجيل الدخول.');
        }
        
        $user->sendEmailVerificationNotification();
        
        return back()->with('resent', true);
    }
    
    /**
     * Get the post email verification redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        $user = auth()->user();
        
        if ($user->role->name === 'moderator') {
            return route('moderator.dashboard');
        } elseif ($user->role->name === 'admin') {
            return route('admin.dashboard');
        } elseif ($user->role->name === 'teacher') {
            return route('teacher.dashboard');
        }
        
        return $this->redirectTo;
    }
    
    /**
     * Update user's email address and resend verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);
        
        $user = $request->user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();
        
        $user->sendEmailVerificationNotification();
        
        return back()->with('email_updated', true);
    }
}
