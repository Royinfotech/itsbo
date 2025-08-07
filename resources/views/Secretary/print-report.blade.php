{{-- resources/views/attendance/print-report.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ $event->event_name }}</title>
     <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
            background: #fff;
            color: #333;
        }

        .container {
            padding: 30px;
            max-width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #222;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 20px;
            font-weight: 500;
            color: #555;
        }

        .event-details {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #444;
        }

        .event-details div {
            flex: 1;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 11px;
        }

        .attendance-table th,
        .attendance-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .attendance-table th {
            background-color: #f7f7f7;
            color: #222;
            font-weight: 600;
        }

        .attendance-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .status-present {
            color: #2e7d32;
            font-weight: bold;
        }

        .status-absent {
            color: #c62828;
            font-weight: bold;
        }

        .status-partial {
            color: #ef6c00;
            font-weight: bold;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 40px;
        }

        .summary-box {
            flex: 1;
            border: 1.5px solid #444;
            padding: 15px 20px;
            border-radius: 8px;
            background-color: #fdfdfd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .summary-title {
            font-size: 15px;
            font-weight: bold;
            border-bottom: 1px solid #999;
            padding-bottom: 6px;
            margin-bottom: 12px;
            text-align: center;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 11px;
        }

        .summary-item strong {
            font-weight: 600;
        }

        .year-breakdown {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .year-item {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f3f3f3;
            font-size: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 50px;
            border-top: 1px solid #ccc;
            padding-top: 12px;
        }

        /* Print styles */
        @media print {
            body {
                font-size: 10px;
            }

            .container {
                padding: 10px;
            }

            .attendance-table th,
            .attendance-table td {
                padding: 5px;
                font-size: 9px;
            }

            .summary {
                flex-direction: column;
                gap: 10px;
            }

            .summary-box {
                page-break-inside: avoid;
            }

            .footer {
                font-size: 9px;
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
                    <th style="width: 13%;">Student ID</th>
                    <th style="width: 10%;">Year</th>
                    
                    @php
                        $timeDuration = $event->time_duration ?? 'Whole Day';
                        $showAM = in_array($timeDuration, ['Whole Day', 'Half Day: Morning']);
                        $showPM = in_array($timeDuration, ['Whole Day', 'Half Day: Afternoon']);
                    @endphp
                    
                    @if($showAM)
                        <th style="width: 10%;">AM In</th>
                        <th style="width: 10%;">AM Out</th>
                    @endif
                    
                    @if($showPM)
                        <th style="width: 10%;">PM In</th>
                        <th style="width: 10%;">PM Out</th>
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
                        <td style="text-align: center;">{{ $student->student_id ?? 'N/A' }}</td>
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
                        <td colspan="{{ 4 + ($showAM ? 2 : 0) + ($showPM ? 2 : 0) }}" style="text-align: center; font-style: italic; color: #666;">
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
                    // Use ONLY the count from the filtered data
                    $totalStudents = $attendanceData->count();
                    $totalPresent = 0;
                    $totalPartial = 0;
                    
                    // Count students who actually have attendance records
                    foreach ($attendanceData as $student) {
                        $hasAMIn = !empty($student->am_in);
                        $hasAMOut = !empty($student->am_out);
                        $hasPMIn = !empty($student->pm_in);
                        $hasPMOut = !empty($student->pm_out);
                        
                        $isPresent = false;
                        $isPartial = false;
                        
                        if ($timeDuration === 'Whole Day') {
                            $hasAMAttendance = ($hasAMIn || $hasAMOut);
                            $hasPMAttendance = ($hasPMIn || $hasPMOut);
                            $isPresent = $hasAMAttendance && $hasPMAttendance;
                            $isPartial = ($hasAMAttendance || $hasPMAttendance) && !$isPresent;
                        } elseif ($timeDuration === 'Half Day: Morning') {
                            $isPresent = ($hasAMIn || $hasAMOut);
                        } elseif ($timeDuration === 'Half Day: Afternoon') {
                            $isPresent = ($hasPMIn || $hasPMOut);
                        }
                        
                        if ($isPresent) {
                            $totalPresent++;
                        } elseif ($isPartial) {
                            $totalPartial++;
                        }
                    }
                    
                    $totalAbsent = $totalStudents - $totalPresent - $totalPartial;
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
                
                @if($timeDuration === 'Whole Day' && $totalPartial > 0)
                <div class="summary-item">
                    <span>Partial Attendance:</span>
                    <strong style="color: #ff8800;">{{ $totalPartial }}</strong>
                </div>
                @endif
                
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
                        $partial = 0;
                        $total = $students->count();
                        
                        foreach ($students as $student) {
                            $hasAMIn = !empty($student->am_in);
                            $hasAMOut = !empty($student->am_out);
                            $hasPMIn = !empty($student->pm_in);
                            $hasPMOut = !empty($student->pm_out);
                            
                            if ($timeDuration === 'Whole Day') {
                                $hasAMAttendance = ($hasAMIn || $hasAMOut);
                                $hasPMAttendance = ($hasPMIn || $hasPMOut);
                                if ($hasAMAttendance && $hasPMAttendance) {
                                    $present++;
                                } elseif ($hasAMAttendance || $hasPMAttendance) {
                                    $partial++;
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
                            'partial' => $partial,
                            'absent' => $total - $present - $partial
                        ];
                    })->sortKeys();
                @endphp
                
                @forelse($yearBreakdown as $year => $data)
                    <div class="summary-item">
                        <span>Year {{ $year }}:</span>
                        <span>
                            <strong>{{ $data['present'] }}/{{ $data['total'] }}</strong>
                            ({{ $data['total'] > 0 ? round(($data['present'] / $data['total']) * 100, 1) : 0 }}%)
                            @if($data['partial'] > 0)
                                <small style="color: #ff8800;">+{{ $data['partial'] }} partial</small>
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="summary-item">
                        <span style="font-style: italic; color: #666;">No active students found</span>
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