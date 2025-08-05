<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\SchoolYear;
use Carbon\Carbon;

class PaymentReportController extends Controller
{
    /**
     * Display the payment transaction report
     */
    public function index(Request $request)
    {
        try {
            // Get the open school year
            $openSchoolYear = SchoolYear::where('is_open', 1)->first();
            
            // Base query for payments
            $query = Payment::with(['student']); // Assuming you have a student relationship
            
            // Filter by school year if available
            if ($openSchoolYear) {
                $query->where('school_year_id', $openSchoolYear->id);
            }
            
            // Filter by month if provided
            if ($request->has('month') && $request->month != '') {
                $month = $request->month;
                $query->where(function($q) use ($month) {
                    $q->whereMonth('payment_date', $month)
                      ->orWhere(function($subQ) use ($month) {
                          $subQ->whereNull('payment_date')
                               ->whereMonth('created_at', $month);
                      });
                });
            }
            
            // Get payments ordered by date (newest first)
            $payments = $query->orderBy('payment_date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->get();
            
            // Calculate summaries
            $paidPayments = $payments->where('status', 'paid');
            $pendingPayments = $payments->where('status', 'pending');
            $failedPayments = $payments->where('status', 'failed');
            
            // Pass data to view
            return view('payment-report', compact(
                'payments',
                'paidPayments', 
                'pendingPayments',
                'failedPayments',
                'openSchoolYear'
            ));
            
        } catch (\Exception $e) {
            // Handle any errors
            return back()->with('error', 'An error occurred while generating the report: ' . $e->getMessage());
        }
    }
    
    /**
     * Alternative method if you want to handle different report types
     */
    public function show(Request $request)
    {
        return $this->index($request);
    }
    
    /**
     * Export/Print version of the report
     */
    public function print(Request $request)
    {
        // Same logic as index but might want to format differently for printing
        return $this->index($request);
    }
    
    /**
     * Get payment statistics for dashboard or API
     */
    public function getStats(Request $request)
    {
        try {
            $openSchoolYear = SchoolYear::where('is_open', 1)->first();
            
            $query = Payment::query();
            
            if ($openSchoolYear) {
                $query->where('school_year_id', $openSchoolYear->id);
            }
            
            if ($request->has('month') && $request->month != '') {
                $month = $request->month;
                $query->where(function($q) use ($month) {
                    $q->whereMonth('payment_date', $month)
                      ->orWhere(function($subQ) use ($month) {
                          $subQ->whereNull('payment_date')
                               ->whereMonth('created_at', $month);
                      });
                });
            }
            
            $payments = $query->get();
            
            $stats = [
                'total_transactions' => $payments->count(),
                'total_amount' => $payments->sum('amount'),
                'paid_transactions' => $payments->where('status', 'paid')->count(),
                'paid_amount' => $payments->where('status', 'paid')->sum('amount'),
                'pending_transactions' => $payments->where('status', 'pending')->count(),
                'pending_amount' => $payments->where('status', 'pending')->sum('amount'),
                'failed_transactions' => $payments->where('status', 'failed')->count(),
                'failed_amount' => $payments->where('status', 'failed')->sum('amount'),
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}