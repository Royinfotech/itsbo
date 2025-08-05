<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Payment;
use App\Models\PaymentFor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SchoolYear;

class DashboardTreasurerController extends Controller
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
            $activeSchoolYear = $this->getActiveSchoolYear();
            
            // Get active students count
            $activeStudentCount = Student::where('status', 'active')->count();

            // Get total paid amount from all payments for the active school year
            $totalPaid = Payment::whereHas('paymentFor', function($query) use ($activeSchoolYear) {
                $query->where('school_year_id', $activeSchoolYear->id);
            })->sum('amount');

            // Get payment types with their collections for the active school year only
            $payment_fors = PaymentFor::select([
                'payment_fors.*',
                DB::raw('COALESCE((SELECT SUM(amount) FROM payments WHERE payment_for_id = payment_fors.id), 0) as total_paid')
            ])
            ->where('school_year_id', $activeSchoolYear->id)
            ->get();

            // Calculate totals for each payment type
            foreach ($payment_fors as $payment_for) {
                $payment_for->expected_total = $payment_for->amount * $activeStudentCount;
                $payment_for->remaining = $payment_for->expected_total - $payment_for->total_paid;
                $payment_for->progress = $payment_for->expected_total > 0 
                    ? ($payment_for->total_paid / $payment_for->expected_total) * 100 
                    : 0;
            }

            // Prepare chart data
            $chartLabels = $payment_fors->pluck('name');
            $chartData = $payment_fors->pluck('total_paid');
            $chartColors = [
                '#800000', '#A52A2A', '#D2691E', '#CD853F',
                '#8B4513', '#DEB887', '#D2B48C', '#BC8F8F'
            ];

            // Log dashboard data
            Log::info('Dashboard Data Loaded', [
                'active_school_year' => $activeSchoolYear->id,
                'active_students' => $activeStudentCount,
                'total_paid' => $totalPaid,
                'payment_types' => $payment_fors->count()
            ]);

            return view('Treasurer.Dashboard', compact(
                'payment_fors',
                'totalPaid',
                'chartLabels',
                'chartData',
                'chartColors'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard loading error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error loading dashboard data');
        }
    }
}