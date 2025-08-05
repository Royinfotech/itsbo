<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;  // Add this import
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentUser;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLogin()
    {
        return view('Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        try {
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();
                
                \Log::info('Login attempt:', [
                    'username' => $user->username,
                    'role' => $user->role
                ]);

                // Prevent students from using admin login
                if ($user->role === 'student') {
                    Auth::logout();
                    return back()->withErrors([
                        'username' => 'Please use the student login page.'
                    ]);
                }

                return redirect($user->getDashboardRoute());
            }

            return back()->withErrors([
                'username' => 'Invalid credentials.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Login error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'username' => 'An error occurred during login.'
            ]);
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
