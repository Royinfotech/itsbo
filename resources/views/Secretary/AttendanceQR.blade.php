<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/assets/pictures/LoginBackg.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.3;
            z-index: -1;
        }

        .main-content {
            margin-left: 50px;
            padding: 5px;
            height: 100vh;
            overflow-y: auto;
            position: relative;
        }

        .container {
            background: #ffffff;
            padding: 15px;
            border-radius: 20px;
            max-width: 1100px;
            max-height: 800px;
            box-shadow: 0 40px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            margin-left: 50px;
            margin-top: 40px;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            flex-shrink: 0;
        }

        .header h2 {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .header h2 i {
            color: #800000;
            font-size: 30px;
        }

        .header p {
            color: #718096;
            font-size: 14px;
            margin-top: 8px;
        }

        .scrollable-content {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }

        /* New Single Line Layout */
        .controls-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
            align-items: start;
        }

        .main-controls-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .left-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .right-camera {
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .form-group {
            background: white;
            padding: 20px;
            height: 400px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .form-group:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }

        .form-group label {
            font-weight: 600;
            color: #2d3748;
            display: block;
            margin-bottom: 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label i {
            margin-right: 8px;
            color: #800000;
        }

        select {
            width: 100%;
            padding: 10px 10px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background: white;
            font-size: 14px;
            color: #2d3748;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 12px;
        }

        select:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        select:disabled {
            background-color: #f7fafc;
            color: #a0aec0;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        option:disabled {
            color: #a0aec0;
            background-color: #f7fafc;
        }

        .duration-info {
            margin-top: 10px;
            padding: 8px 10px;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .duration-info.show {
            opacity: 1;
            transform: translateY(0);
        }

        .date-warning {
            background: linear-gradient(135deg, #fed7aa, #fdba74);
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 12px 15px;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
            font-weight: 500;
            font-size: 13px;
        }

        .date-warning i {
            margin-right: 8px;
            font-size: 14px;
        }

        .current-scan-type {
            text-align: center;
            margin-bottom: 15px;
            padding: 12px;
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 10px;
            border-left: 4px solid #800000;
        }

        #currentScanType {
            font-weight: 700;
            color: #800000;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .qr-reader-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        #reader {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .result {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            padding: 12px;
            border-radius: 10px;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .buttons-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        button {
            padding: 12px 16px;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
        }

        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(128, 0, 0, 0.4);
            background: linear-gradient(135deg, #a00000, #c00000);
        }

        button:active:not(:disabled) {
            transform: translateY(0);
        }

        button:disabled {
            background: linear-gradient(135deg, #cbd5e0, #a0aec0);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .danger-btn {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
        }
        
        .danger-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #c82333, #bd2130) !important;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4) !important;
        }

        /* Scrollbar Styling */
        .scrollable-content::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .scrollable-content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #800000, #a00000);
            border-radius: 3px;
        }

        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #a00000, #c00000);
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Tablet (max-width: 1024px) */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
                height: auto;
            }
            .container {
                margin-left: 0;
                margin-top: 20px;
                max-width: 98vw;
                max-height: none;
                padding: 10px;
            }
            .main-controls-section {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            .controls-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
            .right-camera {
                margin-top: 16px;
                justify-content: center;
            }
            .form-group {
                height: auto;
                padding: 12px;
            }
            .header h2 {
                font-size: 22px;
            }
            .buttons-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            button {
                font-size: 13px;
                padding: 10px 12px;
            }
            #reader {
                max-width: 250px;
            }
        }

        /* Mobile (max-width: 768px) */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 5px;
                height: auto;
            }
            .container {
                margin-left: 0;
                margin-top: 10px;
                max-width: 99vw;
                padding: 6px;
                border-radius: 12px;
            }
            .main-controls-section {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .controls-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .form-group {
                height: auto;
                padding: 8px;
            }
            .header h2 {
                font-size: 18px;
                gap: 6px;
            }
            .header p {
                font-size: 12px;
            }
            .buttons-section {
                grid-template-columns: 1fr;
                gap: 6px;
            }
            button {
                font-size: 12px;
                padding: 8px 8px;
            }
            #reader {
                max-width: 280px;
            }
            .qr-reader-container {
                padding: 8px;
                border-radius: 10px;
            }
            .result {
                font-size: 12px;
                padding: 8px;
            }
        }

        /* Small Mobile (max-width: 480px) */
        @media (max-width: 480px) {
            .main-content {
                margin-left: 0;
                padding: 2px;
                height: auto;
            }
            .container {
                margin-left: 0;
                margin-top: 4px;
                max-width: 100vw;
                padding: 2px;
                border-radius: 8px;
            }
            .form-group {
                padding: 4px;
                border-radius: 8px;
            }
            .header h2 {
                font-size: 15px;
                gap: 4px;
            }
            .header p {
                font-size: 10px;
            }
            .buttons-section {
                grid-template-columns: 1fr;
                gap: 4px;
            }
            button {
                font-size: 11px;
                padding: 6px 4px;
                border-radius: 7px;
            }
            #reader {
                max-width: 180px;
            }
            .qr-reader-container {
                padding: 4px;
                border-radius: 7px;
            }
            .result {
                font-size: 10px;
                padding: 4px;
            }
        }

        /* SweetAlert2 modal responsiveness */
        .swal2-popup {
            max-width: 40vw !important;
            width: 100% !important;
            box-sizing: border-box;
            border-radius: 16px !important;
            padding: 1.5rem 1rem !important;
        }

        @media (max-width: 600px) {
            .swal2-popup {
                max-width: 70vw !important;
                font-size: 1rem !important;
                padding: 1rem 0.5rem !important;
            }
            .swal2-title {
                font-size: 1.1rem !important;
            }
            .swal2-html-container {
                font-size: 0.98rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">

        <div class="container">
            <div class="header">
                <h2>
                    <i class="fas fa-qrcode"></i>
                    QR Code Attendance Scanner
                </h2>
                <p>Streamlined attendance tracking system for events and activities</p>
            </div>
            
            <div class="scrollable-content">
                @if(isset($error))
                    <div class="alert alert-danger">{{ $error }}</div>
                @endif
                <!-- New Single Line Layout -->
                <div class="main-controls-section">
                    <div class="left-controls">
                        <!-- Controls in one line -->
                        <div class="controls-grid">
                            <div class="form-group">
                                <label for="event_id">
                                    <i class="fas fa-calendar-alt"></i>
                                    Select Event
                                </label>
                                <select id="event_id">
                                    <option value="">-- Select Event --</option>
                                    @if(isset($events) && $events->count() > 0)
                                        @foreach($events as $event)
                                            <option value="{{ $event->event_id }}" 
                                                    data-duration="{{ $event->time_duration }}"
                                                    data-date="{{ $event->event_date }}"
                                                @if($event->is_finished) disabled @endif>
                                                {{ $event->event_name }} - {{ date('M d, Y', strtotime($event->event_date)) }}
                                                @if($event->is_finished) (Finished) @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No events available</option>
                                    @endif
                                </select>
                                <div id="timeDuration" class="duration-info"></div>
                                <div id="dateWarning" class="date-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This event is not scheduled for today. Attendance scanning is only allowed on the event date.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="timeduration_select">
                                    <i class="fas fa-clock"></i>
                                    Time Duration
                                </label>
                                <select id="timeduration_select" disabled>
                                    <option value="">-- Time Duration --</option>
                                    <option value="Whole Day">Whole Day</option>
                                    <option value="Half Day: Morning">Half Day: Morning</option>
                                    <option value="Half Day: Afternoon">Half Day: Afternoon</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="scan_type">
                                    <i class="fas fa-qrcode"></i>
                                    Scan Type
                                </label>
                                <select id="scan_type" disabled>
                                    <option value="">-- Select Scan Type --</option>
                                    <option value="am_in" class="morning-option">AM IN</option>
                                    <option value="am_out" class="morning-option">AM OUT</option>
                                    <option value="pm_in" class="afternoon-option">PM IN</option>
                                    <option value="pm_out" class="afternoon-option">PM OUT</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="current-scan-type">
                                    <div>Current Scan Mode:</div>
                                    <span id="currentScanType">No scan type selected</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="buttons-section">
                            <button type="button" id="openScanTypeBtn" disabled>
                                <i class="fas fa-play"></i> Open Scan Type
                            </button>
                            <button type="button" id="closeScanTypeBtn" disabled>
                                <i class="fas fa-stop"></i> Close Scan Type
                            </button>
                            <button type="button" id="printAttendanceBtn" disabled>
                                <i class="fas fa-print"></i> Print Attendance
                            </button>
                            <button type="button" id="finishEventBtn" class="danger-btn" disabled>
                                <i class="fas fa-flag-checkered"></i> Finish Event
                            </button>
                        </div>
                    </div>

                    <!-- Camera on the right -->
                    <div class="right-camera">
                        <div class="qr-reader-container">
                            <div id="reader"></div>
                            <div class="result" id="scanResult">Ready to scan QR codes</div>
                        </div>
                    </div>
                </div>

                <!-- Hidden Print Content -->
                <div id="printContent" style="display: none;"></div>

                <!-- Additional content section removed for cleaner layout -->
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="{{ asset('assets/js/attendanceqr.js') }}"></script>
</body>
</html>