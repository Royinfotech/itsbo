<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/Officers.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="main-content">
        <div class="container">
            <h2>Officer Registration</h2>
            @php
                $currentSchoolYear = \App\Models\SchoolYear::where('is_open', true)->first();
                $positions = $currentSchoolYear ? $currentSchoolYear->open_positions : [];
            @endphp

            @if(!$currentSchoolYear)
                <div class="alert alert-warning">No open school year/semester. Please contact the SuperAdmin.</div>
            @else
                <div class="alert alert-info">
                    <h4>Current School Year: {{ $currentSchoolYear->year }} - {{ $currentSchoolYear->semester }} Semester</h4>
                    @if($currentSchoolYear->semester === '2nd')
                        <small>Officers carried over from 1st semester</small>
                    @endif
                </div>
            @endif

            <form id="officerForm" method="POST" action="{{ route('officers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label>Last Name:</label>
                    <input type="text" 
                           id="last_name"
                           name="last_name" 
                           placeholder="Enter last name" 
                           required>
                </div>
                
                <div class="mb-3">
                    <label>First Name:</label>
                    <input type="text" 
                           id="first_name"
                           name="first_name" 
                           placeholder="Enter first name" 
                           required>
                </div>
                
                <div class="mb-3">
                    <label>Middle Name:</label>
                    <input type="text" 
                           id="middle_name"
                           name="middle_name" 
                           placeholder="Enter middle name">
                </div>
                
                <div class="mb-3">
                    <label>Birthdate:</label>
                    <input type="date" name="birthdate" required>
                </div>
                
                <label>Email Address:</label>
                <input type="email" name="email" placeholder="Enter email" required>
                
                <label>Position:</label>
                <select id="position" name="position" required>
                    @if($positions)
                        @foreach($positions as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
                        @endforeach
                    @else
                        <option value="">No positions available</option>
                    @endif
                </select>
                
                <label>Image:</label>
                <input type="file" name="image" accept="image/*" required max="20480000">
                
                <button type="submit">Add Officer</button>
            </form>
        </div>

        <div class="container">
            <h2>Officer Records</h2>
            @php
                $officers = $currentSchoolYear ? \App\Models\Officer::where('school_year_id', $currentSchoolYear->id)->orderBy('created_at', 'desc')->get() : collect();
            @endphp
            <table id="officersTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Full Name</th>
                        <th>Birthdate</th>
                        <th>Email</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($officers) && $officers->count() > 0)
                        @foreach($officers as $officer)
                        <tr class="officer-row" 
                            data-id="{{ $officer->id }}"
                            data-last-name="{{ $officer->last_name }}"
                            data-first-name="{{ $officer->first_name }}"
                            data-middle-name="{{ $officer->middle_name }}"
                            data-birthdate="{{ $officer->birthdate }}"
                            data-email="{{ $officer->email }}"
                            data-position="{{ $officer->position }}"
                            data-image-path="{{ $officer->image_path }}"
                            style="cursor: pointer;">
                            <td><img src="{{ asset('storage/' . $officer->image_path) }}" alt="Officer Image" width="50"></td>
                            <td>{{ $officer->last_name }}, {{ $officer->first_name }} {{ $officer->middle_name }}</td>
                            <td>{{ $officer->birthdate }}</td>
                            <td>{{ $officer->email }}</td>
                            <td>{{ $officer->position }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">No officers found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div id="statusMessage" class="alert" style="display: none;"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/Officers.js') }}"></script>

    <!-- Update Modal -->
    <div class="modal fade" id="updateOfficerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateOfficerForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="update_officer_id" name="officer_id">
                        
                        <div class="mb-3">
                            <label>Last Name:</label>
                            <input type="text" id="update_last_name" name="last_name" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label>First Name:</label>
                            <input type="text" id="update_first_name" name="first_name" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label>Middle Name:</label>
                            <input type="text" id="update_middle_name" name="middle_name" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label>Birthdate:</label>
                            <input type="date" id="update_birthdate" name="birthdate" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label>Email Address:</label>
                            <input type="email" id="update_email" name="email" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label>Position:</label>
                            <select id="update_position" name="position" class="form-control">
                                <option value="President">President</option>
                                <option value="Vice President">Vice President</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Assistant Secretary">Assistant Secretary</option>
                                <option value="Treasurer">Treasurer</option>
                                <option value="Assistant Treasurer">Assistant Treasurer</option>
                                <option value="Auditor">Auditor</option>
                                <option value="Pio1">PIO 1</option>
                                <option value="Pio2">PIO 2</option>
                                <option value="Sgt at Arms">Sgt at Arms</option>
                                <option value="1st Year Representative">1st Year Representative</option>
                                <option value="2nd Year Representative">2nd Year Representative</option>
                                <option value="3rd Year Representative">3rd Year Representative</option>
                                <option value="4th Year Representative">4th Year Representative</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label>New Image (optional):</label>
                            <input type="file" id="update_image" name="image" class="form-control" accept="image/*" max="20480000">
                        </div>

                        <div class="current-image mb-3">
                            <label>Current Image:</label>
                            <img id="current_image" src="" alt="Current Officer Image" style="max-width: 100px;">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Officer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>