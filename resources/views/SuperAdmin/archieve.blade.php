<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Archive System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
            padding: 1.5rem;
        }
        .container { max-width: 64rem; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 {
            font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #800000, #a00000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .header p { color: #6b7280; }
        .search-section {
            background: white; border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem; margin-bottom: 1.5rem;
        }
        .search-container {
            display: flex; flex-direction: column; gap: 1rem; align-items: center;
        }
        @media (min-width: 640px) {
            .search-container { flex-direction: row; }
        }
        .input-container { flex: 1; position: relative; width: 100%; }
        .search-icon {
            position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);
            color: #9ca3af; width: 1.25rem; height: 1.25rem;
        }
        .search-input {
            width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #d1d5db; border-radius: 0.5rem;
            font-size: 1.125rem; outline: none; transition: all 0.2s;
        }
        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .search-button {
            padding: 0.75rem 1.5rem; background: #3b82f6; color: white;
            border: none; border-radius: 0.5rem; font-weight: 500; cursor: pointer;
            transition: background-color 0.2s; min-width: 7.5rem;
        }
        .search-button:hover { background: #2563eb; }
        .error-message {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem;
            padding: 1rem; margin-bottom: 1.5rem; color: #dc2626;
        }
        .student-card {
            background: white; border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden; margin-bottom: 2rem;
        }
        .student-header {
            background: linear-gradient(135deg, #800000, #a00000); color: white;
            padding: 1.5rem; display: flex; align-items: center; gap: 1rem;
        }
        .avatar {
            width: 4rem; height: 4rem; background: rgba(255,255,255,0.2);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem;
        }
        .student-name { font-size: 1.5rem; font-weight: bold; }
        .student-id { color: #bfdbfe; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-graduated { background: #dbeafe; color: #1e40af; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-badge {
            padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem;
            font-weight: 500; margin-left: auto;
        }
        .student-content { padding: 1.5rem; }
        .grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
        @media (min-width: 1024px) {
            .grid { grid-template-columns: repeat(3, 1fr); }
        }
        .section { display: flex; flex-direction: column; gap: 1rem; }
        .section-title { font-size: 1.125rem; font-weight: 600; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; }
        .info-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.5rem 0; }
        .info-icon { width: 1.25rem; height: 1.25rem; color: #6b7280; margin-top: 0.25rem; }
        .info-content { flex: 1; }
        .info-label { font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem; }
        .info-value { font-weight: 500; color: #1f2937; }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .stats-item { display: flex; flex-direction: column; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; }
        .stats-value { font-weight: 500; font-size: 1.125rem; color: #1f2937; }
        .payment-section, .attendance-section {
            background: #f9fafb; border-radius: 0.5rem; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem;
        }
        .section-header { display: flex; align-items: center; gap: 0.5rem; font-weight: 500; color: #1f2937; }
        .payment-info, .attendance-info { display: flex; flex-direction: column; gap: 0.5rem; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.25rem 0; }
        .payment-paid { background: #dcfce7; color: #166534; }
        .payment-completed { background: #dbeafe; color: #1e40af; }
        .payment-pending { background: #fef3c7; color: #d97706; }
        .payment-overdue { background: #fee2e2; color: #dc2626; }
        .payment-status { padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .payment-history { margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #e5e7eb; }
        .payment-history h4 { font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
        .payment-list { max-height: 8rem; overflow-y: auto; display: flex; flex-direction: column; gap: 0.25rem; }
        .payment-item { display: flex; justify-content: space-between; align-items: center; padding: 0.25rem 0; font-size: 0.875rem; }
        .payment-date-type { display: flex; flex-direction: column; }
        .payment-date { color: #6b7280; }
        .payment-type { font-size: 0.75rem; color: #9ca3af; }
        .payment-amount-status { display: flex; align-items: center; gap: 0.5rem; }
        .payment-amount { font-weight: 500; }
        .history-status-paid { background: #dcfce7; color: #166534; }
        .history-status-late { background: #fef3c7; color: #d97706; }
        .history-status-unpaid { background: #fee2e2; color: #dc2626; }
        .history-status { padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .attendance-rate-excellent { color: #16a34a; }
        .attendance-rate-good { color: #d97706; }
        .attendance-rate-poor { color: #dc2626; }
        .attendance-present { color: #16a34a; }
        .attendance-absent { color: #dc2626; }
        .no-student {
            background: white; border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem; text-align: center;
        }
        .no-student-icon { width: 3rem; height: 3rem; color: #9ca3af; margin: 0 auto 1rem; font-size: 3rem; }
        .no-student h3 { font-size: 1.125rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
        .no-student p { color: #6b7280; }
        .hidden { display: none; }
        .icon { display: inline-block; width: 1em; height: 1em; stroke-width: 0; stroke: currentColor; fill: currentColor; }
        .print-btn {
            margin: 1rem 0 2rem 0;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .print-btn:hover { background: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Student Archive</h1>
            <p>Search and view student information by ID</p>
        </div>


        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('superadmin.archieve') }}" class="search-container">
                <div class="input-container">
                    <div class="search-icon">🔍</div>
                    <input
                        type="text"
                        name="student_id"
                        id="searchInput"
                        placeholder="Enter Student ID (e.g., STU001)"
                        class="search-input"
                        value="{{ request('student_id') }}"
                        required
                    />
                </div>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>


        @if(request('student_id'))
            @if(!$student)
                <div class="error-message">
                    <p>Student not found. Please check the student ID and try again.</p>
                </div>
            @else
                <button class="print-btn" onclick="printStudentCard()">Print Student Archive</button>
                <div id="studentCard" class="student-card">
                    <div class="student-header">
                        <div class="avatar">👤</div>
                        <div>
                            <h2 class="student-name">{{ $student->student_name ?? ($student->first_name ?? '') . ' ' . ($student->last_name ?? '') }}</h2>
                            <p class="student-id">Student ID: {{ $student->student_id }}</p>
                        </div>
                        <span class="status-badge status-{{ strtolower($student->status ?? 'active') }}">
                            {{ ucfirst($student->status ?? 'Active') }}
                        </span>
                    </div>
                    <div class="student-content">
                        <div class="grid">
                            <!-- Personal Information -->
                            <div class="section">
                                <h3 class="section-title">Personal Information</h3>
                                <div class="info-item">
                                    <div class="info-icon">📧</div>
                                    <div class="info-content">
                                        <p class="info-label">Email</p>
                                        <p class="info-value">{{ $student->email }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">📞</div>
                                    <div class="info-content">
                                        <p class="info-label">Phone</p>
                                        <p class="info-value">{{ $student->phone ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">📍</div>
                                    <div class="info-content">
                                        <p class="info-label">Address</p>
                                        <p class="info-value">{{ $student->address ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">📅</div>
                                    <div class="info-content">
                                        <p class="info-label">Date of Birth</p>
                                        <p class="info-value">{{ $student->date_of_birth ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Academic Information -->
                            <div class="section">
                                <h3 class="section-title">Academic Information</h3>
                                <div class="info-item">
                                    <div class="info-icon">📚</div>
                                    <div class="info-content">
                                        <p class="info-label">Major</p>
                                        <p class="info-value">{{ $student->major ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">🎓</div>
                                    <div class="info-content">
                                        <p class="info-label">Academic Year</p>
                                        <p class="info-value">{{ $student->year_level ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">📅</div>
                                    <div class="info-content">
                                        <p class="info-label">School Year</p>
                                        <p class="info-value">{{ $student->school_year ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">📅</div>
                                    <div class="info-content">
                                        <p class="info-label">Enrollment Date</p>
                                        <p class="info-value">{{ $student->enrollment_date ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="stats-grid">
                                    <div class="stats-item">
                                        <p class="info-label">GPA</p>
                                        <p class="stats-value">{{ $student->gpa ?? '-' }}</p>
                                    </div>
                                    <div class="stats-item">
                                        <p class="info-label">Credits</p>
                                        <p class="stats-value">{{ $student->credits ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Payment & Attendance Information -->
                            <div class="section">
                                <h3 class="section-title">Payment & Attendance</h3>
                                <!-- Payment Information -->
                                <div class="payment-section">
                                    <div class="section-header">
                                        <span>💰</span>
                                        <span>Payment Information</span>
                                    </div>
                                    <div class="payment-info">
                                        <div class="info-row">
                                            <span class="info-label">Status:</span>
                                            <span class="payment-status payment-{{ strtolower($payments->first()->status ?? 'pending') }}">
                                                {{ ucfirst($payments->first()->status ?? 'Pending') }}
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Current Amount:</span>
                                            <span class="info-value">
                                                ₱{{ number_format($payments->first()->amount ?? 0, 2) }}
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Due Date:</span>
                                            <span class="info-value">{{ $payments->first()->due_date ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <!-- Payment History -->
                                    <div class="payment-history">
                                        <h4>Payment History</h4>
                                        <div class="payment-list">
                                            @forelse($payments as $payment)
                                                <div class="payment-item">
                                                    <div class="payment-date-type">
                                                        <span class="payment-date">{{ $payment->date_paid ?? '-' }}</span>
                                                        <span class="payment-type">{{ $payment->payment_for ?? '-' }}</span>
                                                    </div>
                                                    <div class="payment-amount-status">
                                                        <span class="payment-amount">₱{{ number_format($payment->amount ?? 0, 2) }}</span>
                                                        <span class="history-status history-status-{{ strtolower($payment->status ?? 'unpaid') }}">{{ ucfirst($payment->status ?? 'Unpaid') }}</span>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="payment-item">No payment records found.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <!-- Attendance Information -->
                                <div class="attendance-section">
                                    <div class="section-header">
                                        <span>✅</span>
                                        <span>Attendance Record</span>
                                    </div>
                                    <div class="attendance-info">
                                        <div class="info-row">
                                            <span class="info-label">Attendance Rate:</span>
                                            <span class="stats-value">
                                                @php
                                                    $present = $attendances->where('status', 'Present')->count();
                                                    $total = $attendances->count();
                                                    $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                                                @endphp
                                                {{ $rate }}%
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Present:</span>
                                            <span class="info-value attendance-present">{{ $present }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Absent:</span>
                                            <span class="info-value attendance-absent">{{ $attendances->where('status', 'Absent')->count() }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Total Days:</span>
                                            <span class="info-value">{{ $total }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div id="noStudent" class="no-student">
                <div class="no-student-icon">👤</div>
                <h3>No Student Selected</h3>
                <p>Enter a student ID in the search bar above to view their information.</p>
            </div>
        @endif
    </div>
    <script>
        function printStudentCard() {
            var printContents = document.getElementById('studentCard').outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
</body>
</html>
