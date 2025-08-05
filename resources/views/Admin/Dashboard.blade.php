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
                    <div class="chart-container">
                        <canvas id="studentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Files Count Section -->
            <div class="dashboard-card">
                <h3>Uploaded Files</h3>
                <div class="stats-container">
                    <div class="counter-value">
                        <span id="fileCount">{{ $totalFiles ?? 0 }}</span>
                        <span class="counter-label">Total Files</span>
                    </div>
                    <div class="chart-container">
                        <canvas id="fileChart"></canvas>
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
@endsection

@push('scripts')
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sample data - you'll need to replace this with actual data from your backend
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const studentData = @json($studentData);
    const fileData = @json($fileData);

    // Create the students chart
    const studentCtx = document.getElementById('studentChart').getContext('2d');
    const studentChart = new Chart(studentCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Students',
                data: Object.values(studentData),
                backgroundColor: 'rgba(128, 0, 0, 0.1)',
                borderColor: '#800000',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#800000',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Monthly New Students',
                    color: '#800000',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Create the files chart
    const fileCtx = document.getElementById('fileChart').getContext('2d');
    const fileChart = new Chart(fileCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Files',
                data: Object.values(fileData),
                backgroundColor: 'rgba(128, 0, 0, 0.1)',
                borderColor: '#800000',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#800000',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Monthly New Files',
                    color: '#800000',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Function to update counts and charts
    function updateCounts() {
        // Update student count
        fetch('/get-student-count')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const count = data.count;
                    document.getElementById('studentCount').textContent = count;
                    
                    // Update the last bar in the chart
                    studentChart.data.datasets[0].data[11] = count;
                    studentChart.update();
                    
                    // Add highlight effect
                    document.getElementById('studentCount').parentElement.classList.add('highlight');
                    setTimeout(() => {
                        document.getElementById('studentCount').parentElement.classList.remove('highlight');
                    }, 500);
                }
            })
            .catch(error => console.error('Error fetching student count:', error));

        // Update file count
        fetch('/get-file-count')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const count = data.count;
                    document.getElementById('fileCount').textContent = count;
                    
                    // Update the last bar in the chart
                    fileChart.data.datasets[0].data[11] = count;
                    fileChart.update();
                    
                    // Add highlight effect
                    document.getElementById('fileCount').parentElement.classList.add('highlight');
                    setTimeout(() => {
                        document.getElementById('fileCount').parentElement.classList.remove('highlight');
                    }, 500);
                }
            })
            .catch(error => console.error('Error fetching file count:', error));
    }

    // Update counts every 30 seconds
    setInterval(updateCounts, 30000);

    // Function to update events table
    function updateEvents() {
        fetch('/get-events')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.getElementById('eventTableBody');
                    tbody.innerHTML = '';
                    
                    data.events.forEach(event => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${event.event_title}</td>
                            <td>${event.event_location}</td>
                            <td>${new Date(event.event_date).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric' 
                            })}</td>
                            <td>${event.event_duration}</td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error fetching events:', error));
    }

    // Update events every 30 seconds
    setInterval(updateEvents, 30000);
});
</script>
@endpush
