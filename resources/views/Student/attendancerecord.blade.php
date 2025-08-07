<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Record</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .attendance-container {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 0 auto;
            max-width: 1100px;
        }

        .attendance-container h2 {
            color: #800000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 22px;
            flex-wrap: wrap;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            margin-bottom: 15px;
            position: relative;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #800000;
            border-radius: 3px;
        }

        .attendance-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
        }

        .attendance-table th {
            background-color: #800000;
            color: white;
            font-weight: 500;
        }

        .attendance-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            text-align: center;
            min-width: 60px;
        }

        .status-present {
            background-color: #d4edda;
            color: #155724;
        }

        .status-absent {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }

        .time-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            display: inline-block;
        }

        .duration-badge {
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            display: inline-block;
            margin-left: 5px;
        }

        .duration-morning {
            background-color: #ffc107;
            color: #212529;
        }

        .duration-afternoon {
            background-color: #fd7e14;
            color: white;
        }

        .duration-whole {
            background-color: #28a745;
            color: white;
        }

        .session-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .time-display {
            font-size: 11px;
            color: #6c757d;
        }

        .not-applicable {
            color: #6c757d;
            font-style: italic;
            font-size: 12px;
        }

        .no-records {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }

        .no-records i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .overall-status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
        }

        /* Enhanced mobile styles */
        @media (max-width: 900px) {
            .attendance-container {
                padding: 10px;
                margin: 10px;
                overflow-x: auto;
            }

            .attendance-table {
                margin: 10px 0;
                min-width: 800px;
            }

            .status-badge {
                padding: 3px 6px;
                font-size: 10px;
                min-width: 50px;
            }

            .table-responsive::after {
                content: '← Scroll →';
                position: absolute;
                bottom: -20px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 12px;
                color: #666;
                white-space: nowrap;
                animation: fadeInOut 2s infinite;
            }
        }

        @media (max-width: 480px) {
            .attendance-container {
                padding: 8px;
                margin: 5px;
            }

            .attendance-table {
                font-size: 11px;
            }

            .attendance-table th,
            .attendance-table td {
                padding: 6px;
            }

            .status-badge {
                padding: 2px 4px;
                font-size: 9px;
                min-width: 40px;
            }
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="attendance-container">
        <h2><i class="fas fa-clipboard-check"></i> My Attendance Record</h2>

        @if($attendances->isEmpty())
            <div class="no-records">
                <i class="fas fa-info-circle"></i>
                <p>No attendance records found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Duration</th>
                            <th>Morning Session</th>
                            <th>Afternoon Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            @php
                                // Handle database format conversion
                                $dbDuration = $attendance->event->time_duration ?? 'Whole Day';
                                $eventDuration = match($dbDuration) {
                                    'Half Day: Morning' => 'morning',
                                    'Half Day: Afternoon' => 'afternoon', 
                                    'Whole Day' => 'whole_day',
                                    default => 'whole_day'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $attendance->event->event_name }}</strong>
                                </td>
                                <td>{{ Carbon\Carbon::parse($attendance->event->event_date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="duration-badge duration-{{ $eventDuration == 'morning' ? 'morning' : ($eventDuration == 'afternoon' ? 'afternoon' : 'whole') }}">
                                        @if($eventDuration == 'morning')
                                            <i class="fas fa-sun"></i> Half Day: Morning
                                        @elseif($eventDuration == 'afternoon')
                                            <i class="fas fa-cloud-sun"></i> Half Day: Afternoon
                                        @else
                                            <i class="fas fa-clock"></i> Whole Day
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($eventDuration == 'afternoon')
                                        <span class="not-applicable">N/A</span>
                                    @else
                                        <div class="session-info">
                                            <div>
                                                @if($attendance->am_in)
                                                    <span class="status-badge status-present">
                                                        <i class="fas fa-sign-in-alt"></i> In
                                                    </span>
                                                @else
                                                    <span class="status-badge status-absent">
                                                        <i class="fas fa-times"></i> Absent
                                                    </span>
                                                @endif
                                                
                                                @if($attendance->am_out && $attendance->am_in)
                                                    <span class="status-badge status-present">
                                                        <i class="fas fa-sign-out-alt"></i> Out
                                                    </span>
                                                @elseif($attendance->am_in)
                                                    <span class="status-badge status-partial">
                                                        <i class="fas fa-clock"></i> No Out
                                                    </span>
                                                @endif
                                            </div>
                                            @if($attendance->am_in_time || $attendance->am_out_time)
                                                <div class="time-display">
                                                    @if($attendance->am_in_time)
                                                        In: <span class="time-badge">{{ $attendance->getFormattedTime($attendance->am_in_time) }}</span>
                                                    @endif
                                                    @if($attendance->am_out_time)
                                                        Out: <span class="time-badge">{{ $attendance->getFormattedTime($attendance->am_out_time) }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($eventDuration == 'morning')
                                        <span class="not-applicable">N/A</span>
                                    @else
                                        <div class="session-info">
                                            <div>
                                                @if($attendance->pm_in)
                                                    <span class="status-badge status-present">
                                                        <i class="fas fa-sign-in-alt"></i> In
                                                    </span>
                                                @else
                                                    <span class="status-badge status-absent">
                                                        <i class="fas fa-times"></i> Absent
                                                    </span>
                                                @endif
                                                
                                                @if($attendance->pm_out && $attendance->pm_in)
                                                    <span class="status-badge status-present">
                                                        <i class="fas fa-sign-out-alt"></i> Out
                                                    </span>
                                                @elseif($attendance->pm_in)
                                                    <span class="status-badge status-partial">
                                                        <i class="fas fa-clock"></i> No Out
                                                    </span>
                                                @endif
                                            </div>
                                            @if($attendance->pm_in_time || $attendance->pm_out_time)
                                                <div class="time-display">
                                                    @if($attendance->pm_in_time)
                                                        In: <span class="time-badge">{{ $attendance->getFormattedTime($attendance->pm_in_time) }}</span>
                                                    @endif
                                                    @if($attendance->pm_out_time)
                                                        Out: <span class="time-badge">{{ $attendance->getFormattedTime($attendance->pm_out_time) }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="{{ asset('assets/js/Student.js') }}"></script>
</body>
</html>