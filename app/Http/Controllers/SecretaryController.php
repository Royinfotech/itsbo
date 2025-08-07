<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Event;
use App\Models\Officer;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\StudentApprovedMail;
use App\Mail\StudentApprovalMail;
use Illuminate\Support\Facades\Storage;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\StudentUser;



class SecretaryController extends Controller
{
    public function secretary()
    {
        try {
            // Get secretary data from officers table
            $secretary = Officer::where('position', 'Secretary')->first();
            
            return view("Secretary.Secretary", compact('secretary')); 
        } catch (\Exception $e) {
            Log::error('Error fetching secretary data: ' . $e->getMessage());
            return view("Secretary.Secretary", ['secretary' => null]);
        }
    }
    public function orgstruct()
{
    try {
        // Get current school year with detailed logging
        $currentSchoolYear = SchoolYear::where('is_open', true)->first();
        Log::info('Current School Year Data:', ['schoolYear' => $currentSchoolYear]);

        // Get positions with validation
        $positions = [];
        if ($currentSchoolYear && is_array($currentSchoolYear->open_positions)) {
            $positions = $currentSchoolYear->open_positions;
            Log::info('Positions found:', ['positions' => $positions]);
        }
        
        // Get officers with position mapping
        $officers = [];
        if ($currentSchoolYear && !empty($positions)) {
            foreach ($positions as $position) {
                $positionKey = strtolower(str_replace(' ', '', $position));
                $officer = Officer::where('position', $position)
                    ->where('school_year_id', $currentSchoolYear->id)
                    ->first();
                
                if ($officer) {
                    $officers[$positionKey] = $officer;
                    Log::info("Officer found for position: {$position}", ['officer' => $officer]);
                } else {
                    Log::info("No officer found for position: {$position}");
                }
            }
        }

        // Create the view with compact for cleaner code
        return view('Secretary.orgstruct', compact('positions', 'officers', 'currentSchoolYear'));

    } catch (\Exception $e) {
        Log::error('Error in Secretary orgstruct:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return view('Secretary.orgstruct', [
            'positions' => [],
            'officers' => [],
            'currentSchoolYear' => null,
            'error' => 'Failed to load organizational structure'
        ]);
    }
}
    public function events()
    {
        return view("Secretary.Event"); 
    }
    public function storeEvent(Request $request)
    {
        try {
            $request->validate([
                'event_name' => 'required|string|max:255',
                'event_date' => 'required|date',
                'time_duration' => 'required|string',
                'event_location' => 'required|string'
            ]);

            $event = Event::create([
                'event_name' => $request->event_name,
                'event_date' => $request->event_date,
                'time_duration' => $request->time_duration,
                'event_location' => $request->event_location
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'event' => $event
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Event creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEvents()
    {
        try {
            $events = Event::orderBy('event_date', 'asc')
                ->get()
                ->map(function($event) {
                    return [
                        'id' => $event->id,
                        'event_name' => $event->event_name,
                        'event_date' => Carbon::parse($event->event_date)->format('Y-m-d'),
                        'time_duration' => $event->time_duration,
                        'event_location' => $event->event_location,
                    ];
                });

            return response()->json([
                'success' => true,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getEvent($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json([
                'success' => true,
                'event' => $event
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function printAttendanceReport($eventId)
{
    try {
        $event = Event::findOrFail($eventId);

        // Get only ACTIVE students with their attendance records for this event
        $attendanceData = DB::table('students')
            ->leftJoin('attendance', function($join) use ($eventId) {
                $join->on('students.id', '=', 'attendance.student_id')
                     ->where('attendance.event_id', '=', $eventId);
            })
            ->where('students.status', '=', 'active') // Only active students
            ->select([
                'students.id',
                'students.student_id', 
                'students.student_name',
                'students.year_level',
                'attendance.am_in',
                'attendance.am_out', 
                'attendance.pm_in',
                'attendance.pm_out',
                'attendance.created_at as attendance_date'
            ])
            ->orderBy('students.year_level')
            ->orderBy('students.student_name')
            ->get();

        $totalActiveStudents = $attendanceData->count();

        return view('attendance.print-report', compact('event', 'attendanceData', 'totalActiveStudents'));

    } catch (\Exception $e) {
        Log::error('Error generating attendance report: ' . $e->getMessage(), [
            'event_id' => $eventId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to generate attendance report: ' . $e->getMessage());
    }
}
    // List students pending approval
    public function pendingStudents()
{
    $students = Student::where('status', 'pending')->get();
    return view('Secretary.Approvestudents', compact('students'));
}

// Approve student
public function approveStudent($id)
{
    DB::beginTransaction();
    try {
        $student = Student::findOrFail($id);
        
        // Create student user account
        $studentUser = StudentUser::create([
            'student_id' => $student->student_id,
            'email' => $student->email,
            'password' => $student->password,
        ]);

        // Update student status
        $student->update([
            'status' => 'active',
            'approved_at' => now()
        ]);

        // Send approval email
        try {
            Mail::to($student->email)->send(new StudentApprovalMail($student));
            
            Log::info('Approval email sent', [
                'student_id' => $student->student_id,
                'email' => $student->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval email', [
                'error' => $e->getMessage(),
                'student_id' => $student->student_id
            ]);
            // Continue execution even if email fails
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Student approved and notification sent'
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Student approval failed', [
            'error' => $e->getMessage(),
            'student_id' => $id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to approve student: ' . $e->getMessage()
        ], 500);
    }
}

public function updateStudent(Request $request, $id)
{
    try {
        $student = Student::findOrFail($id); // Adjust model name if different
        
        $validatedData = $request->validate([
            'student_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'year_level' => 'required|string|max:50',
            'birthdate' => 'required|date',
            'age' => 'required|integer|min:1|max:150',
            'birthplace' => 'required|string|max:255'
        ]);
        
        $student->update($validatedData);
        
        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update student: ' . $e->getMessage()
        ], 500);
    }
}
// Decline student
public function declineStudent($id)
{
    $student = Student::findOrFail($id);
    $student->status = 'declined';
    $student->save();

    return response()->json(['message' => 'Student declined.']);
}

}
