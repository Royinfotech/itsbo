<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/student.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body data-student-id="{{ $student->student_id }}">
    <div class="dashboard-container">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <div class="toggle-logo">
                <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo">
            </div>
        </button>
        <div class="sidebar" id="sidebar">
            <img src="{{ $student && $student->photo ? asset('storage/' . $student->photo) : asset('assets/pictures/default-student.jpg') }}" 
                 alt="Student Photo" 
                 class="student-photo">
            <h2 class="student-name">{{ $student->student_name ?? 'Student Name' }}</h2>
            <div class="student-details">
                <p>Student ID: {{ $student->student_id ?? 'N/A' }}</p>
                <p>Email: {{ $student->email ?? 'N/A' }}</p>
                <p>Year Level: {{ $student->year_level ?? 'N/A' }}</p>
            </div>

                <ul>
                    <li><a href="#" data-page="announcement" id="announcement"><i class="fas fa-clipboard"></i> Announcement</a></li>
                    <li><a href="#" data-page="profile" id="profile"><i class="fa fa-user"></i> Profile</a></li>
                    <li><a href="#" data-page="qrcode" id="qrcode"><i class="fas fa-qrcode"></i> QR code</a></li>
                    <li><a href="#" data-page="attendance" id="attendancerecord"><i class="fas fa-clipboard-check"></i> Attendance Record</a></li>
                    <li><a href="#" data-page="StudentAccounts" id="accounts"><i class="fas fa-credit-card"></i> Accounts</a></li>
                    <li><a href="#" data-page="orgstruct" id="orgstruct"><i class="fas fa-user-tie"></i> ITSBO Officers</a></li>
                </ul>
                       <div class="logout-container">
                             <a href="{{ route('student.login') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>  
                     </div>  
         </div>

         <div class="main-content">
    </div>
    <script src="{{ asset('assets/js/Student.js') }}"></script>
</body>
</html>