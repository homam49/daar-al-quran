<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Check if user exists
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if email is verified first
        if (!$user->hasVerifiedEmail()) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'يرجى التحقق من البريد الإلكتروني أولا قبل الدخول إلى النظام.');
        }
        
        // Check if user is approved by moderator
        if (!$user->is_approved) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'حسابك لم يتم الموافقة عليه بعد. يرجى الانتظار حتى تتم الموافقة من قبل المشرف.');
        }

        return $next($request);
    }
}
