<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\QR;
use App\Models\User;
use App\Models\SchoolYear;

class AttendanceQRController extends Controller
{
    // Map for scan types
    private $scanTypes = [
        'am_in', 'am_out', 'pm_in', 'pm_out'
    ];

    // Fetch Events
    public function getEvents()
    {
        try {
            // Check for active school year
            $activeSchoolYear = $this->getActiveSchoolYear();

            $events = Event::select('event_id', 'event_name', 'event_location', 'event_date', 'time_duration', 'is_finished')
                         ->where('school_year_id', $activeSchoolYear->id)
                         ->orderBy('event_date', 'desc')
                         ->get();

            Log::info('Fetched events:', ['count' => $events->count()]);

            return response()->json([
                'success' => true,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching events:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events'
            ], 500);
        }
    }

    // Secretary opens a scan type for an event
    public function openScanType(Request $request)
    {
        try {
            // Check for active school year
            $activeSchoolYear = $this->getActiveSchoolYear();

            $request->validate([
                'event_id' => 'required|exists:events,event_id',
                'scan_type' => 'required|in:am_in,am_out,pm_in,pm_out',
            ]);

            // Get the event to verify it belongs to current school year and check duration
            $event = Event::findOrFail($request->event_id);
            
            if ($event->school_year_id !== $activeSchoolYear->id) {
                throw new \Exception('This event belongs to a different school year.');
            }

            // Check if event is finished
            if ($event->is_finished) {
                return response()->json([
                    'success' => false,
                    'message' => 'This event has been marked as finished'
                ], 403);
            }

            // Validate scan type against event duration
            if (!$this->isValidScanTypeForEvent($event, $request->scan_type)) {
                $durationText = $event->time_duration;
                return response()->json([
                    'success' => false,
                    'message' => "Scan type '{$request->scan_type}' is not valid for {$durationText} events"
                ], 400);
            }

            // Update the event's open scan type
            $event->update(['open_scan_type' => $request->scan_type]);

            Log::info('Scan type opened:', [
                'event_id' => $request->event_id,
                'scan_type' => $request->scan_type,
                'time_duration' => $event->time_duration
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Scan type ' . strtoupper(str_replace('_', ' ', $request->scan_type)) . ' opened successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error opening scan type:', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id ?? null,
                'scan_type' => $request->scan_type ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Secretary closes scan type for an event
    public function closeScanType(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,event_id',
            ]);

            $event = Event::findOrFail($request->event_id);
            
            // Check if event is finished
            if ($event->is_finished) {
                return response()->json([
                    'success' => false,
                    'message' => 'This event has been marked as finished'
                ], 403);
            }

            if (!$event->open_scan_type) {
                return response()->json([
                    'success' => false,
                    'message' => 'No scan type is currently open for this event'
                ], 400);
            }

            $previousScanType = $event->open_scan_type;
            $event->update(['open_scan_type' => null]);

            Log::info('Scan type closed:', [
                'event_id' => $request->event_id,
                'previous_scan_type' => $previousScanType
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Scan type ' . strtoupper(str_replace('_', ' ', $previousScanType)) . ' closed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error closing scan type:', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get open scan type for an event
    public function getOpenScanType(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,event_id'
            ]);

            $event = Event::findOrFail($request->event_id);
            
            return response()->json([
                'success' => true,
                'open_scan_type' => $event->open_scan_type,
                'event_finished' => $event->is_finished
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting open scan type:', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'open_scan_type' => null
            ], 500);
        }
    }

    // Check for active school year first
    private function getActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_open', true)->first();
        if (!$activeSchoolYear) {
            throw new \Exception('No active school year found. Please open a school year first.');
        }
        return $activeSchoolYear;
    }

    // Validate scan type against event duration
    private function isValidScanTypeForEvent(Event $event, string $scanType): bool
    {
        switch ($event->time_duration) {
            case 'Whole Day':
                return in_array($scanType, ['am_in', 'am_out', 'pm_in', 'pm_out']);
            case 'Half Day: Morning':
                return in_array($scanType, ['am_in', 'am_out']);
            case 'Half Day: Afternoon':
                return in_array($scanType, ['pm_in', 'pm_out']);
            default:
                return false;
        }
    }

    // QR scan endpoint
    public function scanQr(Request $request)
    {
        try {
            // Check for active school year first
            $activeSchoolYear = $this->getActiveSchoolYear();
            
            Log::info('Received scan request:', [
                'request' => $request->all(),
                'school_year' => $activeSchoolYear->year
            ]);

            // Validate required fields
            $request->validate([
                'student_id' => 'required',
                'event_id' => 'required|exists:events,event_id'
            ]);

            // Parse the student_id if it's JSON
            $studentId = $request->student_id;
            if (is_string($studentId) && (str_starts_with($studentId, '{') || str_starts_with($studentId, '['))) {
                try {
                    $decodedData = json_decode($studentId, true);
                    $studentId = $decodedData['student_id'] ?? $studentId;
                    
                    Log::info('Parsed QR data:', [
                        'raw' => $request->student_id,
                        'parsed_id' => $studentId,
                        'decoded_data' => $decodedData
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to parse JSON:', [
                        'raw_data' => $studentId,
                        'error' => $e->getMessage()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid QR code format'
                    ], 400);
                }
            }

            // Get event and verify it exists and belongs to current school year
            $event = Event::where('event_id', $request->event_id)
                          ->where('school_year_id', $activeSchoolYear->id)
                          ->first();
            
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found or does not belong to current school year'
                ], 404);
            }
            
            // Check if event is finished
            if ($event->is_finished) {
                return response()->json([
                    'success' => false,
                    'message' => 'This event has been marked as finished'
                ], 403);
            }

            // Verify scan type is open
            $scanType = $event->open_scan_type;
            if (!$scanType || !in_array($scanType, $this->scanTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No scan type is currently open for this event'
                ], 400);
            }

            // Validate scan type against event duration
            if (!$this->isValidScanTypeForEvent($event, $scanType)) {
                return response()->json([
                    'success' => false,
                    'message' => "Current scan type is not valid for this event's duration"
                ], 400);
            }

            // Check if student exists
            $student = Student::where('student_id', $studentId)->first();
            if (!$student) {
                Log::warning('Student not found:', ['student_id' => $studentId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found: ' . $studentId
                ], 404);
            }

            // Get or create attendance record
            $attendance = Attendance::firstOrCreate(
                [
                    'event_id' => $request->event_id,
                    'student_id' => $studentId,
                    'attendance_date' => now()->toDateString(),
                    'school_year_id' => $activeSchoolYear->id
                ],
                [
                    'am_in' => false,
                    'am_out' => false,
                    'pm_in' => false,
                    'pm_out' => false,
                    'am_in_time' => null,
                    'am_out_time' => null,
                    'pm_in_time' => null,
                    'pm_out_time' => null
                ]
            );

            // Check if already scanned for this type
            if ($attendance->$scanType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already scanned for ' . strtoupper(str_replace('_', ' ', $scanType))
                ], 409);
            }

            // Record attendance with timestamp
            $attendance->$scanType = true;
            $attendance->{$scanType . '_time'} = now();
            
            try {
                $attendance->save();

                Log::info('Attendance recorded successfully:', [
                    'student_id' => $studentId,
                    'student_name' => $student->student_name,
                    'event_id' => $request->event_id,
                    'scan_type' => $scanType,
                    'attendance_id' => $attendance->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Attendance recorded for ' . strtoupper(str_replace('_', ' ', $scanType)),
                    'student_name' => $student->student_name,
                    'scan_type' => $scanType,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to save attendance:', [
                    'error' => $e->getMessage(),
                    'student_id' => $studentId,
                    'event_id' => $request->event_id,
                    'scan_type' => $scanType
                ]);

                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Attendance scan failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => $request->student_id ?? null,
                'event_id' => $request->event_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Secretary Attendance QR page
    public function index()
    {
        try {
            // Check for active school year
            $activeSchoolYear = $this->getActiveSchoolYear();

            Log::info('Fetching events for QR attendance page', [
                'school_year' => $activeSchoolYear->year
            ]);

            // Get events for current school year only, include event_duration field
            $events = Event::select('event_id', 'event_name', 'event_location', 'event_date', 'time_duration', 'is_finished', 'open_scan_type')
                         ->where('school_year_id', $activeSchoolYear->id)
                         ->orderBy('event_date', 'desc')
                         ->get();

            Log::info('Events retrieved:', [
                'count' => $events->count(),
                'school_year' => $activeSchoolYear->year
            ]);

            return view('Secretary.AttendanceQR', [
                'events' => $events,
                'activeSchoolYear' => $activeSchoolYear
            ]);

        } catch (\Exception $e) {
            Log::error('Error in AttendanceQR index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Secretary.AttendanceQR', [
                'error' => $e->getMessage(),
                'events' => collect() // Empty collection to prevent blade errors
            ]);
        }
    }
    // Finish Event
    public function finishEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,event_id'
            ]);

            $event = Event::findOrFail($validated['event_id']);
            
            // Check if already finished
            if ($event->is_finished) {
                return response()->json([
                    'success' => false,
                    'message' => 'This event is already finished'
                ], 400);
            }

            // Update event as finished and close any open scan types
            $event->update([
                'is_finished' => true,
                'finished_at' => now(),
                'open_scan_type' => null // Close any open scan type
            ]);

            Log::info('Event finished successfully', [
                'event_id' => $validated['event_id'],
                'event_name' => $event->event_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->event_name . '" has been marked as finished'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to finish event', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to finish event: ' . $e->getMessage()
            ], 500);
        }
    }

public function printReport(Request $request) 
{
    $eventId = $request->get('event_id');
    
    if (!$eventId) {
        return back()->with('error', 'Event ID is required');
    }
    
    // Get event details
    $event = Event::find($eventId);
    if (!$event) {
        return back()->with('error', 'Event not found');
    }
    
    // Get ALL students with their attendance data (if any)
    $attendanceData = DB::table('students')
        ->leftJoin('attendances', function($join) use ($eventId) {
            $join->on('students.student_id', '=', 'attendances.student_id')
                 ->where('attendances.event_id', '=', $eventId);
        })
        ->select(
            'students.student_id',
            'students.student_name',
            'students.year_level',
            'attendances.am_in',
            'attendances.am_out',
            'attendances.pm_in',
            'attendances.pm_out'
        )
        ->orderBy('students.year_level')
        ->orderBy('students.student_name')
        ->get();
    
    // The view handles all the calculations internally based on event time_duration
    // So we just need to pass the raw data and let the view do the work
    
    return view('secretary.print-report', compact(
        'event',
        'attendanceData'
    ));

}
    
}