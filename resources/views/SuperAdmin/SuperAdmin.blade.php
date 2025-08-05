<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITSBO Super Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Superadmin.css') }}">
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
            <img src="../assets/pictures/itsbo.png" 
                 alt="secretary Photo" 
                 class="secretary-photo">
            <h2 class="secretary-name">ITSBO Gateway</h2>
            <div class="secretary-details">
                <p>ITSBO Super Admin</p>
                <p>Email: itsbogateway@gmail.com</p>
            </div>     
            <ul>
                <li><a href="#" id="itsboOfficers"><i class="fas fa-user-tie"></i> ITSBO Officers</a></li>
                <li><a href="#" id="manageUsers"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="#" id="dashboard"><i class="fas fa-tachometer-alt"></i> Treasurer Dashboard</a></li>
                <li><a href="#" id="payment"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="#" id="transaction"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                <li><a href="#" id="secdashboard"><i class="fas fa-tachometer-alt"></i> Secretary Dashboard</a></li>
                <li><a href="#" id="approveStudents"><i class="fas fa-user-graduate"></i> Pending Students</a></li>
                <li><a href="#" id="events"><i class="fas fa-calendar"></i> Events</a></li>
                <li><a href="#" id="attendanceqr"><i class="fas fa-qrcode"></i>Attendance QR Code</a></li>
                <li><a href="#" id="officers"><i class="fas fa-user-tie"></i> Officers</a></li>
                <li><a href="#" id="schoolYear"><i class="fas fa-calendar-plus"></i> School Year & Semester</a></li>
                <li><a href="#" id="archieve"><i class="fas fa-archive"></i> Archieve Data's</a></li>
            </ul>
            <div class="logout-container">
                <a href="{{ route('login') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>  
            </div>     
        </div> 
        <div class="main-content">
        </div>
    </div> 
    <script src="{{ asset('assets/js/superadmin.js') }}"></script>
</body>
</html>