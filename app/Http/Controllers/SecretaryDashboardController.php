<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\SchoolYear;

class SecretaryDashboardController extends Controller
{
    private function getActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_open', true)->first();
        if (!$activeSchoolYear) {
            throw new \Exception('No active school year found. Please open a school year first.');
        }
        return $activeSchoolYear;
    }
    public function index()
    {
        try {
            // Get total counts
            $totalStudents = Student::count();
            $activeSchoolYear = $this->getActiveSchoolYear();

            // Get student counts by status
            $activeStudents = Student::where('status', 'active')->count();
            $pendingStudents = Student::where('status', 'pending')->count();
            $inactiveStudents = Student::where('status', 'inactive')->count();
            $declinedStudents = Student::where('status', 'declined')->count();

            // Get monthly data for students
            $monthlyStudents = Student::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray();

            // Initialize students array with zeros
            $studentData = array_fill(1, 12, 0);
            foreach ($monthlyStudents as $month => $count) {
                $studentData[(int)$month] = (int)$count;
            }

            // Get events data
            $events = Event::where('school_year_id', $activeSchoolYear->id)->get();

            return view('Secretary.SecDashboard', compact(
                'totalStudents', 
                'activeStudents', 
                'pendingStudents', 
                'inactiveStudents', 
                'declinedStudents', 
                'studentData', 
                'events'
            ));
        } catch (\Exception $e) {
            Log::error('Error getting dashboard data: ' . $e->getMessage());
            return view('Secretary.SecDashboard', [
                'totalStudents' => 0,
                'activeStudents' => 0,
                'pendingStudents' => 0,
                'inactiveStudents' => 0,
                'declinedStudents' => 0,
                'studentData' => array_fill(1, 12, 0),
                'events' => []
            ]);
        }
    }
}