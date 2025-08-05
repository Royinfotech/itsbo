<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStudentSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('student_id')) {
            return redirect()->route('student.login')
                ->with('error', 'Please login to access student dashboard');
        }
        return $next($request);
    }
}