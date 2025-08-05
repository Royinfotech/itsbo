<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Year & Semester Management</title>
    <link rel="stylesheet" href="{{ asset('assets/css/sy.css') }}">
</head>
<body>
    <div class="main-container">
        <h2>School Year & Semester Management</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">School Year Management</div>
            <div class="card-body">
                @if($current)
                    <div class="alert alert-info">
                        <strong>Current Active:</strong> {{ $current->year }} - {{ $current->semester }} Semester
                        
                        @if($current->semester === '1st')
                            <form method="POST" action="{{ route('superadmin.schoolyear.newsemester') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="year" value="{{ $current->year }}">
                                <input type="hidden" name="semester" value="2nd">
                                <input type="hidden" name="officer_limit" value="{{ $current->officer_limit }}">
                                <input type="hidden" name="positions" value="{{ is_array($current->open_positions) ? implode(',', $current->open_positions) : '' }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Open 2nd Semester
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('superadmin.schoolyear.close') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Close</button>
                        </form>
                    </div>
                @endif

                <!-- Form for new school year -->
                <form method="POST" action="{{ route('superadmin.schoolyear.open') }}">
                    @csrf
                    <div class="mb-3">
                        <label>School Year</label>
                        <input type="text" name="year" class="form-control" placeholder="e.g. 2024-2025" required>
                    </div>
                    <div class="mb-3">
                        <label>Semester</label>
                        <select name="semester" class="form-control" required>
                            <option value="1st">1st</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Officer Limit</label>
                        <input type="number" name="officer_limit" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label>Open Positions (comma separated)</label>
                        <input type="text" name="positions" class="form-control" placeholder="e.g. President, Vice President, Secretary" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Open School Year & Semester</button>
                </form>
            </div>
        </div>

        <h4>History</h4>
        <table>
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Officer Limit</th>
                    <th>Positions</th>
                    <th>Opened At</th>
                    <th>Closed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schoolYears as $sy)
                    <tr>
                        <td>{{ $sy->year }}</td>
                        <td>{{ $sy->semester }}</td>
                        <td>{{ $sy->is_open ? 'Open' : 'Closed' }}</td>
                        <td>{{ $sy->officer_limit }}</td>
                        <td>{{ is_array($sy->open_positions) ? implode(', ', $sy->open_positions) : '' }}</td>
                        <td>{{ $sy->opened_at }}</td>
                        <td>{{ $sy->closed_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
