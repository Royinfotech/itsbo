<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Transaction Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px 0 0 0;
            text-align: center;
            position: relative;
            display: flex;
            justify-content: center;
            height: auto;
        }

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

        .dashboard-container {
            width: 90%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            margin-left: 50px;
            flex-direction: column;
            align-items: center;
            transition: 0.3s ease;
            margin-top: 20px;
            border-top: 5px solid maroon;
        }

        h2 {
            color: #000000;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .report-info {
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #6d1b1b;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .print-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            background: rgba(109, 27, 27, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(109, 27, 27, 0.1);
        }

        .date-input-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .date-input-group label {
            font-size: 12px;
            font-weight: 500;
            color: #6d1b1b;
        }

        input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #6d1b1b;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            width: 150px;
            background: white;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #541414;
            box-shadow: 0 0 5px rgba(109, 27, 27, 0.2);
        }

        button {
            background-color: #6d1b1b;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s ease;
            margin: 0 5px;
        }

        button:hover {
            background-color: #541414;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background: rgba(109, 27, 27, 0.1);
            border-radius: 5px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .summary-item {
            font-weight: 500;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            border: 1px solid rgba(109, 27, 27, 0.2);
        }

        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: 500;
            width: 100%;
            box-sizing: border-box;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }

        .amount-cell {
            text-align: right;
            font-weight: 500;
        }

        .status-paid {
            color: #28a745;
            font-weight: 500;
        }

        .status-pending {
            color: #ffc107;
            font-weight: 500;
        }

        .status-failed {
            color: #dc3545;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                width: 90%;
                margin-left: 0;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }

            .filter-section {
                flex-direction: column;
                gap: 15px;
            }

            .date-input-group {
                align-items: center;
            }

            input[type="date"] {
                width: 200px;
            }

            .filter-buttons {
                justify-content: center;
            }

            .summary-section {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                width: 95%;
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 6px;
            }

            button {
                padding: 8px 12px;
                font-size: 12px;
            }

            .summary-section {
                gap: 10px;
            }

            .summary-item {
                font-size: 12px;
                padding: 4px 8px;
            }

            input[type="date"] {
                width: 180px;
                font-size: 12px;
            }
        }

        @media print {
            body::before {
                display: none;
            }
            .filter-section, button {
                display: none !important;
            }
            .dashboard-container {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
            .alert {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Payment Transaction Report</h2>

        <div class="report-info">
            @if(isset($activeSchoolYear) && $activeSchoolYear)
                School Year: {{ $activeSchoolYear->year }} - {{ $activeSchoolYear->semester }}
            @else
                School Year: No open school year
            @endif

            @if(request('from_date') && request('to_date'))
                | Period: {{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }} to {{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}
            @elseif(request('from_date'))
                | From: {{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }}
            @elseif(request('to_date'))
                | Until: {{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}
            @else
                | All Dates
            @endif

            | Generated: {{ \Carbon\Carbon::now()->format('M d, Y h:i A') }}
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if(isset($error))
            <div class="alert alert-danger">
                <strong>Error:</strong> {{ $error }}
            </div>
        @endif

        <div class="filter-section">
            <form method="GET" id="filterForm" style="display: flex; gap: 20px; align-items: end; flex-wrap: wrap; justify-content: center;">
                <div class="date-input-group">
                    <label for="from_date">From Date:</label>
                    <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}">
                </div>

                <div class="date-input-group">
                    <label for="to_date">To Date:</label>
                    <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}">
                </div>

                <div class="filter-buttons">
                    <button type="button" onclick="printReport()">Print Report</button>
                </div>
            </form>
        </div>

    <script>
        function printReport() {
            window.print();
        }

        function clearFilters() {
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';
        }

        // Set max date to today for both inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('from_date').setAttribute('max', today);
            document.getElementById('to_date').setAttribute('max', today);
            
            // Ensure to_date is not earlier than from_date
            document.getElementById('from_date').addEventListener('change', function() {
                const fromDate = this.value;
                if (fromDate) {
                    document.getElementById('to_date').setAttribute('min', fromDate);
                }
            });
        });
    </script>
</body>
</html>