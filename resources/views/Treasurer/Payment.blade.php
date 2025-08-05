<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/payment.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <!-- Add Payment Button -->
    <button class="add-payment-btn" onclick="showNewPaymentModal()">
        <i class="fa-solid fa-circle-plus"></i> Add Payment For
    </button>

    <div class="main-container">
        <!-- Title -->
        <h1 class="title">Payment Management</h1>

        <!-- Search Section -->
        <div class="search-container" style="display: flex; align-items: center; gap: 20px;">
            <div class="search-box">
                <input type="text" id="studentSearch" placeholder="Search by Student ID or Name...">
                <i class="fas fa-search search-icon"></i>
            </div>
            <!-- Year Level Sorting -->
            <div class="year-level-sorting" style="display: flex; align-items: center; gap: 10px;">
                <label for="yearLevelFilter" style="font-weight: bold; color: #333;">Year Level:</label>
                <select id="yearLevelFilter" style="padding: 6px; border-radius: 4px;">
                    <option value="">All</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
                <label for="sortOrder" style="font-weight: bold; color: #333;">Sort:</label>
                <select id="sortOrder" style="padding: 6px; border-radius: 4px;">
                    <option value="asc">Ascending (1st → 4th)</option>
                    <option value="desc">Descending (4th → 1st)</option>
                </select>
                <button type="button" onclick="applyYearLevelSorting()" style="padding: 6px 12px; border-radius: 4px; background: #4caf50; color: #fff; border: none; font-weight: bold;">Apply</button>
            </div>
        </div>

        <!-- Student List Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Year Level</th>
                        <th>Total Paid</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
            @foreach($students as $student)
                @if($student->status == 'active')
                <tr onclick="viewPaymentDetails({{ $student->id }})" style="cursor: pointer;">
                    <td data-student-id>{{ $student->student_id }}</td>
                    <td data-student-name>{{ $student->student_name }}</td>
                    <td>{{ $student->year_level }}</td>
                    <td>₱{{ number_format($student->total_paid ?? 0, 2) }}</td>
                </tr>
                @endif
            @endforeach
                </tbody>
            </table>
        </div>
    </div>


<!-- Add Payment Summary Section -->
<div class="main-container" style="margin-top: 20px;">
    <h2 class="title">Payment Summary</h2>
    
    @php
        // Get active school year
        $activeSchoolYear = App\Models\SchoolYear::where('is_open', true)->first();
        
        // Get active students count
        $activeStudentCount = App\Models\Student::where('status', 'active')->count();
        
        // Get only payment_fors for the active school year
        $activePaymentFors = App\Models\PaymentFor::select([
            'payment_fors.*',
            DB::raw('COALESCE((SELECT SUM(amount) FROM payments WHERE payment_for_id = payment_fors.id), 0) as total_paid')
        ])
        ->where('school_year_id', $activeSchoolYear->id)
        ->get();
        
        $totalExpectedAll = 0;
        $totalPaidAll = 0;
    @endphp

    <div class="payment-summary">
        <!-- Total Summary Card -->
        <div class="summary-card">
            <h3>Active Students: {{ $activeStudentCount }}</h3>
            <div class="payment-breakdown">
                @foreach($activePaymentFors as $payment_for)
                    @php
                        $expectedAmount = $payment_for->amount * $activeStudentCount;
                        $paidAmount = $payment_for->total_paid ?? 0;
                        $totalExpectedAll += $expectedAmount;
                        $totalPaidAll += $paidAmount;
                    @endphp
                    <div class="breakdown-item">
                        <span class="breakdown-label">{{ $payment_for->name }}:</span>
                        <span class="breakdown-amount">₱{{ number_format($payment_for->amount, 2) }} × {{ $activeStudentCount }} students = ₱{{ number_format($expectedAmount, 2) }}</span>
                    </div>
                @endforeach
                <div class="breakdown-total">
                    <strong>Total Expected Collection: ₱{{ number_format($totalExpectedAll, 2) }}</strong>
                </div>
            </div>
        </div>

<div class="collection-summary" style="background: #f9f9f9; padding: 25px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 30px;">
    <h3 style="text-align: center; margin-bottom: 20px; color: #333;">Total Collection Summary</h3>

    @php
        // Calculate totals from active payment_fors only
        $totalExpectedAll = $activePaymentFors->sum(function($payment) use ($activeStudentCount) {
            return $payment->amount * $activeStudentCount;
        });
        
        // Get total paid amount only from payments for active school year
        $totalPaidAll = App\Models\Payment::whereHas('paymentFor', function($query) use ($activeSchoolYear) {
            $query->where('school_year_id', $activeSchoolYear->id);
        })->sum('amount');
        
        $progressPercentage = $totalExpectedAll > 0 ? ($totalPaidAll / $totalExpectedAll) * 100 : 0;
    @endphp

    <div class="progress-container" style="max-width: 900px; margin: 0 auto 25px auto;">
        <div class="progress-bar" style="background: #e0e0e0; border-radius: 50px; height: 30px; position: relative; overflow: hidden;">
            <div class="progress-fill"
                 style="background: #4caf50; width: {{ min($progressPercentage, 100) }}%; height: 100%; position: relative;">
            </div>
            <span class="progress-label"
                  style="position: absolute; width: 87%; text-align: center; color: #000000; font-weight: bold; top: 5px;">
                {{ number_format($progressPercentage, 2) }}%   
            </span>
        </div>
    </div>

    <div class="collection-amounts" style="display: flex; justify-content: space-around; flex-wrap: wrap; text-align: center;">
        <div class="collected" style="margin-bottom: 10px;">
            <span class="label" style="font-weight: bold; color: #333;">Total Collected:</span><br>
            <span class="value" style="font-size: 1.5em;font-weight:600; color: green;">₱{{ number_format($totalPaidAll, 2, '.', ',') }}</span>
        </div>
        <div class="expected">
            <span class="label" style="font-weight: bold; color: #333;">Expected Collection:</span><br>
            <span class="value" style="font-size: 1.5em; font-weight: 600; color: red;">₱{{ number_format($totalExpectedAll, 2, '.', ',') }}</span>
        </div>
    </div>
</div>



    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Payment</h2>
            
            <!-- Add New Payment Form -->
            <form id="paymentForm" class="payment-form">
                <!-- Student Information Section -->
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" readonly>
                </div>
                <div class="form-group">
                    <label for="student_name">Student Name</label>
                    <input type="text" id="student_name" name="student_name" readonly>
                </div>
                <div class="form-group">
                    <label for="year_level">Year Level</label>
                    <input type="text" id="year_level" name="year_level" readonly>
                </div>
                
                <div class="form-group">
                    <label for="or_number">Official Receipt Number</label>
                        <input type="number" id="or_number" name="or_number" placeholder="Enter OR number" required>
                </div>
                <div class="form-group">
                    <label for="current_date">Date</label>
                    <input type="text" id="current_date" name="current_date" readonly>
                </div>

                <!-- Payment Details Section -->
                <div class="form-group">
                    <label for="payment_for">Payment For</label>
                    <select id="payment_for" name="payment_for" required>
                        <option value="">Select Payment For</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>
                    <small id="remaining_amount" style="color: #740606; font-size: 12px;"></small>
                </div>

                <div style="display: flex; justify-content: center;">
                   <button class="btn-submit">Submit</button>
               </div>
            </form>

            <!-- Payment History Table -->
            <div id="paymentHistoryTable"></div>

            <!-- Payment Summary Section (Moved to bottom) -->
            <div class="payment-summary" style="margin-top: 30px; border-top: 1px solid #dee2e6; padding-top: 20px;">
                <h3>Student Payment Summary</h3>
                <div class="progress-container">
                    <div class="total-progress">
                        <div class="progress-bar">
                            <div id="totalProgressBar" class="progress-fill"></div>
                        </div>
                        <div class="progress-text">
                            <span id="totalProgressText">0%</span>
                            <span id="totalAmountText">₱0.00 / ₱0.00</span>
                        </div>
                    </div>
                </div>
                <div class="payment-summary-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Total Amount</th>
                                <th>Amount Paid</th>
                                <th>Remaining</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody id="paymentSummaryBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Payment Modal -->
<div id="editPaymentModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Payment</h2>
        
        <form id="editPaymentForm" class="payment-form">
            <div class="form-group">
                <label for="edit_or_number">Official Receipt Number</label>
                <input type="number" id="edit_or_number" name="or_number" required>
            </div>
            
            <div class="form-group">
                <label for="edit_payment_for">Payment For</label>
                <select id="edit_payment_for" name="payment_for" required>
                    <option value="">Select Payment For</option>
                    @foreach($activePaymentFors as $payment_for)
                    <option value="{{ $payment_for->id }}" data-amount="{{ $payment_for->amount }}">
                        {{ $payment_for->name }} - ₱{{ number_format($payment_for->amount, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_amount">Amount</label>
                <input type="number" id="edit_amount" name="amount" step="0.01" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Payment</button>
                <button type="button" onclick="closeEditPaymentModal()" style="background-color: #ccc; color: #333; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-top: 10px; margin-bottom: 20px;">Cancel</button>
            </div>
        </form>
    </div>
</div>

    <div id="newPaymentModal" class="new-payment-modal">
        <div class="new-payment-content" style="max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="closeNewPaymentModal()">&times;</span>
            <h2>Create New Payment</h2>
            <form id="newPaymentForm" class="new-payment-form">
                <div class="form-group">
                    <label for="payment_name">Payment Name</label>
                    <input type="text" id="payment_name" name="payment_name" required>
                </div>
                <div class="form-group">
                    <label for="payment_amount">Payment Amount</label>
                    <input type="number" id="payment_amount" name="payment_amount" step="0.01" required>
                </div>
                <div style="text-align: center;">
                    <button type="submit" class="btn-create">Create Payment For</button></div>
            </form>

            <!-- Payment For Table -->
<div class="payment-for-table">
    <h3>Payment For List</h3>
    <table>
        <thead>
            <tr>
                <th>Payment Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody id="paymentForTableBody">
            @foreach($activePaymentFors as $payment_for)
            <tr onclick="showPaymentForDetails({{ $payment_for->id }})" style="cursor: pointer;">
                <td>{{ $payment_for->name }}</td>
                <td>₱{{ number_format($payment_for->amount, 2) }}</td>
            </tr>
            @endforeach
                  </tbody>
                   </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment For Details Modal -->
    <div id="paymentForDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaymentForDetailsModal()">&times;</span>
            <h2>Payment For Details</h2>
            <div id="paymentForDetails">
                <form id="editPaymentForForm" class="payment-form" style="display: none;">
                    <div class="form-group">
                        <label for="edit_payment_name">Payment Name</label>
                        <input type="text" id="edit_payment_name" name="payment_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_payment_amount">Payment Amount</label>
                        <input type="number" id="edit_payment_amount" name="payment_amount" step="0.01" required>
                    </div>
                    <div class="payment-actions">
                        <button type="submit" style="background-color: #740909;color: white;padding: 10px 20px;border: none;border-radius: 5px;cursor: pointer;font-size: 14px;margin-top: 10px;margin-bottom: 20px;">Save Changes</button>
                        <button type="button" onclick="cancelEdit()" style="background-color: #f0f0f0; color: #333; padding: 10px 20px; border: 1px solid #ccc; border-radius: 5px; cursor: pointer; font-size: 14px; margin-top: 10px; margin-bottom: 20px;">Cancel</button>
                    </div>
                </form>
                <div id="paymentForDetailsView">
                    <div class="details-group">
                        <label>Payment Name:</label>
                        <p id="view_payment_name"></p>
                    </div>
                    <div class="details-group">
                        <label>Amount:</label>
                        <p id="view_payment_amount"></p>
                    </div>
                    <div class="details-group">
                        <label>Status:</label>
                        <p id="view_status"></p>
                    </div>
                    <div class="details-group">
                        <label>Created At:</label>
                        <p id="view_created_at"></p>
                    </div>
                    <div class="details-group">
                        <label>Last Updated:</label>
                        <p id="view_updated_at"></p>
                    </div>
                    <div class="payment-actions">
                        <button class="btn-edit" onclick="startEdit()">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/payment.js') }}"></script>
    <script>
        // Initialize the payment management functionality
        document.addEventListener('DOMContentLoaded', function() {
            initPaymentManagement();
        });
</body>
</html>

