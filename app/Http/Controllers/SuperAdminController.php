<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Officer;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Display the SuperAdmin Dashboard.
     */
    public function index()
    {
        return view('superadmin.superadmin');
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

    public function viewPage($role, $page)
    {
        $allowedRoles = ['secretary', 'treasurer', 'admin', 'student'];

        if (!in_array($role, $allowedRoles)) {
            abort(404, 'Role not found');
        }

        $viewPath = "{$role}.{$page}";

        if (view()->exists($viewPath)) {
            return view($viewPath);
        }

        abort(404, 'Page not found');
    }
    public function schoolYear()
    {
        $schoolYears = SchoolYear::orderBy('created_at', 'desc')->get();
        $current = SchoolYear::where('is_open', true)->first();
        return view('SuperAdmin.SchoolYear', compact('schoolYears', 'current'));
    }

    public function openSchoolYear(Request $request)
    {
        Log::info('Attempting to open school year', [
            'year' => $request->year,
            'semester' => $request->semester,
            'positions' => $request->positions
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            $validated = $request->validate([
                'year' => 'required|string',
                'semester' => 'required|in:1st,2nd',
                'officer_limit' => 'required|integer|min:1',
                'positions' => 'required|string'
            ]);

            // First, close any currently open school year
            SchoolYear::where('is_open', true)
                     ->update(['is_open' => false, 'closed_at' => now()]);

            // Check if this exact year exists
            $existingYear = SchoolYear::where('year', $validated['year'])
                                    ->orderBy('created_at', 'desc')
                                    ->first();

            if ($existingYear) {
                // Create new semester while keeping same positions
                $newSchoolYear = SchoolYear::create([
                    'year' => $validated['year'],
                    'semester' => $validated['semester'],
                    'is_open' => true,
                    'officer_limit' => $existingYear->officer_limit,
                    'open_positions' => $existingYear->open_positions,
                    'opened_by' => auth()->id(),
                    'opened_at' => now()
                ]);

                // Copy officers from previous semester
                foreach ($existingYear->officers as $officer) {
                    $officer->replicate([
                        'school_year_id' => $newSchoolYear->id
                    ])->save();
                }

                // Set all students to inactive when new semester opens
                Student::where('status', 'active')
                       ->update(['status' => 'inactive']);

                Log::info('Opened new semester and set students to inactive', [
                    'year' => $newSchoolYear->year,
                    'semester' => $newSchoolYear->semester
                ]);

                DB::commit();
                return back()->with('success', 'New semester opened successfully with existing officers. All students set to inactive.');
            } else {
                // This is a completely new school year
                $positions = array_map('trim', explode(',', $validated['positions']));
                
                SchoolYear::create([
                    'year' => $validated['year'],
                    'semester' => $validated['semester'],
                    'is_open' => true,
                    'officer_limit' => $validated['officer_limit'],
                    'open_positions' => $positions,
                    'opened_by' => auth()->id(),
                    'opened_at' => now()
                ]);

                // Set all students to inactive when new school year opens
                Student::where('status', 'active')
                       ->update(['status' => 'inactive']);

                Log::info('Opened new school year and set students to inactive', [
                    'year' => $validated['year'],
                    'semester' => $validated['semester']
                ]);

                DB::commit();
                return back()->with('success', 'New school year opened successfully. All students set to inactive.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to open school year', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to open school year: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function closeSchoolYear(Request $request)
    {
        try {
            $currentYear = SchoolYear::where('is_open', true)->first();
            if ($currentYear) {
                $currentYear->update([
                    'is_open' => false,
                    'closed_at' => now()
                ]);
                return back()->with('success', 'School year closed successfully');
            }
            return back()->with('error', 'No open school year found');
        } catch (\Exception $e) {
            Log::error('Error closing school year: ' . $e->getMessage());
            return back()->with('error', 'Failed to close school year');
        }
    }
    public function openNewSemester(Request $request)
{
    try {
        // Begin transaction
        DB::beginTransaction();
        
        // Get current first semester
        $currentYear = SchoolYear::where('year', $request->year)
                               ->where('semester', '1st')
                               ->first();

        if (!$currentYear) {
            throw new \Exception('First semester not found for this school year.');
        }

        // Check if second semester already exists
        $existingSecondSem = SchoolYear::where('year', $request->year)
                                      ->where('semester', '2nd')
                                      ->exists();
        
        if ($existingSecondSem) {
            throw new \Exception('Second semester already exists for this school year.');
        }

        // Close any currently open school year
        SchoolYear::where('is_open', true)->update([
            'is_open' => false,
            'closed_at' => now()
        ]);

        // Create new semester
        $newSemester = SchoolYear::create([
            'year' => $request->year,
            'semester' => '2nd',
            'is_open' => true,
            'officer_limit' => $currentYear->officer_limit,
            'open_positions' => $currentYear->open_positions,
            'opened_by' => auth()->id(),
            'opened_at' => now()
        ]);

        // Copy officers
        $officers = Officer::where('school_year_id', $currentYear->id)->get();
        foreach ($officers as $officer) {
            $officer->replicate()
                   ->fill(['school_year_id' => $newSemester->id])
                   ->save();
        }

        // Set all students to inactive when new semester opens
        Student::where('status', 'active')
               ->update(['status' => 'inactive']);

        Log::info('Opened new semester and set students to inactive', [
            'year' => $request->year,
            'semester' => '2nd'
        ]);

        DB::commit();
        
        return back()->with('success', 'Second semester opened successfully. All students set to inactive.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Failed to open second semester', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to open second semester: ' . $e->getMessage());
    }
    
}
    public function archieve(Request $request)
{
    $student = null;
    $attendances = collect();
    $payments = collect();

    if ($request->filled('student_id')) {
        $student = \App\Models\Student::where('student_id', $request->student_id)->first();

        if ($student) {
            $attendances = \App\Models\Attendance::where('student_id', $student->student_id)->get();
            $payments = \App\Models\Payment::where('student_id', $student->student_id)->get();
        }
    }

    return view('SuperAdmin.archieve', compact('student', 'attendances', 'payments'));
}
}