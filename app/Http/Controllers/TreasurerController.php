<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Officer;
use App\Models\Event;
use App\Models\Student;
use App\Models\PaymentFor;
use App\Models\Attendance;

class TreasurerController extends Controller
{
    public function index()
    {
        // Get the current treasurer from the database
        $treasurer = Officer::where('position', 'Treasurer')->first();

        return view('Treasurer.Treasurer', [
            'treasurer' => $treasurer
        ]);
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

        return view('SuperAdmin.OrgStruct', [
            'positions' => $positions,
            'officers' => $officers,
            'currentSchoolYear' => $currentSchoolYear
        ]);

    } catch (\Exception $e) {
        Log::error('Error in SuperAdmin orgstruct:', ['error' => $e->getMessage()]);
        return view('SuperAdmin.OrgStruct', [
            'positions' => [],
            'officers' => [],
            'error' => 'Failed to load organizational structure'
        ]);
    }
}

    public function treasdashboard()
    {
        return view('Treasurer.TreasDashboard');
    }
    public function payment()
    {
        $students = Student::all();
        $payment_fors = PaymentFor::all(); // Add this line
        
        return view('Treasurer.Payment', compact('students', 'payment_fors'));
    }
    public function transaction()
    {
        return view('treasurer.transaction');
    }
    public function eventsam()
{
    $events = Event::all(); // Fetch all events from the database
    return view('treasurer.eventsam', compact('events'));
}
    public function itsboOfficers()
{
    return view('Treasurer.Orgtruct', ['menu' => 'ItsboOfficers', 'content' => 'Orgtruct']);
}
    public function fines()
    {
        return view('Treasurer.Fines');
    }

public function searchStudents(Request $request)
{
    $query = $request->input('query');
    $type = $request->input('type');

    $students = Student::when($type === 'id', function($q) use ($query) {
            return $q->where('student_id', 'LIKE', "%{$query}%");
        })
        ->when($type === 'name', function($q) use ($query) {
            return $q->where('student_name', 'LIKE', "%{$query}%");
        })
        ->get();

    return response()->json($students);
}

public function getStudentPayments($id)
{
    $student = Student::findOrFail($id);
    $payment_fors = PaymentFor::all(); // Add this line
    $payments = $student->payments;
    
    return response()->json([
        'student' => $student,
        'payment_fors' => $payment_fors,
        'payments' => $payments
    ]);
}

public function showFines()
{
    // Get only active students
    $students = Student::where('status', 'active')->get();
    $events = Event::currentSchoolYear()->get();

    $finesData = [];

    foreach ($students as $student) {
        $totalFines = 0;
        foreach ($events as $event) {
            $attendance = Attendance::where('student_id', $student->student_id)
                ->where('event_id', $event->event_id)
                ->first();

            $requiredFields = [];
            switch ($event->time_duration) {
                case 'Whole Day':
                    $requiredFields = ['am_in', 'am_out', 'pm_in', 'pm_out'];
                    break;
                case 'Half Day: Morning':
                    $requiredFields = ['am_in', 'am_out'];
                    break;
                case 'Half Day: Afternoon':
                    $requiredFields = ['pm_in', 'pm_out'];
                    break;
            }

            foreach ($requiredFields as $field) {
                if (!$attendance || !$attendance->$field) {
                    $totalFines += 50;
                }
            }
        }

        // Include only active students in the fines data
        if ($student->status === 'active') {
            $lessPayment = 0; // Replace with actual payment logic if needed
            
            $finesData[] = [
                'student' => $student,
                'total_fines' => $totalFines,
                'less_payment' => $lessPayment,
                'remaining_balance' => $totalFines - $lessPayment,
            ];
        }
    }

    return view('Treasurer.Fines', compact('finesData'));
}
}