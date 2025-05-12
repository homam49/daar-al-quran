<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class StudentVerificationController extends Controller
{
    /**
     * Where to redirect students after verification.
     *
     * @var string
     */
    protected $redirectTo = '/student/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:student');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    
    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return $request->user()->email_verified_at
            ? redirect($this->redirectPath())
            : view('auth.student-verify');
    }
    
    /**
     * Mark the authenticated student's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        if (! hash_equals((string) $id, (string) $request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->email))) {
            throw new AuthorizationException;
        }

        if ($request->user()->email_verified_at) {
            return redirect($this->redirectPath());
        }

        $request->user()->email_verified_at = now();
        $request->user()->save();

        return redirect($this->redirectPath())->with('verified', true);
    }
    
    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->email_verified_at) {
            return redirect($this->redirectPath());
        }

        $student = $request->user();
        $student->notify(new \App\Notifications\StudentVerifyEmail($student));

        return back()->with('resent', true);
    }
    
    /**
     * Get the post email verification redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        return route('student.dashboard');
    }
    
    /**
     * Update student's email address and resend verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:students,email,' . $request->user()->id],
        ]);
        
        $student = $request->user();
        $student->email = $request->email;
        $student->email_verified_at = null;
        $student->save();
        
        $student->notify(new \App\Notifications\StudentVerifyEmail($student));
        
        return back()->with('email_updated', true);
    }
}
