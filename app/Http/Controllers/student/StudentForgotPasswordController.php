<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Student; // Adjust this to your Student model
use App\Mail\StudentNewPasswordMail; // We'll create this mail class

class StudentForgotPasswordController extends Controller
{
    public function sendNewPassword(Request $request)
    {
        try {
            // Validate the email
            $request->validate([
                'email' => 'required|email'
            ]);

            // Find student by email
            $student = Student::where('email', $request->email)->first();

            if (!$student) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No student found with this email address.'
                ], 404);
            }

            // Generate new 8-character password
            $newPassword = $this->generateRandomPassword();

            // Update student password
            $student->password = Hash::make($newPassword);
            $student->save();

            // Send email with new password
            try {
                Mail::to($student->email)->send(new StudentNewPasswordMail($student, $newPassword));
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'New password has been sent to your email address.'
                ]);
                
            } catch (\Exception $e) {
                // Log the email error but don't expose it to user
                \Log::error('Failed to send password reset email: ' . $e->getMessage());
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send email. Please contact administrator.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Generate random 8-character password with mixed case, numbers, and symbols
     */
    private function generateRandomPassword()
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*';

        // Ensure at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill remaining 4 characters randomly
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 8; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }
}