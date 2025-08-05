<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Event;
use App\Models\File;
use App\Models\Officer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Models\StudentUser;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\Attendance;

class StudentController extends Controller
{


    
    public function profile($student_id)
    {
        try {
            $student = Student::where('student_id', $student_id)->firstOrFail();
            return view('Student.profile', compact('student'));
        } catch (\Exception $e) {
            Log::error('Profile load error:', [
                'student_id' => $student_id,
                'error' => $e->getMessage()
            ]);
            return response()->view('Student.error', [
                'message' => 'Failed to load profile'
            ], 500);
        }
    }

    public function qrcode($student_id)
    {
        try {
            Log::info('Generating QR code for student:', ['student_id' => $student_id]);
            
            $student = Student::where('student_id', $student_id)->firstOrFail();
            
            // Create QR code data
            $qrData = json_encode([
                'student_id' => $student->student_id,
                'timestamp' => now()->timestamp
            ]);

            // Set up QR code renderer
            $renderer = new ImageRenderer(
                new RendererStyle(300, 4),
                new SvgImageBackEnd()
            );
            
            // Generate QR code
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrData);

            return view('Student.qrcode', [
                'student' => $student,
                'qrcode' => $qrCodeSvg
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code generation failed:', [
                'student_id' => $student_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->view('Student.qrcode', [
                'student' => Student::where('student_id', $student_id)->first(),
                'error' => 'Failed to generate QR code. Please try again.'
            ], 500);
        }
    }

    public function refreshQRCode($student_id)
    {
        try {
            // Verify student exists
            $student = Student::where('student_id', $student_id)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'message' => 'QR code refreshed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code refresh failed:', [
                'student_id' => $student_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh QR code'
            ], 500);
        }
    }


    public function dashboard($student_id)
    {
        try {
            // Get student data from students table
            $student = Student::where('student_id', $student_id)->first();

            if (!$student) {
                Log::warning('Student not found:', ['student_id' => $student_id]);
                return redirect()->route('student.login')
                    ->withErrors(['error' => 'Student not found']);
            }

            // Get recent events
            $events = Event::orderBy('event_date', 'desc')
                          ->take(5)
                          ->get();

            Log::info('Student accessed dashboard:', ['student_id' => $student_id]);

            return view('Student.dashboard', [
                'student' => $student,
                'student_id' => $student_id,
                'events' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard error:', [
                'student_id' => $student_id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('student.login')
                ->withErrors(['error' => 'Error loading dashboard']);
        }
    }
    public function studentLogout(Request $request)
    {
        // Clear student session data
        $request->session()->forget(['student_id', 'logged_in']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Student logged out successfully');
        
        return redirect()->route('student.login')
            ->with('message', 'Logged out successfully');
    }
    public function announcement()
    {
        try {
            // Get student ID from session
            $student_id = session('student_id');
            $student = Student::where('student_id', $student_id)->first();

            return view('Student.announcement', [
                'student' => $student,
                'student_id' => $student_id
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading announcements:', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to load announcements'], 500);
        }
    }
    public function updatePhoto(Request $request, $id)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpg,jpeg,png|max:10048',
    ]);

    $student = Student::findOrFail($id);

    if ($student->photo) {
        Storage::delete($student->photo);
    }

    $path = $request->file('photo')->store('photos');

    $student->update(['photo' => $path]);

    return response()->json(['success' => true]);
}


public function getAttendanceRecord($student_id)
{
    try {
        Log::info('Fetching attendance records for student:', ['student_id' => $student_id]);

        // Verify student exists
        $student = Student::where('student_id', $student_id)->firstOrFail();

        // Get attendance records with event details
        $attendances = Attendance::with(['event' => function($query) {
            $query->select('event_id', 'event_name', 'event_date', 'time_duration');
        }])
        ->where('student_id', $student_id)
        ->orderBy('attendance_date', 'desc')
        ->get();

        Log::info('Retrieved attendance records:', [
            'count' => $attendances->count(),
            'student_name' => $student->student_name
        ]);

        // Calculate attendance statistics
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->filter(function($attendance) {
                return ($attendance->am_in && $attendance->pm_in);
            })->count(),
            'halfday' => $attendances->filter(function($attendance) {
                return ($attendance->am_in xor $attendance->pm_in);
            })->count(),
            'absent' => $attendances->filter(function($attendance) {
                return (!$attendance->am_in && !$attendance->pm_in);
            })->count()
        ];

        return view('Student.attendancerecord', [
            'student' => $student,
            'attendances' => $attendances,
            'stats' => $stats,
            'success' => true
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to fetch attendance record:', [
            'student_id' => $student_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->view('Student.attendancerecord', [
            'attendances' => collect(),
            'error' => 'Failed to load attendance records. Please try again later.',
            'success' => false
        ], 500);
    }
}
    public function orgstruct()
{
    try {
        $currentSchoolYear = \App\Models\SchoolYear::where('is_open', true)->first();
        $positions = $currentSchoolYear ? $currentSchoolYear->open_positions : [];
        
        $officers = [];
        if ($currentSchoolYear) {
            foreach ($positions as $position) {
                $positionKey = strtolower(str_replace(' ', '', $position));
                $officers[$positionKey] = Officer::where('position', $position)
                    ->where('school_year_id', $currentSchoolYear->id)
                    ->first();
            }
        }

        return view('Student.OrgStruct', [
            'positions' => $positions,
            'officers' => $officers,
            'currentSchoolYear' => $currentSchoolYear
        ]);

    } catch (\Exception $e) {
        Log::error('Error in SuperAdmin orgstruct:', ['error' => $e->getMessage()]);
        return view('Student.OrgStruct', [
            'positions' => [],
            'officers' => [],
            'error' => 'Failed to load organizational structure'
        ]);
    }
}
}