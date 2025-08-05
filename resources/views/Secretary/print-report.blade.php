{{-- resources/views/attendance/print-report.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ $event->event_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .event-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 11px;
        }

        .event-details div {
            flex: 1;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10px;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }

        .attendance-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .attendance-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status-present {
            color: #008000;
            font-weight: bold;
        }

        .status-absent {
            color: #cc0000;
            font-weight: bold;
        }

        .na-cell {
            background-color: #e8e8e8;
            color: #666;
            font-style: italic;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            width: 48%;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .year-summary {
            margin-top: 20px;
        }

        .year-breakdown {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .year-item {
            border: 1px solid #ccc;
            padding: 8px;
            min-width: 100px;
            text-align: center;
            font-size: 10px;
        }

        /* Print-specific styles */
        @media print {
            body {
                font-size: 10px;
            }
            
            .container {
                padding: 10px;
            }
            
            .attendance-table {
                font-size: 8px;
            }
            
            .attendance-table th,
            .attendance-table td {
                padding: 3px 2px;
            }
            
            /* Ensure table doesn't break across pages */
            .attendance-table {
                page-break-inside: avoid;
            }
            
            /* Keep summary together */
            .summary {
                page-break-inside: avoid;
            }
        }

        @page {
            margin: 0.5in;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1>ATTENDANCE REPORT</h1>
            <h2>{{ $event->event_name }}</h2>
            
            <div class="event-details">
                <div>
                    <strong>Event Date:</strong> 
                    {{ \Carbon\Carbon::parse($event->event_date)->format('F j, Y') }}
                </div>
                <div>
                    <strong>Duration:</strong> 
                    {{ $event->time_duration ?? 'Whole Day' }}
                </div>
                <div>
                    <strong>Generated:</strong> 
                    {{ now()->format('F j, Y - g:i A') }}
                </div>
            </div>
        </div>

        <!-- Main Attendance Table -->
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">Student Name</th>
                    <th style="width: 15%;">Student ID</th>
                    <th style="width: 10%;">Year</th>
                    
                    @php
                        $timeDuration = $event->time_duration ?? 'Whole Day';
                        $showAM = in_array($timeDuration, ['Whole Day', 'Half Day: Morning']);
                        $showPM = in_array($timeDuration, ['Whole Day', 'Half Day: Afternoon']);
                    @endphp
                    
                    @if($showAM)
                        <th style="width: 15%;">AM In</th>
                        <th style="width: 15%;">AM Out</th>
                    @endif
                    
                    @if($showPM)
                        <th style="width: 15%;">PM In</th>
                        <th style="width: 15%;">PM Out</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                    // Sort attendance data by year level
                    $sortedAttendanceData = $attendanceData->sortBy('year_level');
                @endphp
                
                @forelse($sortedAttendanceData as $index => $student)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $student->student_name ?? 'N/A' }}</td>
                        <td>{{ $student->student_id ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ $student->year_level ?? 'N/A' }}</td>
                        
                        @if($showAM)
                            <td style="text-align: center;">{{ $student->am_in ?? '-' }}</td>
                            <td style="text-align: center;">{{ $student->am_out ?? '-' }}</td>
                        @endif
                        
                        @if($showPM)
                            <td style="text-align: center;">{{ $student->pm_in ?? '-' }}</td>
                            <td style="text-align: center;">{{ $student->pm_out ?? '-' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showAM && $showPM ? '8' : '6' }}" style="text-align: center; font-style: italic; color: #666;">
                            No active students found for this event
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary">
            <!-- Overall Summary -->
            <div class="summary-box">
                <div class="summary-title">ATTENDANCE SUMMARY</div>
                
                @php
                    // Use the totalActiveStudents passed from controller
                    $totalStudents = $totalActiveStudents ?? $attendanceData->count();
                    $totalPresent = 0;
                    
                    // Count students who actually have attendance records
                    foreach ($attendanceData as $student) {
                        $hasAMIn = !empty($student->am_in);
                        $hasAMOut = !empty($student->am_out);
                        $hasPMIn = !empty($student->pm_in);
                        $hasPMOut = !empty($student->pm_out);
                        
                        $isPresent = false;
                        
                        if ($timeDuration === 'Whole Day') {
                            // For whole day events, student must have attendance in BOTH AM and PM to be present
                            $isPresent = (($hasAMIn || $hasAMOut) && ($hasPMIn || $hasPMOut));
                        } elseif ($timeDuration === 'Half Day: Morning') {
                            $isPresent = ($hasAMIn || $hasAMOut);
                        } elseif ($timeDuration === 'Half Day: Afternoon') {
                            $isPresent = ($hasPMIn || $hasPMOut);
                        }
                        
                        if ($isPresent) {
                            $totalPresent++;
                        }
                    }
                    
                    $totalAbsent = $totalStudents - $totalPresent;
                    $attendanceRate = $totalStudents > 0 ? round(($totalPresent / $totalStudents) * 100, 1) : 0;
                @endphp
                
                <div class="summary-item">
                    <span>Total Active Students:</span>
                    <strong>{{ $totalStudents }}</strong>
                </div>
                <div class="summary-item">
                    <span>Present:</span>
                    <strong style="color: #008000;">{{ $totalPresent }}</strong>
                </div>
                
                <div class="summary-item">
                    <span>Absent:</span>
                    <strong style="color: #cc0000;">{{ $totalAbsent }}</strong>
                </div>
                <div class="summary-item" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 10px;">
                    <span>Attendance Rate:</span>
                    <strong>{{ $attendanceRate }}%</strong>
                </div>
                
                <div class="summary-item" style="margin-top: 10px; font-size: 10px; color: #666;">
                    <span>Event Type:</span>
                    <strong>
                        @if($timeDuration === 'Half Day: Morning')
                            Half Day (Morning)
                        @elseif($timeDuration === 'Half Day: Afternoon')
                            Half Day (Afternoon)
                        @else
                            Whole Day
                        @endif
                    </strong>
                </div>
            </div>

            <!-- Year Level Breakdown -->
            <div class="summary-box">
                <div class="summary-title">BY YEAR LEVEL</div>
                @php
                    $yearBreakdown = $attendanceData->groupBy('year_level')->map(function($students, $year) use ($timeDuration) {
                        $present = 0;
                        $total = $students->count();
                        
                        foreach ($students as $student) {
                            $hasAMIn = !empty($student->am_in);
                            $hasAMOut = !empty($student->am_out);
                            $hasPMIn = !empty($student->pm_in);
                            $hasPMOut = !empty($student->pm_out);
                            
                            if ($timeDuration === 'Whole Day') {
                                if (($hasAMIn || $hasAMOut) && ($hasPMIn || $hasPMOut)) {
                                    $present++;
                                }
                            } elseif ($timeDuration === 'Half Day: Morning') {
                                if ($hasAMIn || $hasAMOut) {
                                    $present++;
                                }
                            } elseif ($timeDuration === 'Half Day: Afternoon') {
                                if ($hasPMIn || $hasPMOut) {
                                    $present++;
                                }
                            }
                        }
                        
                        return [
                            'total' => $total,
                            'present' => $present,
                            'absent' => $total - $present
                        ];
                    })->sortKeys();
                @endphp
                
                @forelse($yearBreakdown as $year => $data)
                    <div class="summary-item">
                        <span>Year {{ $year }}:</span>
                        <span>
                            <strong>{{ $data['present'] }}/{{ $data['total'] }}</strong>
                            ({{ $data['total'] > 0 ? round(($data['present'] / $data['total']) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                @empty
                    <div class="summary-item">
                        <span colspan="2" style="font-style: italic; color: #666;">No active students found</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ccc; padding-top: 10px;">
            <p>This report was automatically generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <p>Event ID: {{ $event->id }} | Total Active Students: {{ $totalStudents }} | Duration: {{ $timeDuration }}</p>
            <p><em>Note: Only students with "active" status are included in this report</em></p>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        window.onload = function() {
            // Small delay to ensure styles are loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Print button functionality (if you want to add a print button)
        function printReport() {
            window.print();
        }

        // Close window after printing (optional)
        window.onafterprint = function() {
            // Uncomment if you want to auto-close after printing
            // window.close();
        };
    </script>
</body>
</html>