<?php

// app/Http/Middleware/StudentAuthMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if student is logged in via session
        if (!session('student_logged_in') || !session('student_id')) {
            Log::info('Student auth failed - no session', [
                'student_logged_in' => session('student_logged_in'),
                'student_id' => session('student_id')
            ]);
            return redirect()->route('student.login')->with('error', 'Please log in to access this page.');
        }

        // Check session timeout (2 hours = 7200 seconds)
        $loginTime = session('student_login_time');
        $lastActivity = session('last_activity', $loginTime);
        
        if ($loginTime && (time() - $lastActivity) > 7200) {
            Log::info('Student session expired', [
                'student_id' => session('student_id'),
                'login_time' => $loginTime,
                'last_activity' => $lastActivity
            ]);
            
            session()->flush();
            return redirect()->route('student.login')->with('error', 'Your session has expired. Please log in again.');
        }

        // Update last activity time
        session(['last_activity' => time()]);

        return $next($request);
    }
}