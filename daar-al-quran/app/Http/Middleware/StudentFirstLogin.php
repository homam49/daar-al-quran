<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentFirstLogin
{
    /**
     * Handle an incoming request, checking if student has completed their first login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $student = Auth::guard('student')->user();
        
        // Redirect to complete profile if it's first login, email is missing, or email is not verified
        if ($student && ($student->first_login || !$student->email || !$student->email_verified_at)) {
            // Allow the route to complete-profile
            if ($request->routeIs('student.complete-profile') || $request->routeIs('student.update-profile')) {
                return $next($request);
            }
            
            // Also allow email verification routes
            if ($request->routeIs('student.verification.notice') || 
                $request->routeIs('student.verification.verify') || 
                $request->routeIs('student.verification.resend')) {
                return $next($request);
            }
            
            return redirect()->route('student.complete-profile')
                ->with('warning', 'يرجى استكمال ملفك الشخصي وتأكيد بريدك الإلكتروني لاستخدام النظام');
        }

        return $next($request);
    }
} 