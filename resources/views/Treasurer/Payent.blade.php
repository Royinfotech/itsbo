<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            text-align: center;
            position: relative;
            background-color: #f9fafb;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            padding-top: 10px;
        }

        /* Background Image */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('./pictures/LoginBackg.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.3;
            z-index: -1;
        }

        /* Main Container */
        .main-container {
            width: 90%;
            max-width: 1100px;
            margin-right: 235px;
            background: white;
            margin-top: 15px;
            margin-left: 280px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-top: 5px solid maroon;
        }

        /* Title */
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #042546;
            margin-bottom: 15px;
        }

        /* Search Container */
        .search-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box input {
            width: 230px;
            padding: 8px 35px 8px 12px;
            border: 2px solid #740606;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
        }

        .search-box i {
            position: absolute;
            right: 12px;
            color: #740606;
            font-size: 14px;
        }

        /* Improve table responsiveness */
.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
}

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background-color: #740606;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        thead th {
            padding: 10px;
            font-size: 13px;
            text-transform: uppercase;
        }

        tbody tr {
            background-color: #f9fafb;
            transition: background 0.3s ease;
        }

        tbody tr:hover {
            background-color: #f1f5f9;
        }

        tbody td {
            padding: 8px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
        }

        /* Open Button */
        .open-btn {
            background-color: #740606;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 13px;
        }

        .open-btn:hover {
            background-color: #550404;
        }

        /* Adjust search container for smaller screens */
@media screen and (max-width: 768px) {
    .search-container {
        flex-direction: column;
        gap: 10px;
    }
    .search-box input {
        width: 100%;
    }
}

/* Adjust layout for mobile screens */
@media screen and (max-width: 480px) {
    .main-container {
        width: 100%;
        margin: 10px;
        padding: 15px;
    }
    .search-box input {
        width: 100%;
    }
    .add-payment-btn {
        position: relative;
        top: auto;
        right: auto;
        width: 100%;
        text-align: center;
    }
}

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .modal-content {
            position: relative;
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #740606;
        }

        .payment-form {
            display: grid;
            gap: 15px;
            margin-top: 20px;
        }

        .form-group {
            display: grid;
            gap: 5px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input, .form-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .payment-history {
            margin-top: 30px;
        }

        .payment-history table {
            margin-top: 15px;
        }

        .btn-submit {
            background-color: #740606;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .btn-submit:hover {
            background-color: #550404;
        }

        /* Add Payment Button */
        .add-payment-btn {
            position: absolute;
            top: 15px;
            right: 70px;
            background-color: #740606;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        .add-payment-btn:hover {
            background-color: #550404;
        }

        .add-payment-btn i {
            font-size: 16px;
        }

        /* New Payment Modal */
        .new-payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }

        .new-payment-content {
            position: relative;
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-height: 90vh;
            overflow-y: auto;
        }

        .new-payment-content h2 {
            color: #042546;
            margin-bottom: 20px;
            text-align: center;
        }

        .new-payment-form {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            display: grid;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #333;
        }

        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-create {
            background-color: #740606;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-create:hover {
            background-color: #550404;
        }

        /* Payment Types Table Styles */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #e6f4ea;
            color: #1e7e34;
        }

        .status-badge.inactive {
            background-color: #fce8e8;
            color: #dc3545;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px 8px;
            margin: 0 2px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .action-btn.edit-btn {
            color: #740606;
        }

        .action-btn.delete-btn {
            color: #dc3545;
        }

        .action-btn:hover {
            background-color: #f8f9fa;
        }

        /* Update the table container for payment types */
        .table-container {
            width: 100%;
            height: 250px;
            overflow-y: auto;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            margin-top: 15px;
        }

        /* Payment For Table Styles */
        .payment-for-table {
            margin-top: 20px;
        }

        .payment-for-table h3 {
            color: #042546;
            margin-bottom: 15px;
            text-align: left;
        }

        .payment-for-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .payment-for-table th {
            background-color: #740606;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }

        .payment-for-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .payment-for-table tr:hover {
            background-color: #f5f5f5;
        }

        /* Payment For Details Modal Styles */
        .details-group {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .details-group label {
            font-weight: bold;
            color: #740606;
            display: block;
            margin-bottom: 5px;
        }

        .details-group p {
            margin: 0;
            color: #333;
        }

        .payment-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #740606;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-edit:hover {
            background-color: #550404;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .or-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .or-input-group input {
            flex: 1;
        }

        .btn-generate {
            background-color: #740606;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-generate:hover {
            background-color: #550404;
        }

        .form-group input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        /* Payment Summary Styles */
        .payment-summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .payment-summary h3 {
            color: #042546;
            margin-bottom: 15px;
            font-size: 16px;
        }

.payment-breakdown {
    margin-top: 15px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.breakdown-label {
    font-weight: 500;
    color: #444;
}

.breakdown-amount {
    color: #666;
}

.breakdown-total {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #800000;
    text-align: right;
    font-size: 1.2em;
    color: #800000;
}

.collection-summary {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.collection-summary h3 {
    color: #333;
    margin-bottom: 15px;
}

.progress-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
}

.progress-bar {
    height: 30px;
    background: #e9ecef;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 15px;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: #800000;
    transition: width 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-label {
    color: white;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.collection-amounts {
    text-align: center;
}

.amount-details {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 1.1em;
}

.collected .value {
    color: #800000;
    font-weight: bold;
}

.expected .value {
    color: #666;
}

.label {
    font-weight: 500;
    margin-right: 8px;
}
    </style>
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
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="studentSearch" placeholder="Search by Student ID or Name...">
                <i class="fas fa-search search-icon"></i>
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
                    <tr onclick="viewPaymentDetails({{ $student->id }})" style="cursor: pointer;">
                        <td data-student-id>{{ $student->student_id }}</td>
                        <td data-student-name>{{ $student->student_name }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td>₱{{ number_format($student->total_paid ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


 <!-- Add Payment Summary Section -->
<div class="main-container" style="margin-top: 20px;">
    <h2 class="title">Payment Summary</h2>
    
    @php
        $activeStudentCount = App\Models\Student::where('status', 'active')->count();
        $totalExpectedAll = 0;
        $totalPaidAll = 0;
    @endphp

    <div class="payment-summary">
        <!-- Total Summary Card -->
        <div class="summary-card">
            <h3>Active Students: {{ $activeStudentCount }}</h3>
            <div class="payment-breakdown">
                @foreach($payment_fors as $payment_for)
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
        $expectedAmount = $payment_for->amount * $activeStudentCount;
        $totalPaidAll = App\Models\Payment::sum('amount');
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
                    <div class="or-input-group">
                        <input type="text" id="or_number" name="or_number" required>
                        
                    </div>
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

                <button type="submit" class="btn-submit">Submit Payment</button>
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

    <!-- New Payment Modal -->
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
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <input type="text" id="description" name="description">
                </div>
                <button type="submit" class="btn-create">Create Payment For</button>
            </form>

            <!-- Payment For Table -->
            <div class="payment-for-table">
                <h3>Payment For List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Payment Name</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="paymentForTableBody">
                        @foreach($payment_fors as $payment_for)
                        <tr onclick="showPaymentForDetails({{ $payment_for->id }})" style="cursor: pointer;">
                            <td>{{ $payment_for->name }}</td>
                            <td>₱{{ number_format($payment_for->amount, 2) }}</td>
                            <td>{{ $payment_for->description ?? 'No description' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                    <div class="form-group">
                        <label for="edit_description">Description (Optional)</label>
                        <input type="text" id="edit_description" name="description">
                    </div>
                    <div class="payment-actions">
                        <button type="submit" class="btn-edit">Save Changes</button>
                        <button type="button" class="btn-cancel" onclick="cancelEdit()">Cancel</button>
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
                        <label>Description:</label>
                        <p id="view_description"></p>
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

    <script>
        // Modal functionality
        const modal = document.getElementById('paymentModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        const paymentForm = document.getElementById('paymentForm');
        let currentStudentId = null;

        function viewPaymentDetails(studentId) {
            currentStudentId = studentId;
            modal.style.display = "block";
            
            // Fetch student details
            fetch(`/treasurer/payments/${studentId}`)
                .then(response => response.json())
                .then(data => {
                    // Store student info in hidden fields
                    document.getElementById('student_id').value = data.student.student_id;
                    document.getElementById('student_name').value = 
                        `${data.student.student_name}`.trim();
                    
                    // Update current date
                    updateCurrentDate();
                    
                    // Generate OR number
                    generateOR();
                    
                    // Update year level
                    document.getElementById('year_level').value = data.student.year_level;
                    
                    // Update payment for dropdown with remaining amounts
                    const paymentForSelect = document.getElementById('payment_for');
                    paymentForSelect.innerHTML = '<option value="">Select Payment For</option>';
                    
                    // Calculate total amount and total paid
                    let totalAmount = 0;
                    let totalPaid = 0;
                    
                    data.payment_fors.forEach(paymentFor => {
                        totalAmount += parseFloat(paymentFor.amount);
                        totalPaid += parseFloat(paymentFor.total_paid);
                        
                        const option = document.createElement('option');
                        option.value = paymentFor.id;
                        option.dataset.amount = paymentFor.amount;
                        option.dataset.remaining = paymentFor.remaining_amount;
                        option.textContent = `${paymentFor.name} - ₱${parseFloat(paymentFor.amount).toFixed(2)} (Remaining: ₱${parseFloat(paymentFor.remaining_amount).toFixed(2)})`;
                        paymentForSelect.appendChild(option);
                    });
                    
                    // Update total progress bar
                    const totalProgress = totalAmount > 0 ? (totalPaid / totalAmount) * 100 : 0;
                    document.getElementById('totalProgressBar').style.width = `${totalProgress}%`;
                    document.getElementById('totalProgressText').textContent = `${totalProgress.toFixed(1)}%`;
                    document.getElementById('totalAmountText').textContent = `₱${totalPaid.toFixed(2)} / ₱${totalAmount.toFixed(2)}`;
                    
                    // Update payment summary table
                    const summaryBody = document.getElementById('paymentSummaryBody');
                    summaryBody.innerHTML = data.payment_fors.map(paymentFor => {
                        const progress = paymentFor.amount > 0 ? (paymentFor.total_paid / paymentFor.amount) * 100 : 0;
                        return `
                            <tr>
                                <td>${paymentFor.name}</td>
                                <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.total_paid).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.remaining_amount).toFixed(2)}</td>
                                <td class="progress-cell">
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: ${progress}%"></div>
                                    </div>
                                    <span style="font-size: 12px;">${progress.toFixed(1)}%</span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                    
                    // Display payment history
                    const historyTable = document.getElementById('paymentHistoryTable');
                    historyTable.innerHTML = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>OR Number</th>
                                    <th>Amount</th>
                                    <th>Payment For</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.payments.map(payment => `
                                    <tr>
                                        <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                                        <td>${payment.or_number || 'N/A'}</td>
                                        <td>₱${parseFloat(payment.amount).toFixed(2)}</td>
                                        <td>${payment.payment_for_name || 'N/A'}</td>
                                        <td>${payment.status}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching student details');
                });
        }

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == newPaymentModal) {
                newPaymentModal.style.display = "none";
            }
            if (event.target == paymentForDetailsModal) {
                paymentForDetailsModal.style.display = "none";
            }
        }

        paymentForm.onsubmit = function(e) {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('amount').value);
            
            if (amount <= 0) {
                alert('Payment amount must be greater than zero');
                return;
            }

            const formData = new FormData(paymentForm);
            formData.append('student_id', currentStudentId);
            formData.append('payment_date', new Date().toISOString());
            
            // Get the selected payment_for_id from the dropdown
            const paymentForSelect = document.getElementById('payment_for');
            formData.append('payment_for_id', paymentForSelect.value);

            fetch('/treasurer/payments', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    viewPaymentDetails(currentStudentId); // Refresh the modal
                    updateMainPaymentSummary(); // Refresh the main summary
                    paymentForm.reset();
                } else {
                    alert(data.message || 'Error adding payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error adding payment');
            });
        }

        // New Payment Modal functionality
        const newPaymentModal = document.getElementById('newPaymentModal');
        const newPaymentForm = document.getElementById('newPaymentForm');

        function showNewPaymentModal() {
            newPaymentModal.style.display = "block";
        }

        function closeNewPaymentModal() {
            newPaymentModal.style.display = "none";
        }

        newPaymentForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(newPaymentForm);

            fetch('/treasurer/payment-fors', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment For created successfully');
                    newPaymentForm.reset();
                    // Refresh the payment for table
                    fetchPaymentFors();
                } else {
                    alert(data.message || 'Error creating Payment For');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message && error.message.includes('already exists')) {
                    alert('A payment with this name already exists. Please use a different name.');
                } else {
                    alert(error.message || 'Error creating Payment For');
                }
            });
        }

        function fetchPaymentFors() {
            fetch('/treasurer/payment-fors')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('paymentForTableBody');
                    tbody.innerHTML = data.map(paymentFor => `
                        <tr>
                            <td>${paymentFor.name}</td>
                            <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                            <td>${paymentFor.description || 'No description'}</td>
                        </tr>
                    `).join('');
                })
                .catch(error => console.error('Error:', error));
        }

        // Payment For Details Modal functionality
        const paymentForDetailsModal = document.getElementById('paymentForDetailsModal');
        let currentPaymentForId = null;

        function showPaymentForDetails(id) {
            currentPaymentForId = id;
            paymentForDetailsModal.style.display = "block";
            
            // Fetch payment for details
            fetch(`/treasurer/payment-fors/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Update view mode
                    document.getElementById('view_payment_name').textContent = data.name;
                    document.getElementById('view_payment_amount').textContent = `₱${parseFloat(data.amount).toFixed(2)}`;
                    document.getElementById('view_description').textContent = data.description || 'No description';
                    document.getElementById('view_status').textContent = data.is_active ? 'Active' : 'Inactive';
                    document.getElementById('view_created_at').textContent = new Date(data.created_at).toLocaleString();
                    document.getElementById('view_updated_at').textContent = new Date(data.updated_at).toLocaleString();
                    
                    // Update edit form
                    document.getElementById('edit_payment_name').value = data.name;
                    document.getElementById('edit_payment_amount').value = data.amount;
                    document.getElementById('edit_description').value = data.description || '';
                    
                    // Show view mode
                    document.getElementById('paymentForDetailsView').style.display = 'block';
                    document.getElementById('editPaymentForForm').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching payment for details');
                });
        }

        function closePaymentForDetailsModal() {
            paymentForDetailsModal.style.display = "none";
            currentPaymentForId = null;
        }

        function startEdit() {
            document.getElementById('paymentForDetailsView').style.display = 'none';
            document.getElementById('editPaymentForForm').style.display = 'block';
        }

        function cancelEdit() {
            document.getElementById('paymentForDetailsView').style.display = 'block';
            document.getElementById('editPaymentForForm').style.display = 'none';
        }

        document.getElementById('editPaymentForForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Create an object with the form data
            const data = {
                payment_name: formData.get('payment_name'),
                payment_amount: formData.get('payment_amount'),
                description: formData.get('description')
            };

            fetch(`/treasurer/payment-fors/${currentPaymentForId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment For updated successfully');
                    showPaymentForDetails(currentPaymentForId); // Refresh the details
                    fetchPaymentFors(); // Refresh the table
                } else {
                    alert(data.message || 'Error updating Payment For');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error updating Payment For');
            });
        });

        // Add this to your existing JavaScript
        function generateOR() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const orNumber = `OR-${year}${month}${day}-${random}`;
            document.getElementById('or_number').value = orNumber;
        }

        // Update current date
        function updateCurrentDate() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current_date').value = now.toLocaleDateString('en-US', options);
        }

        // Update amount when payment for is selected
        document.getElementById('payment_for').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const remainingAmount = parseFloat(selectedOption.dataset.remaining);
                const originalAmount = parseFloat(selectedOption.dataset.amount);
                
                // Set the amount input to the remaining amount
                document.getElementById('amount').value = remainingAmount;
                
                // Show remaining amount info
                document.getElementById('remaining_amount').textContent = 
                    `Original Amount: ₱${originalAmount.toFixed(2)} | Remaining: ₱${remainingAmount.toFixed(2)}`;
            } else {
                document.getElementById('amount').value = '';
                document.getElementById('remaining_amount').textContent = '';
            }
        });

        // Add validation to ensure payment amount doesn't exceed remaining amount
        document.getElementById('amount').addEventListener('input', function() {
            const selectedOption = document.getElementById('payment_for').options[document.getElementById('payment_for').selectedIndex];
            if (selectedOption.value) {
                const remainingAmount = parseFloat(selectedOption.dataset.remaining);
                const enteredAmount = parseFloat(this.value);
                
                if (enteredAmount > remainingAmount) {
                    alert('Payment amount cannot exceed the remaining amount');
                    this.value = remainingAmount;
                } else if (enteredAmount <= 0) {
                    alert('Payment amount must be greater than zero');
                    this.value = 0.01;
                }
            }
        });

        // Add this function to update the main payment summary
        function updateMainPaymentSummary() {
            fetch('/treasurer/payment-fors')
                .then(response => response.json())
                .then(data => {
                    let totalAmount = 0;
                    let totalPaid = 0;
                    
                    data.forEach(paymentFor => {
                        totalAmount += parseFloat(paymentFor.amount);
                        totalPaid += parseFloat(paymentFor.total_paid || 0);
                    });
                    
                    // Update total progress bar
                    const totalProgress = totalAmount > 0 ? (totalPaid / totalAmount) * 100 : 0;
                    document.getElementById('mainTotalProgressBar').style.width = `${totalProgress}%`;
                    document.getElementById('mainTotalProgressText').textContent = `${totalProgress.toFixed(1)}%`;
                    document.getElementById('mainTotalAmountText').textContent = `₱${totalPaid.toFixed(2)} / ₱${totalAmount.toFixed(2)}`;
                    
                    // Update payment summary table
                    const summaryBody = document.getElementById('mainPaymentSummaryBody');
                    summaryBody.innerHTML = data.map(paymentFor => {
                        const progress = paymentFor.amount > 0 ? ((paymentFor.total_paid || 0) / paymentFor.amount) * 100 : 0;
                        return `
                            <tr>
                                <td>${paymentFor.name}</td>
                                <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.total_paid || 0).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.amount - (paymentFor.total_paid || 0)).toFixed(2)}</td>
                                <td class="progress-cell">
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: ${progress}%"></div>
                                    </div>
                                    <span style="font-size: 12px;">${progress.toFixed(1)}%</span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                })
                .catch(error => console.error('Error:', error));
        }

        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateMainPaymentSummary();

            const searchInput = document.getElementById('studentSearch');
            const studentTableBody = document.getElementById('studentTableBody');
            const originalRows = [...studentTableBody.getElementsByTagName('tr')];

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                const filteredRows = originalRows.filter(row => {
                    const studentId = row.querySelector('[data-student-id]')?.textContent.toLowerCase() || '';
                    const studentName = row.querySelector('[data-student-name]')?.textContent.toLowerCase() || '';
                    
                    return studentId.includes(searchTerm) || studentName.includes(searchTerm);
                });

                // Clear current table
                studentTableBody.innerHTML = '';

                if (filteredRows.length === 0) {
                    const noResultRow = document.createElement('tr');
                    noResultRow.innerHTML = `
                        <td colspan="4" class="text-center">No students found</td>
                    `;
                    studentTableBody.appendChild(noResultRow);
                } else {
                    filteredRows.forEach(row => studentTableBody.appendChild(row.cloneNode(true)));
                }
            });
        });

        function showPaymentModal(studentId, studentName, yearLevel) {
    currentStudentId = studentId;
    modal.style.display = "block";
    
    // Fetch student details
    fetch(`/treasurer/payments/${studentId}`)
        .then(response => response.json())
        .then(data => {
            // Store student info in hidden fields
            document.getElementById('student_id').value = data.student.student_id;
            document.getElementById('student_name').value = 
                `${data.student.student_name}`.trim();
            
            // Update current date
            updateCurrentDate();
            
            // Generate OR number
            generateOR();
            
            // Update year level
            document.getElementById('year_level').value = data.student.year_level;
            
            // Update payment for dropdown with remaining amounts
            const paymentForSelect = document.getElementById('payment_for');
            paymentForSelect.innerHTML = '<option value="">Select Payment For</option>';
            
            // Calculate total amount and total paid
            let totalAmount = 0;
            let totalPaid = 0;
            
            data.payment_fors.forEach(paymentFor => {
                totalAmount += parseFloat(paymentFor.amount);
                totalPaid += parseFloat(paymentFor.total_paid);
                
                const option = document.createElement('option');
                option.value = paymentFor.id;
                option.dataset.amount = paymentFor.amount;
                option.dataset.remaining = paymentFor.remaining_amount;
                option.textContent = `${paymentFor.name} - ₱${parseFloat(paymentFor.amount).toFixed(2)} (Remaining: ₱${parseFloat(paymentFor.remaining_amount).toFixed(2)})`;
                paymentForSelect.appendChild(option);
            });
            
            // Get fines for the student
            fetch(`/treasurer/student/${studentId}/fines`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add Fines & Violation to payment options if there are fines
                const paymentForSelect = document.getElementById('payment_for');
                if (data.data.total_fines > 0) {
                    const fineOption = document.createElement('option');
                    fineOption.value = 'fines';
                    fineOption.setAttribute('data-fine-amount', data.data.total_fines);
                    fineOption.textContent = `Fines & Violation (₱${data.data.total_fines.toFixed(2)})`;
                    paymentForSelect.appendChild(fineOption);
                    
                    // Add fines breakdown as hidden element
                    const breakdownHtml = data.data.breakdown.map(fine => `
                        <div class="fine-item">
                            <span>Date: ${new Date(fine.date).toLocaleDateString()}</span>
                            <span>Event: ${fine.event}</span>
                            <span>Amount: ₱${fine.amount.toFixed(2)}</span>
                            <div class="fine-details">
                                ${Object.entries(fine.breakdown)
                                    .filter(([key, amount]) => amount > 0)
                                    .map(([key, amount]) => `
                                        <div>${key.toUpperCase()}: ₱${amount.toFixed(2)}</div>
                                    `).join('')}
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('fines-breakdown').innerHTML = breakdownHtml;
                }
            }
        })
        .catch(error => console.error('Error fetching fines:', error));
            
            // Update total progress bar
            const totalProgress = totalAmount > 0 ? (totalPaid / totalAmount) * 100 : 0;
            document.getElementById('totalProgressBar').style.width = `${totalProgress}%`;
            document.getElementById('totalProgressText').textContent = `${totalProgress.toFixed(1)}%`;
            document.getElementById('totalAmountText').textContent = `₱${totalPaid.toFixed(2)} / ₱${totalAmount.toFixed(2)}`;
            
            // Update payment summary table
            const summaryBody = document.getElementById('paymentSummaryBody');
            summaryBody.innerHTML = data.payment_fors.map(paymentFor => {
                const progress = paymentFor.amount > 0 ? (paymentFor.total_paid / paymentFor.amount) * 100 : 0;
                return `
                    <tr>
                        <td>${paymentFor.name}</td>
                        <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                        <td>₱${parseFloat(paymentFor.total_paid).toFixed(2)}</td>
                        <td>₱${parseFloat(paymentFor.remaining_amount).toFixed(2)}</td>
                        <td class="progress-cell">
                            <div class="mini-progress">
                                <div class="mini-progress-fill" style="width: ${progress}%"></div>
                            </div>
                            <span style="font-size: 12px;">${progress.toFixed(1)}%</span>
                        </td>
                    </tr>
                `;
            }).join('');
            
            // Display payment history
            const historyTable = document.getElementById('paymentHistoryTable');
            historyTable.innerHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>OR Number</th>
                            <th>Amount</th>
                            <th>Payment For</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.payments.map(payment => `
                            <tr>
                                <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                                <td>${payment.or_number || 'N/A'}</td>
                                <td>₱${parseFloat(payment.amount).toFixed(2)}</td>
                                <td>${payment.payment_for_name || 'N/A'}</td>
                                <td>${payment.status}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching student details');
        });
}

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == newPaymentModal) {
                newPaymentModal.style.display = "none";
            }
            if (event.target == paymentForDetailsModal) {
                paymentForDetailsModal.style.display = "none";
            }
        }

        paymentForm.onsubmit = function(e) {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('amount').value);
            
            if (amount <= 0) {
                alert('Payment amount must be greater than zero');
                return;
            }

            const formData = new FormData(paymentForm);
            formData.append('student_id', currentStudentId);
            formData.append('payment_date', new Date().toISOString());
            
            // Get the selected payment_for_id from the dropdown
            const paymentForSelect = document.getElementById('payment_for');
            formData.append('payment_for_id', paymentForSelect.value);

            fetch('/treasurer/payments', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    viewPaymentDetails(currentStudentId); // Refresh the modal
                    updateMainPaymentSummary(); // Refresh the main summary
                    paymentForm.reset();
                } else {
                    alert(data.message || 'Error adding payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error adding payment');
            });
        }

        // New Payment Modal functionality
        const newPaymentModal = document.getElementById('newPaymentModal');
        const newPaymentForm = document.getElementById('newPaymentForm');

        function showNewPaymentModal() {
            newPaymentModal.style.display = "block";
        }

        function closeNewPaymentModal() {
            newPaymentModal.style.display = "none";
        }

        newPaymentForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(newPaymentForm);

            fetch('/treasurer/payment-fors', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment For created successfully');
                    newPaymentForm.reset();
                    // Refresh the payment for table
                    fetchPaymentFors();
                } else {
                    alert(data.message || 'Error creating Payment For');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message && error.message.includes('already exists')) {
                    alert('A payment with this name already exists. Please use a different name.');
                } else {
                    alert(error.message || 'Error creating Payment For');
                }
            });
        }

        function fetchPaymentFors() {
            fetch('/treasurer/payment-fors')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('paymentForTableBody');
                    tbody.innerHTML = data.map(paymentFor => `
                        <tr>
                            <td>${paymentFor.name}</td>
                            <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                            <td>${paymentFor.description || 'No description'}</td>
                        </tr>
                    `).join('');
                })
                .catch(error => console.error('Error:', error));
        }

        // Payment For Details Modal functionality
        const paymentForDetailsModal = document.getElementById('paymentForDetailsModal');
        let currentPaymentForId = null;

        function showPaymentForDetails(id) {
            currentPaymentForId = id;
            paymentForDetailsModal.style.display = "block";
            
            // Fetch payment for details
            fetch(`/treasurer/payment-fors/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Update view mode
                    document.getElementById('view_payment_name').textContent = data.name;
                    document.getElementById('view_payment_amount').textContent = `₱${parseFloat(data.amount).toFixed(2)}`;
                    document.getElementById('view_description').textContent = data.description || 'No description';
                    document.getElementById('view_status').textContent = data.is_active ? 'Active' : 'Inactive';
                    document.getElementById('view_created_at').textContent = new Date(data.created_at).toLocaleString();
                    document.getElementById('view_updated_at').textContent = new Date(data.updated_at).toLocaleString();
                    
                    // Update edit form
                    document.getElementById('edit_payment_name').value = data.name;
                    document.getElementById('edit_payment_amount').value = data.amount;
                    document.getElementById('edit_description').value = data.description || '';
                    
                    // Show view mode
                    document.getElementById('paymentForDetailsView').style.display = 'block';
                    document.getElementById('editPaymentForForm').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching payment for details');
                });
        }

        function closePaymentForDetailsModal() {
            paymentForDetailsModal.style.display = "none";
            currentPaymentForId = null;
        }

        function startEdit() {
            document.getElementById('paymentForDetailsView').style.display = 'none';
            document.getElementById('editPaymentForForm').style.display = 'block';
        }

        function cancelEdit() {
            document.getElementById('paymentForDetailsView').style.display = 'block';
            document.getElementById('editPaymentForForm').style.display = 'none';
        }

        document.getElementById('editPaymentForForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Create an object with the form data
            const data = {
                payment_name: formData.get('payment_name'),
                payment_amount: formData.get('payment_amount'),
                description: formData.get('description')
            };

            fetch(`/treasurer/payment-fors/${currentPaymentForId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment For updated successfully');
                    showPaymentForDetails(currentPaymentForId); // Refresh the details
                    fetchPaymentFors(); // Refresh the table
                } else {
                    alert(data.message || 'Error updating Payment For');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error updating Payment For');
            });
        });

        // Add this to your existing JavaScript
        function generateOR() {
            const date = new Date();
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            const orNumber = `OR-${year}${month}${day}-${random}`;
            document.getElementById('or_number').value = orNumber;
        }

        // Update current date
        function updateCurrentDate() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current_date').value = now.toLocaleDateString('en-US', options);
        }

        // Update amount when payment for is selected
        document.getElementById('payment_for').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const remainingAmount = parseFloat(selectedOption.dataset.remaining);
                const originalAmount = parseFloat(selectedOption.dataset.amount);
                
                // Set the amount input to the remaining amount
                document.getElementById('amount').value = remainingAmount;
                
                // Show remaining amount info
                document.getElementById('remaining_amount').textContent = 
                    `Original Amount: ₱${originalAmount.toFixed(2)} | Remaining: ₱${remainingAmount.toFixed(2)}`;
            } else {
                document.getElementById('amount').value = '';
                document.getElementById('remaining_amount').textContent = '';
            }
        });

        // Add validation to ensure payment amount doesn't exceed remaining amount
        document.getElementById('amount').addEventListener('input', function() {
            const selectedOption = document.getElementById('payment_for').options[document.getElementById('payment_for').selectedIndex];
            if (selectedOption.value) {
                const remainingAmount = parseFloat(selectedOption.dataset.remaining);
                const enteredAmount = parseFloat(this.value);
                
                if (enteredAmount > remainingAmount) {
                    alert('Payment amount cannot exceed the remaining amount');
                    this.value = remainingAmount;
                } else if (enteredAmount <= 0) {
                    alert('Payment amount must be greater than zero');
                    this.value = 0.01;
                }
            }
        });

        // Add this function to update the main payment summary
        function updateMainPaymentSummary() {
            fetch('/treasurer/payment-fors')
                .then(response => response.json())
                .then(data => {
                    let totalAmount = 0;
                    let totalPaid = 0;
                    
                    data.forEach(paymentFor => {
                        totalAmount += parseFloat(paymentFor.amount);
                        totalPaid += parseFloat(paymentFor.total_paid || 0);
                    });
                    
                    // Update total progress bar
                    const totalProgress = totalAmount > 0 ? (totalPaid / totalAmount) * 100 : 0;
                    document.getElementById('mainTotalProgressBar').style.width = `${totalProgress}%`;
                    document.getElementById('mainTotalProgressText').textContent = `${totalProgress.toFixed(1)}%`;
                    document.getElementById('mainTotalAmountText').textContent = `₱${totalPaid.toFixed(2)} / ₱${totalAmount.toFixed(2)}`;
                    
                    // Update payment summary table
                    const summaryBody = document.getElementById('mainPaymentSummaryBody');
                    summaryBody.innerHTML = data.map(paymentFor => {
                        const progress = paymentFor.amount > 0 ? ((paymentFor.total_paid || 0) / paymentFor.amount) * 100 : 0;
                        return `
                            <tr>
                                <td>${paymentFor.name}</td>
                                <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.total_paid || 0).toFixed(2)}</td>
                                <td>₱${parseFloat(paymentFor.amount - (paymentFor.total_paid || 0)).toFixed(2)}</td>
                                <td class="progress-cell">
                                    <div class="mini-progress">
                                        <div class="mini-progress-fill" style="width: ${progress}%"></div>
                                    </div>
                                    <span style="font-size: 12px;">${progress.toFixed(1)}%</span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                })
                .catch(error => console.error('Error:', error));
        }

        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateMainPaymentSummary();

            const searchInput = document.getElementById('studentSearch');
            const studentTableBody = document.getElementById('studentTableBody');
            const originalRows = [...studentTableBody.getElementsByTagName('tr')];

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                const filteredRows = originalRows.filter(row => {
                    const studentId = row.querySelector('[data-student-id]')?.textContent.toLowerCase() || '';
                    const studentName = row.querySelector('[data-student-name]')?.textContent.toLowerCase() || '';
                    
                    return studentId.includes(searchTerm) || studentName.includes(searchTerm);
                });

                // Clear current table
                studentTableBody.innerHTML = '';

                if (filteredRows.length === 0) {
                    const noResultRow = document.createElement('tr');
                    noResultRow.innerHTML = `
                        <td colspan="4" class="text-center">No students found</td>
                    `;
                    studentTableBody.appendChild(noResultRow);
                } else {
                    filteredRows.forEach(row => studentTableBody.appendChild(row.cloneNode(true)));
                }
            });
        });

        function showPaymentModal(studentId, studentName, yearLevel) {
    currentStudentId = studentId;
    modal.style.display = "block";
    
    // Fetch student details
    fetch(`/treasurer/payments/${studentId}`)
        .then(response => response.json())
        .then(data => {
            // Store student info in hidden fields
            document.getElementById('student_id').value = data.student.student_id;
            document.getElementById('student_name').value = 
                `${data.student.student_name}`.trim();
            
            // Update current date
            updateCurrentDate();
            
            // Generate OR number
            generateOR();
            
            // Update year level
            document.getElementById('year_level').value = data.student.year_level;
            
            // Update payment for dropdown with remaining amounts
            const paymentForSelect = document.getElementById('payment_for');
            paymentForSelect.innerHTML = '<option value="">Select Payment For</option>';
            
            // Calculate total amount and total paid
            let totalAmount = 0;
            let totalPaid = 0;
            
            data.payment_fors.forEach(paymentFor => {
                totalAmount += parseFloat(paymentFor.amount);
                totalPaid += parseFloat(paymentFor.total_paid);
                
                const option = document.createElement('option');
                option.value = paymentFor.id;
                option.dataset.amount = paymentFor.amount;
                option.dataset.remaining = paymentFor.remaining_amount;
                option.textContent = `${paymentFor.name} - ₱${parseFloat(paymentFor.amount).toFixed(2)} (Remaining: ₱${parseFloat(paymentFor.remaining_amount).toFixed(2)})`;
                paymentForSelect.appendChild(option);
            });
            
            // Get fines for the student
            fetch(`/treasurer/student/${studentId}/fines`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add Fines & Violation to payment options if there are fines
                const paymentForSelect = document.getElementById('payment_for');
                if (data.data.total_fines > 0) {
                    const fineOption = document.createElement('option');
                    fineOption.value = 'fines';
                    fineOption.setAttribute('data-fine-amount', data.data.total_fines);
                    fineOption.textContent = `Fines & Violation (₱${data.data.total_fines.toFixed(2)})`;
                    paymentForSelect.appendChild(fineOption);
                    
                    // Add fines breakdown as hidden element
                    const breakdownHtml = data.data.breakdown.map(fine => `
                        <div class="fine-item">
                            <span>Date: ${new Date(fine.date).toLocaleDateString()}</span>
                            <span>Event: ${fine.event}</span>
                            <span>Amount: ₱${fine.amount.toFixed(2)}</span>
                            <div class="fine-details">
                                ${Object.entries(fine.breakdown)
                                    .filter(([key, amount]) => amount > 0)
                                    .map(([key, amount]) => `
                                        <div>${key.toUpperCase()}: ₱${amount.toFixed(2)}</div>
                                    `).join('')}
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('fines-breakdown').innerHTML = breakdownHtml;
                }
            }
        })
        .catch(error => console.error('Error fetching fines:', error));
            
            // Update total progress bar
            const totalProgress = totalAmount > 0 ? (totalPaid / totalAmount) * 100 : 0;
            document.getElementById('totalProgressBar').style.width = `${totalProgress}%`;
            document.getElementById('totalProgressText').textContent = `${totalProgress.toFixed(1)}%`;
            document.getElementById('totalAmountText').textContent = `₱${totalPaid.toFixed(2)} / ₱${totalAmount.toFixed(2)}`;
            
            // Update payment summary table
            const summaryBody = document.getElementById('paymentSummaryBody');
            summaryBody.innerHTML = data.payment_fors.map(paymentFor => {
                const progress = paymentFor.amount > 0 ? (paymentFor.total_paid / paymentFor.amount) * 100 : 0;
                return `
                    <tr>
                        <td>${paymentFor.name}</td>
                        <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                        <td>₱${parseFloat(paymentFor.total_paid).toFixed(2)}</td>
                        <td>₱${parseFloat(paymentFor.remaining_amount).toFixed(2)}</td>
                        <td class="progress-cell">
                            <div class="mini-progress">
                                <div class="mini-progress-fill" style="width: ${progress}%"></div>
                            </div>
                            <span style="font-size: 12px;">${progress.toFixed(1)}%</span>
                        </td>
                    </tr>
                `;
            }).join('');
            
            // Display payment history
            const historyTable = document.getElementById('paymentHistoryTable');
            historyTable.innerHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>OR Number</th>
                            <th>Amount</th>
                            <th>Payment For</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.payments.map(payment => `
                            <tr>
                                <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                                <td>${payment.or_number || 'N/A'}</td>
                                <td>₱${parseFloat(payment.amount).toFixed(2)}</td>
                                <td>${payment.payment_for_name || 'N/A'}</td>
                                <td>${payment.status}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching student details');
        });
}

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
            if (event.target == newPaymentModal) {
                newPaymentModal.style.display = "none";
            }
            if (event.target == paymentForDetailsModal) {
                paymentForDetailsModal.style.display = "none";
            }
        }

        paymentForm.onsubmit = function(e) {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('amount').value);
            
            if (amount <= 0) {
                alert('Payment amount must be greater than zero');
                return;
            }

            const formData = new FormData(paymentForm);
            formData.append('student_id', currentStudentId);
            formData.append('payment_date', new Date().toISOString());
            
            // Get the selected payment_for_id from the dropdown
            const paymentForSelect = document.getElementById('payment_for');
            formData.append('payment_for_id', paymentForSelect.value);

            fetch('/treasurer/payments', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    viewPaymentDetails(currentStudentId); // Refresh the modal
                    updateMainPaymentSummary(); // Refresh the main summary
                    paymentForm.reset();
                } else {
                    alert(data.message || 'Error adding payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Error adding payment');
            });
        }

        // New Payment Modal functionality
        const newPaymentModal = document.getElementById('newPaymentModal'); 
        const newPaymentForm = document.getElementById('newPaymentForm');
        function showNewPaymentModal() {
            newPaymentModal.style.display = "block";
        }
        function closeNewPaymentModal() {
            newPaymentModal.style.display = "none";
        }
        newPaymentForm.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(newPaymentForm);

            fetch('/treasurer/payment-fors', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Payment For created successfully');
                    newPaymentForm.reset();
                    // Refresh the payment for table
                    fetchPaymentFors();
                } else {
                    alert(data.message || 'Error creating Payment For');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message && error.message.includes('already exists')) {
                    alert('A payment with this name already exists. Please use a different name.');
                } else {
                    alert(error.message || 'Error creating Payment For');
                }
            });
        }   
        function fetchPaymentFors() {
            fetch('/treasurer/payment-fors')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('paymentForTableBody');
                    tbody.innerHTML = data.map(paymentFor => `
                        <tr>
                            <td>${paymentFor.name}</td>
                            <td>₱${parseFloat(paymentFor.amount).toFixed(2)}</td>
                            <td>${paymentFor.description || 'No description'}</td>
                        </tr>
                    `).join('');
                })
                .catch(error => console.error('Error:', error));
        }
        // Payment For Details Modal functionality
        const paymentForDetailsModal = document.getElementById('paymentForDetailsModal');
        let currentPaymentForId = null;
        function showPaymentForDetails(id) {
            currentPaymentForId = id;
            paymentForDetailsModal.style.display = "block";
            
            // Fetch payment for details
            fetch(`/treasurer/payment-fors/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Update view mode
                    document.getElementById('view_payment_name').textContent = data.name;
                    document.getElementById('view_payment_amount').textContent = `₱${parseFloat(data.amount).toFixed(2)}`;
                    document.getElementById('view_description').textContent = data.description || 'No description';
                    document.getElementById('view_status').textContent = data.is_active ? 'Active' : 'Inactive';
                    document.getElementById('view_created_at').textContent = new Date(data.created_at).toLocaleString();
                    document.getElementById('view_updated_at').textContent = new Date(data.updated_at).toLocaleString();
                    
                    // Update edit form
                    document.getElementById('edit_payment_name').value = data.name;
                    document.getElementById('edit_payment_amount').value = data.amount;
                    document.getElementById('edit_description').value = data.description || '';
                    
                    // Show view mode
                    document.getElementById('paymentForDetailsView').style.display = 'block';
                    document.getElementById('editPaymentForForm').style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching payment for details');
                });
        }   


    </script>
</body>
</html> 

       