@extends('layouts.secretary')

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-content">
            <!-- Students Count Section -->
            <div class="dashboard-card">
                <h3>IT Students</h3>
                <div class="stats-container">
                    <div class="counter-value">
                        <span id="studentCount">{{ $totalStudents ?? 0 }}</span>
                        <span class="counter-label">Total Students</span>
                    </div>
                    <div class="student-status-grid">
                        <div class="status-item active">
                            <span id="activeStudentCount">{{ $activeStudents ?? 0 }}</span>
                            <span class="status-label">Active</span>
                        </div>
                        <div class="status-item pending">
                            <span id="pendingStudentCount">{{ $pendingStudents ?? 0 }}</span>
                            <span class="status-label">Pending</span>
                        </div>
                        <div class="status-item inactive">
                            <span id="inactiveStudentCount">{{ $inactiveStudents ?? 0 }}</span>
                            <span class="status-label">Inactive</span>
                        </div>
                        <div class="status-item declined">
                            <span id="declinedStudentCount">{{ $declinedStudents ?? 0 }}</span>
                            <span class="status-label">Declined</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar of Activities -->
            <div class="dashboard-card calendar-card">
                <h3>Calendar of Activities</h3>
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Event Location</th>
                            <th>Event Date</th>
                            <th>Event Duration</th>
                        </tr>
                    </thead>
                    <tbody id="eventTableBody">
                        @foreach($events as $event)
                        <tr>
                            <td>{{ $event->event_name }}</td>
                            <td>{{ $event->event_location }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
                            <td>{{ $event->time_duration }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .student-status-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .status-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .status-item.active {
            border-color: #28a745;
            background-color: #d4edda;
        }

        .status-item.pending {
            border-color: #ffc107;
            background-color: #fff3cd;
        }

        .status-item.inactive {
            border-color: #dc3545;
            background-color: #f8d7da;
        }

        .status-item.declined {
            border-color: #6c757d;
            background-color: #e2e3e5;
        }

        .status-item span:first-child {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .status-item.active span:first-child {
            color: #28a745;
        }

        .status-item.pending span:first-child {
            color: #ffc107;
        }

        .status-item.inactive span:first-child {
            color: #dc3545;
        }

        .status-item.declined span:first-child {
            color: #6c757d;
        }

        .status-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .student-status-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
        }

        @media (max-width: 480px) {
            .student-status-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the data from PHP
    const rawStudentData = @json($studentData ?? []);
    // Process student data
    const studentData = new Array(12).fill(0);
    if (typeof rawStudentData === 'object' && rawStudentData !== null) {
        for (let month in rawStudentData) {
            const monthIndex = parseInt(month) - 1;
            if (monthIndex >= 0 && monthIndex < 12) {
                studentData[monthIndex] = parseInt(rawStudentData[month]) || 0;
            }
        }
    }
    // Function to update counts
    function updateCounts() {
        fetch('/get-student-count')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('studentCount').textContent = data.count;
                    document.getElementById('activeStudentCount').textContent = data.active;
                    document.getElementById('pendingStudentCount').textContent = data.pending;
                    document.getElementById('inactiveStudentCount').textContent = data.inactive;
                    document.getElementById('declinedStudentCount').textContent = data.declined;
                }
            })
            .catch(error => console.error('Error fetching student count:', error));

    // Update counts every 30 seconds
    setInterval(updateCounts, 30000);
});
</script>
@endpush