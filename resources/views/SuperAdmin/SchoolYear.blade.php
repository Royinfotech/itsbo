<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Year & Semester Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-maroon: #800020;
            --light-maroon: #a0404e;
            --dark-maroon: #5c001a;
            --maroon-bg: #f8f4f5;
            --accent-gold: #d4af37;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --shadow: 0 4px 20px rgba(128, 0, 32, 0.1);
            --shadow-hover: 0 8px 30px rgba(128, 0, 32, 0.15);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            color: #2c3e50;
            line-height: 1.6;
            padding: 5px 0 0 0;
        }

        /* Background Overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../pictures/LoginBackg.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.3;
            z-index: -1;
        }

        .main-container {
            margin-left: 60px; /* Preserved original sidebar space */
            padding: 30px;
            max-width: 1300px;
            border-radius: 10px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-maroon);
            margin-bottom: 0.5rem;
            position: relative;
            background-color: #ffffff;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-maroon), var(--accent-gold));
            border-radius: 2px;
        }

        /* Alert Styles - Preserved Laravel functionality */
        .alert {
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: none;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-success::before {
            background: var(--success);
        }

        .alert-info {
            background: linear-gradient(135deg, var(--maroon-bg) 0%, #fdf2f3 100%);
            color: var(--primary-maroon);
            border: 1px solid rgba(128, 0, 32, 0.1);
        }

        .alert-info::before {
            background: var(--primary-maroon);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .alert-danger::before {
            background: var(--danger);
        }

        /* Card Styles */
        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(128, 0, 32, 0.05);
        }

        .card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 100%);
            color: var(--white);
            padding: 1.5rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Form Styles - Preserved Laravel form structure */
        .mb-3 {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-maroon);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--medium-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.1);
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--medium-gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: var(--primary-maroon);
            box-shadow: 0 0 0 3px rgba(128, 0, 32, 0.1);
        }

        /* Button Styles - Preserved Laravel functionality */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-maroon) 0%, var(--primary-maroon) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 0, 32, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%);
            color: var(--white);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .d-inline {
            display: inline-block;
            margin-right: 0.75rem;
            margin-top: 1rem;
        }

        /* Current Status Section */
        .current-status {
            background: linear-gradient(135deg, var(--maroon-bg) 0%, #fff 100%);
            border: 2px solid rgba(128, 0, 32, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .current-status::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-maroon), var(--accent-gold));
            border-radius: 12px 12px 0 0;
        }

        .status-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .status-badge {
            background: var(--primary-maroon);
            color: var(--white);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Table Styles - Preserved Laravel table structure */
        h4 {
            color: var(--primary-maroon);
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 0;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-top: 2rem;
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: var(--shadow);
            margin-top: 1rem;
            border-radius: 16px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--medium-gray);
        }

        th {
            background: linear-gradient(135deg, var(--primary-maroon) 0%, var(--light-maroon) 100%);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background: var(--maroon-bg);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 1rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            table {
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }

            .d-inline {
                display: block;
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h2 class="page-title">
                <i class="fas fa-graduation-cap"></i>
                School Year & Semester Management
            </h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <i class="fas fa-cog"></i>
                School Year Management
            </div>
            <div class="card-body">
                @if($current)
                    <div class="current-status alert alert-info">
                        <div class="status-header">
                            <span class="status-badge">ACTIVE</span>
                            <strong>Current Active:</strong> {{ $current->year }} - {{ $current->semester }} Semester
                        </div>
                        
                        @if($current->semester === '1st')
                            <form method="POST" action="{{ route('superadmin.schoolyear.newsemester') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="year" value="{{ $current->year }}">
                                <input type="hidden" name="semester" value="2nd">
                                <input type="hidden" name="officer_limit" value="{{ $current->officer_limit }}">
                                <input type="hidden" name="positions" value="{{ is_array($current->open_positions) ? implode(',', $current->open_positions) : '' }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-forward"></i>
                                    Open 2nd Semester
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('superadmin.schoolyear.close') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-times-circle"></i>
                                Close
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Form for new school year -->
                <form method="POST" action="{{ route('superadmin.schoolyear.open') }}">
                    @csrf
                    <div class="mb-3">
                        <label>
                            <i class="fas fa-calendar-alt"></i>
                            School Year
                        </label>
                        <input type="text" name="year" class="form-control" placeholder="e.g. 2024-2025" required>
                    </div>
                    <div class="mb-3">
                        <label>
                            <i class="fas fa-list-ol"></i>
                            Semester
                        </label>
                        <select name="semester" class="form-control" required>
                            <option value="">Select Semester</option>
                            <option value="1st">1st</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>
                            <i class="fas fa-users"></i>
                            Officer Limit
                        </label>
                        <input type="number" name="officer_limit" class="form-control" min="1" placeholder="Enter number of officers" required>
                    </div>
                    <div class="mb-3">
                        <label>
                            <i class="fas fa-user-tie"></i>
                            Open Positions (comma separated)
                        </label>
                        <input type="text" name="positions" class="form-control" placeholder="e.g. President, Vice President, Secretary" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">
                        <i class="fas fa-rocket"></i>
                        Open School Year & Semester
                    </button>
                </form>
            </div>
        </div>

        <h4>
            <i class="fas fa-history"></i>
            Academic Period History
        </h4>
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-calendar"></i> Year</th>
                    <th><i class="fas fa-list-ol"></i> Semester</th>
                    <th><i class="fas fa-circle"></i> Status</th>
                    <th><i class="fas fa-users"></i> Officer Limit</th>
                    <th><i class="fas fa-user-tie"></i> Positions</th>
                    <th><i class="fas fa-play"></i> Opened At</th>
                    <th><i class="fas fa-stop"></i> Closed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schoolYears as $sy)
                    <tr>
                        <td>{{ $sy->year }}</td>
                        <td>{{ $sy->semester }}</td>
                        <td>
                            <span class="{{ $sy->is_open ? 'status-open' : 'status-closed' }}" style="font-weight: 600; color: {{ $sy->is_open ? 'var(--success)' : 'var(--dark-gray)' }};">
                                {{ $sy->is_open ? 'Open' : 'Closed' }}
                            </span>
                        </td>
                        <td>{{ $sy->officer_limit }}</td>
                        <td>{{ is_array($sy->open_positions) ? implode(', ', $sy->open_positions) : '' }}</td>
                        <td>{{ $sy->opened_at }}</td>
                        <td>{{ $sy->closed_at ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Enhanced form interactions
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Table row interactions
        document.querySelectorAll('table tbody tr').forEach(row => {
            row.addEventListener('click', function() {
                // Remove previous selections
                document.querySelectorAll('table tbody tr').forEach(r => {
                    r.style.backgroundColor = '';
                });
                
                // Highlight selected row
                this.style.backgroundColor = 'rgba(128, 0, 32, 0.1)';
            });
        });

        // Form validation feedback
        document.querySelectorAll('.form-control, input, select').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = 'var(--danger)';
                    this.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.1)';
                } else {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                }
            });

            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary-maroon)';
                this.style.boxShadow = '0 0 0 3px rgba(128, 0, 32, 0.1)';
            });
        });

        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>