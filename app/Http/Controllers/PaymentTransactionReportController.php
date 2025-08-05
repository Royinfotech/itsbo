<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentTransactionReportController extends Controller
{
    private function getActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_open', true)->first();
        if (!$activeSchoolYear) {
            throw new \Exception('No active school year found. Please open a school year first.');
        }
        return $activeSchoolYear;
    }

    public function index(Request $request)
    {
        try {
            // Get the open school year
            $activeSchoolYear = $this->getActiveSchoolYear();

            // Build query using Eloquent with relationships
            $query = Payment::with(['student', 'paymentFor'])
                ->where('school_year_id', $activeSchoolYear->id);

            // Apply date range filters if provided
            if ($request->filled('from_date')) {
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $query->where('payment_date', '>=', $fromDate);
            }

            if ($request->filled('to_date')) {
                $toDate = Carbon::parse($request->to_date)->endOfDay();
                $query->where('payment_date', '<=', $toDate);
            }

            // Legacy month filter support (if needed)
            if ($request->has('month') && !empty($request->month)) {
                $month = (int) $request->month;
                if ($month >= 1 && $month <= 12) {
                    $query->where(function($q) use ($month) {
                        $q->whereMonth('payment_date', $month)
                          ->orWhereMonth('created_at', $month);
                    });
                }
            }

            // Get all payments with relationships - using 'transactions' variable name for Blade
            $transactions = $query->orderBy('payment_date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->get();

            // Calculate summary data directly from Payment model
            $paidTransactions = $transactions->where('status', 'paid');
            $pendingTransactions = $transactions->where('status', 'pending');
            $failedTransactions = $transactions->where('status', 'failed');

            $summary = [
                'total_count' => $transactions->count(),
                'total_amount' => $transactions->sum('amount'),
                'paid_count' => $paidTransactions->count(),
                'pending_count' => $pendingTransactions->count(),
                'failed_count' => $failedTransactions->count(),
                'paid_amount' => $paidTransactions->sum('amount'),
                'pending_amount' => $pendingTransactions->sum('amount'),
            ];

            return view('reports.payment-transactions', compact(
                'activeSchoolYear',
                'transactions',  // Payment collection, named 'transactions' for Blade
                'summary'
            ));

        } catch (\Exception $e) {
            return view('reports.payment-transactions', [
                'error' => 'An error occurred while generating the report: ' . $e->getMessage(),
                'activeSchoolYear' => null,
                'transactions' => collect([]),  // Empty collection for Blade
                'summary' => [
                    'total_count' => 0,
                    'total_amount' => 0,
                    'paid_count' => 0,
                    'pending_count' => 0,
                    'failed_count' => 0,
                    'paid_amount' => 0,
                    'pending_amount' => 0,
                ]
            ]);
        }
    }

    /**
     * Export the payment report to PDF (optional)
     */
    public function exportPdf(Request $request)
    {
        try {
            $data = $this->getReportData($request);
            
            // Generate PDF using your preferred PDF library
            // Example with DomPDF:
            // $pdf = PDF::loadView('reports.payment-transactions-pdf', $data);
            // return $pdf->download('payment-transactions-report.pdf');
            
            return response()->json(['message' => 'PDF export feature needs to be implemented']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get report data for exports
     */
    private function getReportData(Request $request)
    {
        $activeSchoolYear = $this->getActiveSchoolYear();

        $query = Payment::with(['student', 'paymentFor'])
            ->where('school_year_id', $activeSchoolYear->id);

        // Apply date range filters
        if ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('payment_date', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('payment_date', '<=', $toDate);
        }

        // Legacy month filter
        if ($request->has('month') && !empty($request->month)) {
            $month = (int) $request->month;
            if ($month >= 1 && $month <= 12) {
                $query->where(function($q) use ($month) {
                    $q->whereMonth('payment_date', $month)
                      ->orWhereMonth('created_at', $month);
                });
            }
        }

        $payments = $query->orderBy('payment_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return [
            'activeSchoolYear' => $activeSchoolYear,
            'transactions' => $payments,
            'summary' => [
                'paid_count' => $payments->where('status', 'paid')->count(),
                'pending_count' => $payments->where('status', 'pending')->count(),
                'failed_count' => $payments->where('status', 'failed')->count(),
                'paid_amount' => $payments->where('status', 'paid')->sum('amount'),
            ]
        ];
    }
}