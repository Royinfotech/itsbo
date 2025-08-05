<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Archive</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        
        .with-sidebar {
            margin-left: 90px;
            padding: 40px 30px;
        }
        
        @media (max-width: 768px) {
            .with-sidebar {
                padding: 20px 10px;
            }
        }
        
        h2 {
            color: maroon;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .container-fluid {
            max-width: 1200px;
            margin: auto;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: maroon;
            box-shadow: 0 0 0 2px rgba(128, 0, 0, 0.1);
        }
        
        .btn {
            background-color: maroon;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .btn:hover {
            background-color: #800000;
        }
        
        .alert {
            padding: 15px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-body p {
            margin: 8px 0;
            color: #555;
        }
        
        .card-body p strong {
            color: #333;
            min-width: 120px;
            display: inline-block;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table td {
            color: #555;
        }
        
        @media (max-width: 768px) {
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 8px 6px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .card-header {
                padding: 12px 15px;
            }
        }
        
        .search-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        
        .no-records {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-present {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-absent {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-late {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid maroon;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .student-results {
            display: none;
        }
    </style>
</head>
<body>
<!-- Only show the form and results if a search is performed -->
<div class="with-sidebar">
    <div class="container-fluid">
        <h2>Student Archive</h2>
        <div class="search-form mb-4">
            <form method="GET" action="{{ route('superadmin.archieve') }}">
                <div class="form-group">
                    <label for="student_id">Search Student ID:</label>
                    <input type="text" name="student_id" id="student_id" class="form-control"
                           placeholder="Enter student ID..." value="{{ request('student_id') }}" required>
                </div>
                <button type="submit" class="btn">Search Student</button>
            </form>
        </div>

        @if(request('student_id'))
            @if(!$student)
                <div class="alert">
                    <strong>Student not found.</strong> Please check the student ID and try again.
                </div>
            @else
                <div class="student-results" style="display:block;">
                    <!-- Student Info -->
                    <div class="card mb-4">
                        <div class="card-header"><strong>Student Information</strong></div>
                        <div class="card-body" id="studentInfo">
                            <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                            <p><strong>Name:</strong> {{ $student->student_name }}</p>
                            <p><strong>Email:</strong> {{ $student->email }}</p>
                            <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
                        </div>
                    </div>
                    <!-- Attendance -->
                    <div class="card mb-4">
                        <div class="card-header"><strong>Attendance Records</strong></div>
                        <div class="card-body" id="attendanceRecords">
                            @if($attendances->isEmpty())
                                <div class="no-records">No attendance records found.</div>
                            @else
                                <div style="overflow-x: auto;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>School Year</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendances as $attendance)
                                                <tr>
                                                    <td>{{ $attendance->school_year }}</td>
                                                    <td>{{ $attendance->date }}</td>
                                                    <td>
                                                        <span class="status-badge status-{{ strtolower($attendance->status) }}">
                                                            {{ ucfirst($attendance->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Payments -->
                    <div class="card mb-4">
                        <div class="card-header"><strong>Payment Records</strong></div>
                        <div class="card-body" id="paymentRecords">
                            @if($payments->isEmpty())
                                <div class="no-records">No payment records found.</div>
                            @else
                                <div style="overflow-x: auto;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>School Year</th>
                                                <th>Semester</th>
                                                <th>Payment For</th>
                                                <th>Amount</th>
                                                <th>Date Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->school_year }}</td>
                                                    <td>{{ $payment->semester }}</td>
                                                    <td>{{ $payment->payment_for }}</td>
                                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                                    <td>{{ $payment->date_paid }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>0.                                                                                                                            
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
</body>
</html>