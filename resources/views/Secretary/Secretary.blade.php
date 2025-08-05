<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretary</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Secretary.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
</head>
<body>
    <div class="dashboard-container">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <div class="toggle-logo">
                <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo">
            </div>
        </button>
        <div class="sidebar" id="sidebar">
            <img src="{{ $secretary && $secretary->image_path ? asset('storage/' . $secretary->image_path) : asset('assets/pictures/Secretary.jpg') }}" 
                 alt="Secretary Photo" 
                 class="secretary-photo">
            <h2 class="secretary-name">{{ $secretary ? $secretary->first_name . ' ' . $secretary->last_name : 'Secretary Name' }}</h2>
            <div class="secretary-details">
                <p>Position: {{ $secretary ? $secretary->position : 'Secretary' }}</p>
                <p>Email: {{ $secretary ? $secretary->email : 'secretary@example.com' }}</p>
            </div>    
            <ul>
                <li><a href="#" id="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
               <li><a href="#" id="events"><i class="fas fa-calendar"></i> Events</a></li>
               <li><a href="#" id="approveStudents"><i class="fas fa-user-graduate"></i> Pending Students</a></li>
               <li><a href="#" id="attendanceqr"><i class="fas fa-qrcode"></i>Attendance QR Code</a></li>
               <li><a href="#" id="officers"><i class="fas fa-user-tie"></i> Officer Registration</a></li>
            </ul>
            <div class="logout-container">
                <a href="{{ route('login') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>     
        </div> 
        <div class="main-content"></div>
    </div> 
    <script src="{{ asset('assets/js/Secretary.js') }}"></script>
</body>
</html>