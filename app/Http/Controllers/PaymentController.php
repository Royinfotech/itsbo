<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentFor;
use Illuminate\Support\Facades\Log;
use App\Models\SchoolYear; 


class PaymentController extends Controller
{
    private function getActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_open', true)->first();
        if (!$activeSchoolYear) {
            throw new \Exception('No active school year found');
        }
        return $activeSchoolYear;
    }

    public function index()
    {
        try {
            $activeSchoolYear = $this->getActiveSchoolYear();

            $students = Student::select(
                'students.*',
                DB::raw('(SELECT SUM(amount) FROM payments 
                    WHERE student_id = students.id 
                    AND school_year_id = ?) as total_paid')
            )
            ->where('status', 'active')
            ->setBindings([$activeSchoolYear->id])
            ->get();
            
            // Get payment types for active school year
            $payment_fors = PaymentFor::with(['payments' => function($query) use ($activeSchoolYear) {
                $query->select('payment_for_id', DB::raw('SUM(amount) as total_paid'))
                      ->where('school_year_id', $activeSchoolYear->id)
                      ->groupBy('payment_for_id');
            }])
            ->where('school_year_id', $activeSchoolYear->id)
            ->get();
            
            $activeStudentCount = Student::where('status', 'active')->count();
            
            // Get total paid for current school year
            $totalPaid = Payment::where('school_year_id', $activeSchoolYear->id)
                           ->sum('amount');

            return view('Treasurer.Payment', compact(
                'payment_fors',
                'activeStudentCount',
                'activeSchoolYear',
                'totalPaid'
            ));

        } catch (\Exception $e) {
            Log::error('Payment calculation error:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error calculating payment totals');
        }
    }

    public function search(Request $request)
    {
        try {
            // Get active school year
            $activeSchoolYear = $this->getActiveSchoolYear();
            
            $query = $request->input('query');
            $type = $request->input('type'); // 'id' or 'name'

            $students = Student::select(
                'students.*',
                DB::raw('(SELECT SUM(amount) FROM payments 
                    WHERE student_id = students.id 
                    AND school_year_id = ?) as total_paid'),
                DB::raw('(SELECT COUNT(*) FROM payments 
                    WHERE student_id = students.id 
                    AND school_year_id = ?) as payment_count')
            )
            ->setBindings([$activeSchoolYear->id, $activeSchoolYear->id]);

            if ($type === 'id') {
                $students->where('student_id', 'like', "%{$query}%");
            } else {
                $students->where(function($q) use ($query) {
                    $q->where('student_name', 'like', "%{$query}%");
                });
            }

            $results = $students->where('status', 'active')->get();

            return response()->json([
                'success' => true,
                'data' => $results,
                'school_year' => $activeSchoolYear->year
            ]);

        } catch (\Exception $e) {
            Log::error('Search error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error searching students'
            ], 500);
        }
    }

     public function show($id)
    {
    try {
        $activeSchoolYear = $this->getActiveSchoolYear();
        $student = Student::findOrFail($id);
        
        $payments = Payment::select('payments.*', 'payment_fors.name as payment_for_name')
                          ->join('payment_fors', 'payments.payment_for_id', '=', 'payment_fors.id')
                          ->where('payments.student_id', $id)
                          ->where('payments.school_year_id', $activeSchoolYear->id)
                          ->orderBy('payments.created_at', 'desc')
                          ->get();

        $paymentFors = PaymentFor::where('school_year_id', $activeSchoolYear->id)
                                ->get()
                                ->map(function($paymentFor) use ($id, $activeSchoolYear) {
            $totalPaid = Payment::where([
                'student_id' => $id,
                'payment_for_id' => $paymentFor->id,
                'school_year_id' => $activeSchoolYear->id
            ])->sum('amount');
            
            return [
                'id' => $paymentFor->id,
                'name' => $paymentFor->name,
                'amount' => $paymentFor->amount,
                'remaining_amount' => $paymentFor->amount - $totalPaid,
                'total_paid' => $totalPaid
            ];
        });

        return response()->json([
            'success' => true,
            'student' => $student,
            'payments' => $payments,
            'payment_fors' => $paymentFors,
            'school_year' => $activeSchoolYear->year
        ]);

    } catch (\Exception $e) {
        Log::error('Error getting student details:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving student details'
        ], 500);
    }
   }

    public function store(Request $request)
    {
        try {
            $activeSchoolYear = $this->getActiveSchoolYear();

            $request->validate([
                'student_id' => 'required|exists:students,id',
                'payment_for_id' => 'required|exists:payment_fors,id',
                'amount' => 'required|numeric|min:0.01',
                'or_number' => 'required|string|unique:payments,or_number',
                'payment_date' => 'required|date'
            ]);

            // Get payment for from current school year
            $paymentFor = PaymentFor::where('id', $request->payment_for_id)
                                  ->where('school_year_id', $activeSchoolYear->id)
                                  ->firstOrFail();
            
            // Calculate total paid for current semester
            $totalPaid = Payment::where([
                'student_id' => $request->student_id,
                'payment_for_id' => $request->payment_for_id
            ])
            ->whereHas('schoolYear', function($query) use ($activeSchoolYear) {
                $query->where('id', $activeSchoolYear->id)
                      ->where('semester', $activeSchoolYear->semester);
            })
            ->sum('amount');
            
            $remainingAmount = $paymentFor->amount - $totalPaid;

            if ($request->amount > $remainingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment amount exceeds remaining amount for this semester"
                ], 400);
            }

            $payment = Payment::create([
                'student_id' => $request->student_id,
                'payment_for_id' => $request->payment_for_id,
                'amount' => $request->amount,
                'or_number' => $request->or_number,
                'payment_date' => $request->payment_date,
                'status' => 'completed',
                'school_year_id' => $activeSchoolYear->id
            ]);

            return response()->json([
                'success' => true,
                'message' => "Payment recorded for {$activeSchoolYear->semester} Semester, SY {$activeSchoolYear->year}",
                'payment' => $payment,
                'remaining_amount' => $remainingAmount - $request->amount,
                'school_year' => $activeSchoolYear->year,
                'semester' => $activeSchoolYear->semester
            ]);

        } catch (\Exception $e) {
            Log::error('Payment creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment: ' . $e->getMessage()
            ], 500);
        }
    }

// Update the existing update method to handle the new requirements:
public function update(Request $request, $id)
{
    try {
        $activeSchoolYear = $this->getActiveSchoolYear();
        
        $request->validate([
            'or_number' => 'required|string|unique:payments,or_number,' . $id,
            'payment_for_id' => 'required|exists:payment_fors,id',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $payment = Payment::findOrFail($id);
        
        // Get payment for from current school year
        $paymentFor = PaymentFor::where('id', $request->payment_for_id)
                                ->where('school_year_id', $activeSchoolYear->id)
                                ->firstOrFail();
        
        // Calculate total paid for current semester (excluding current payment)
        $totalPaid = Payment::where([
            'student_id' => $payment->student_id,
            'payment_for_id' => $request->payment_for_id
        ])
        ->where('id', '!=', $id) // Exclude current payment
        ->where('school_year_id', $activeSchoolYear->id)
        ->sum('amount');
        
        $remainingAmount = $paymentFor->amount - $totalPaid;

        if ($request->amount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => "Payment amount exceeds remaining amount for this payment type"
            ], 400);
        }

        $payment->update([
            'or_number' => $request->or_number,
            'payment_for_id' => $request->payment_for_id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'payment' => $payment
        ]);

    } catch (\Exception $e) {
        Log::error('Payment update error:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error updating payment: ' . $e->getMessage()
        ], 500);
    }
}
public function getPaymentHistory($studentId)
    {
        $payments = Payment::where('student_id', $studentId)
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json($payments);
    }
    public function edit($id)
{
    try {
        $payment = Payment::with('paymentFor')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'id' => $payment->id,
            'or_number' => $payment->or_number,
            'payment_for_id' => $payment->payment_for_id,
            'amount' => $payment->amount,
            'payment_date' => $payment->payment_date,
            'payment_for_name' => $payment->paymentFor->name
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching payment for edit:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving payment details'
        ], 500);
    }
}

    public function getPaymentSummary($studentId)
    {
        try {
            $activeSchoolYear = $this->getActiveSchoolYear();

            // Get payments for current semester only
            $payment_fors = PaymentFor::select(
                'payment_fors.*',
                DB::raw('COALESCE((
                    SELECT SUM(amount) 
                    FROM payments 
                    WHERE payments.payment_for_id = payment_fors.id 
                    AND payments.student_id = ?
                    AND payments.school_year_id = ?
                ), 0) as total_paid')
            )
            ->where('school_year_id', $activeSchoolYear->id)
            ->setBindings([$studentId, $activeSchoolYear->id])
            ->get();

            $student = Student::findOrFail($studentId);

            // Get payment history for current semester
            $payments = Payment::where('student_id', $studentId)
                ->whereHas('schoolYear', function($query) use ($activeSchoolYear) {
                    $query->where('id', $activeSchoolYear->id)
                          ->where('semester', $activeSchoolYear->semester);
                })
                ->with('paymentFor')
                ->orderBy('payment_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'student_name' => $student->full_name,
                        'student_id' => $student->id
                    ],
                    'payment_types' => $payment_fors,
                    'payments' => $payments,
                    'school_year' => $activeSchoolYear->year,
                    'semester' => $activeSchoolYear->semester
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment summary error:', [
                'error' => $e->getMessage(),
                'student_id' => $studentId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving payment summary'
            ], 500);
        }
    }

    public function storePaymentFor(Request $request)
    {
        try {
            $activeSchoolYear = $this->getActiveSchoolYear();
            
            $request->validate([
                'payment_name' => 'required|string|max:255',
                'payment_amount' => 'required|numeric|min:0',
            ]);

            // Check for duplicate payment name in current school year
            $existingPayment = PaymentFor::where('name', $request->payment_name)
                ->where('school_year_id', $activeSchoolYear->id)
                ->first();
                
            if ($existingPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'A payment with this name already exists in the current school year.'
                ], 400);
            }

            $paymentFor = PaymentFor::create([
                'name' => $request->payment_name,
                'amount' => $request->payment_amount,
                'school_year_id' => $activeSchoolYear->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment type created successfully',
                'payment_for' => $paymentFor,
                'school_year' => $activeSchoolYear->year
            ]);

        } catch (\Exception $e) {
            Log::error('Payment type creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment type: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showPaymentFor($id)
    {
        
        $paymentFor = PaymentFor::findOrFail($id);
        return response()->json($paymentFor);
    }

    public function getPaymentFors()
{
    try {
        $activeSchoolYear = $this->getActiveSchoolYear();
        
        $paymentFors = PaymentFor::where('school_year_id', $activeSchoolYear->id)
            ->select('id', 'name', 'amount', 'school_year_id')
            ->orderBy('name', 'asc') // Add ordering
            ->get();

        return response()->json([
            'success' => true,
            'data' => $paymentFors,
            'school_year' => $activeSchoolYear->year
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching payment types:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error loading payment types'
        ], 500);
    }
}


    public function updatePaymentFor(Request $request, $id)
    {
        $request->validate([
            'payment_name' => 'required|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        try {
            $paymentFor = PaymentFor::findOrFail($id);
            
            // Check if there are any changes
            if ($paymentFor->name === $request->payment_name && 
                $paymentFor->amount === $request->payment_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes detected in the payment for entry.'
                ], 400);
            }

            // Check for duplicate name (excluding current record)
            $existingPaymentFor = PaymentFor::where('name', $request->payment_name)
                                          ->where('id', '!=', $id)
                                          ->first();

            if ($existingPaymentFor) {
                return response()->json([
                    'success' => false,
                    'message' => 'A payment for with this name already exists.'
                ], 400);
            }

            $paymentFor->update([
                'name' => $request->payment_name,
                'amount' => $request->payment_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment For updated successfully',
                'payment_for' => $paymentFor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating Payment For: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyPaymentFor($id)
    {
        $paymentFor = PaymentFor::findOrFail($id);
        $paymentFor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment For deleted successfully'
        ]);
    
    }
}