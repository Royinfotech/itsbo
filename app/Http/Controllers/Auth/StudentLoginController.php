<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentLoginController extends Controller
{
    public function showStudentLogin()
    {
        return view('auth.student-login');
    }

    public function studentLogin(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'password' => 'required'
        ]);

        try {
            // Check if student exists and credentials are valid
            $student = Student::where('student_id', $request->student_id)->first();

            if (!$student) {
                Log::warning('Student ID not found', ['student_id' => $request->student_id]);
                return back()
                    ->withErrors(['error' => 'Student ID does not exist'])
                    ->withInput($request->except('password'));
            }

            // Check student status first
            if ($student->status !== 'active') {
                $statusMessage = match($student->status) {
                    'pending' => 'Your account is still pending approval',
                    'declined' => 'Your account has been declined. Please contact the administrator',
                    default => 'Your account is not active'
                };
                
                Log::info('Inactive student attempted login', [
                    'student_id' => $student->student_id,
                    'status' => $student->status
                ]);
                
                return back()
                    ->withErrors(['error' => $statusMessage])
                    ->withInput($request->except('password'));
            }

            // Verify password
            if (!Hash::check($request->password, $student->password)) {
                Log::warning('Invalid password attempt', ['student_id' => $request->student_id]);
                return back()
                    ->withErrors(['error' => 'Incorrect password'])
                    ->withInput($request->except('password'));
            }

            // Login successful
            session([
                'student_id' => $student->student_id,
                'student_name' => $student->student_name,
                'logged_in' => true
            ]);

            Log::info('Student logged in successfully', [
                'student_id' => $student->student_id
            ]);

            return redirect()->route('student.dashboard', [
                'student_id' => $student->student_id
            ]);

        } catch (\Exception $e) {
            Log::error('Login error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withErrors(['error' => 'System error occurred. Please try again later'])
                ->withInput($request->except('password'));
        }
    }
}

