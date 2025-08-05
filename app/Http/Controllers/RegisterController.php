<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        Log::info('Registration attempt starting');

        try {
            $validated = $request->validate([
                'student_name' => 'required|string|max:255',
                'student_id' => 'required|string|unique:students,student_id',
                'year_level' => 'required|integer|between:1,4',
                'birthdate' => 'required|date|before:today',
                'age' => 'required|integer|min:16',
                'birthplace' => 'required|string',
                'email' => 'required|email|unique:students,email',
                'username' => 'required|string|unique:students,username',
                'password' => 'required|string|min:8|confirmed',
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:10048' // Changed from nullable to required
            ], [
                'student_id.unique' => 'This iLearn ID is already registered',
                'email.unique' => 'This email is already registered',
                'username.unique' => 'This username is already taken',
                'password.min' => 'Password must be at least 8 characters',
                'password.confirmed' => 'Passwords do not match',
                'photo.required' => 'Please upload your photo to complete registration', // Added required message
                'photo.image' => 'Please upload a valid image file',
                'photo.mimes' => 'Photo must be a JPEG, PNG, or JPG file',
                'photo.max' => 'Photo size must not exceed 10MB'
            ]);

            // Format student name to proper case (First Letter Of Each Word Capitalized)
            $formattedName = $this->formatStudentName($request->student_name);

            // Handle photo upload (now required)
            $photoPath = $request->file('photo')->store('photos', 'public');

            // Create student record with formatted name
            $student = Student::create([
                'student_name' => $formattedName, // Use formatted name here
                'student_id' => $request->student_id,
                'year_level' => $request->year_level,
                'birthdate' => $request->birthdate,
                'age' => $request->age,
                'birthplace' => $request->birthplace,
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'photo' => $photoPath,
                'status' => 'pending'
            ]);

            Log::info('Student saved successfully', ['student_id' => $student->id]);

            return view('auth.registration-success', ['email' => $student->email]);

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['registration' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    private function formatStudentName($name)
    {
        // Remove extra spaces and convert to lowercase first
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        // Convert to title case (first letter of each word capitalized)
        return Str::title(strtolower($name));
    }
}