<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            Log::warning('Unauthenticated user attempting to access protected route');
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        Log::info('Role check:', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_role' => $role
        ]);

        if (strtolower($user->role) !== strtolower($role)) {
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_role' => $role
            ]);
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}