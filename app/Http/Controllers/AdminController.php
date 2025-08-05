<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Officer;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use NoCaptcha\Facades\NoCaptcha;

class AdminController extends Controller
{

    public function admin_manage()
    {
        return view("Admin.Admin_Manage");
    }

    public function orgstruct()
    {
        try {
            // Get current school year and its positions
            $currentSchoolYear = \App\Models\SchoolYear::where('is_open', true)->first();
            $positions = $currentSchoolYear ? $currentSchoolYear->open_positions : [];
            
            // Get all officers grouped by their positions
            $officers = [];
            if ($currentSchoolYear) {
                foreach ($positions as $position) {
                    $positionKey = strtolower(str_replace(' ', '', $position));
                    $officers[$positionKey] = Officer::where('position', $position)
                        ->where('school_year_id', $currentSchoolYear->id)
                        ->first();
                }
            }

            // Debug logging
            Log::info('Current School Year:', ['year' => $currentSchoolYear ? $currentSchoolYear->year : 'None']);
            Log::info('Positions:', ['positions' => $positions]);
            Log::info('Officers:', ['officers' => $officers]);

            return view('Admin.OrgStruct', [
                'positions' => $positions,
                'officers' => $officers,
                'currentSchoolYear' => $currentSchoolYear
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching organizational structure: ' . $e->getMessage());
            return view('Admin.OrgStruct', [
                'positions' => [],
                'officers' => [],
                'error' => 'Failed to load organizational structure'
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    
    public function admin()
    {
        try {
            // Get president data from officers table
            $president = Officer::where('position', 'president')->first();
            
            return view("Admin.Admin", compact('president'));
        } catch (\Exception $e) {
            Log::error('Error fetching President data: ' . $e->getMessage());
            return view("Admin.Admin", ['president' => null]);
        }
    }
    
    public function login(Request $request)
    {
        // Validate the request, including reCAPTCHA
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        // Check if the user exists and is active
        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || $user->status !== 'active') {
            return back()->withErrors(['username' => 'Invalid credentials or account is inactive.']);
        }

        // Attempt to log in
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->intended($user->getDashboardRoute());
        }

        return back()->withErrors(['username' => 'Invalid credentials.']);
    }
    
    public function secdashboard()
    {
        return view("Admin.Secdashboard");
    }
    
    public function itsboOfficers()
    {
        return view("Admin.Orgstruct");
    }

    public function addUser(Request $request)
    {
        try {
            // Log the incoming request
            Log::info('Add User Request:', $request->all());

            // Get user type from request
            $userType = $request->input('user_type', 'system');

            if ($userType === 'student') {
                // Validate for student users
                $validator = Validator::make($request->all(), [
                    'student_id' => 'required|string|max:255|unique:students,student_id',
                    'student_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:students,email',
                    'password' => 'required|string|min:8',
                ]);

                if ($validator->fails()) {
                    Log::error('Student validation failed:', $validator->errors()->toArray());
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

            } else {
                // Validate for system users
                $validator = Validator::make($request->all(), [
                    'username' => 'required|string|max:255|unique:users,username',
                    'password' => 'required|string|min:8',
                    'role' => 'required|in:secretary,treasurer,admin,superadmin'
                ]);

                if ($validator->fails()) {
                    Log::error('System user validation failed:', $validator->errors()->toArray());
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Create system user
                $user = new User();
                $user->username = $request->username;
                $user->password = bcrypt($request->password);
                $user->role = $request->role;
                $user->status = 'active';
                
                if (!$user->save()) {
                    Log::error('Failed to save user to database');
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to save user to database'
                    ], 500);
                }

                Log::info('System user created successfully:', ['user_id' => $user->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'status' => $user->status
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $type = $request->query('type', 'system');

            if ($type === 'student') {
                // Filter students to only show active and inactive statuses
                $students = Student::select('id', 'student_id', 'student_name', 'year_level', 'email', 'status')
                    ->whereIn('status', ['active', 'inactive']) // Only show active and inactive
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function($student) {
                        return [
                            'id' => $student->id,
                            'student_id' => $student->student_id,
                            'student_name' => $student->student_name,
                            'year_level' => $student->year_level,
                            'email' => $student->email,
                            'status' => $student->status ?? 'active',
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $students
                ]);
            } else {
                // System users logic
                $users = User::select('id', 'username', 'role', 'status')
                    ->orderBy('id', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('query');
            $type = $request->input('type', 'system');
            
            if ($type === 'student') {
                $students = Student::select('id', 'student_id', 'student_name', 'email', 'status')
                    ->where(function($q) use ($query) {
                        $q->where('student_name', 'like', "%{$query}%")
                          ->orWhere('student_id', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                    })
                    ->whereIn('status', ['active', 'inactive']) // Only show active and inactive
                    ->orderBy('id', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $students
                ]);
            } else {
                $users = User::select('id', 'username', 'role', 'status')
                    ->where(function($q) use ($query) {
                        $q->where('username', 'like', "%{$query}%")
                          ->orWhere('role', 'like', "%{$query}%");
                    })
                    ->orderBy('id', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error searching users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUserStatus(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'user_id' => 'required|integer',
                'status' => 'required|in:active,inactive',
                'user_type' => 'string|in:system,student'
            ]);

            $userType = $request->input('user_type', 'system');

            if ($userType === 'student') {
                // Find the student
                $student = Student::findOrFail($request->user_id);
                
                // Update status
                $student->status = $request->status;
                $student->save();

                // Log the status change
                Log::info('Student status updated:', [
                    'student_id' => $student->id,
                    'new_status' => $student->status
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Student status updated successfully',
                    'user' => [
                        'id' => $student->id,
                        'student_id' => $student->student_id,
                        'student_name' => $student->student_name,
                        'email' => $student->email,
                        'status' => $student->status
                    ]
                ]);
            } else {
                // Find the system user
                $user = User::findOrFail($request->user_id);
                
                // Check if trying to deactivate a superadmin
                if ($user->role === 'superadmin' && $request->status === 'inactive') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot deactivate a Superadmin user'
                    ], 400);
                }
                
                // Update status
                $user->status = $request->status;
                $user->save();

                // Log the status change
                Log::info('User status updated:', [
                    'user_id' => $user->id,
                    'new_status' => $user->status
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User status updated successfully',
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'status' => $user->status
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showLogin()
    {
        return view('Login');
    }
}