<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/student.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body data-student-id="{{ $student->student_id }}">

    <div class="dashboard-container">

        <!-- Sidebar Toggle Button -->
        <button class="toggle-btn" onclick="toggleSidebar()">
            <div class="toggle-logo">
                <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo">
            </div>
        </button>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">

            <!-- Student Photo -->
            <img src="{{ $student && $student->photo ? asset('storage/' . $student->photo) : asset('assets/pictures/default-student.jpg') }}" 
                 alt="Student Photo" 
                 class="student-photo">

            <!-- Student Name -->
            <h2 class="student-name">{{ $student->student_name ?? 'Student Name' }}</h2>

            <!-- Student Info -->
            <div class="student-details">
                <p>Student ID: {{ $student->student_id ?? 'N/A' }}</p>
                <p>Email: {{ $student->email ?? 'N/A' }}</p>
                <p>Year Level: {{ $student->year_level ?? 'N/A' }}</p>
            </div>

            <!-- Navigation Menu -->
            <ul class="nav-menu">
                <li><a href="#" id="announcement"><i class="fas fa-clipboard"></i> Announcement</a></li>
                <li><a href="#" id="profile"><i class="fa fa-user"></i> Profile</a></li>
                <li><a href="#" id="qrcode"><i class="fas fa-qrcode"></i> QR Code</a></li>
                <li><a href="#" id="attendancerecord"><i class="fas fa-clipboard-check"></i> Attendance Record</a></li>
                <li><a href="#" id="accounts"><i class="fas fa-credit-card"></i> Accounts</a></li>
                <li><a href="#" id="orgstruct"><i class="fas fa-user-tie"></i> ITSBO Officers</a></li>
            </ul>

            <!-- Logout -->
            <div class="logout-container">
                <a href="{{ route('student.login') }}" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

        </div> <!-- End Sidebar -->

        <!-- Main Content -->
        <div class="main-content">
            <!-- Dynamic content goes here -->
        </div>

    </div> <!-- End Dashboard Container -->

    <!-- Scripts -->
    <script src="{{ asset('assets/js/Student.js') }}"></script>
    <script>
    // Prevent back button after logout
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
</script>

</body>
</html>
