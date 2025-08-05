<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <img src="{{ $president && $president->image_path ? asset('storage/' . $president->image_path) : asset('assets/pictures/President.jpg') }}" 
                 alt="secretary Photo" 
                 class="seretary-photo">
            <h2 class="secretary-name">{{ $president ? $president->first_name . ' ' . $president->last_name : 'President Name' }}</h2>
            <div class="secretary-details">
                <p>Position: {{ $president ? $president->position : 'President' }}</p>
                <p>Email: {{ $president ? $president->email : 'president@example.com' }}</p>
            </div>      
            <ul>
                <li><a href="#" id="itsboOfficers"><i class="fas fa-user-tie"></i> ITSBO Officers</a></li>
                <li><a href="#" id="manageUsers"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="#" id="Dashboard"><i class="fas fa-file-alt"></i> Secretary's Report</a></li>
            </ul>

            <div class="logout-container">
                <a href="{{ route('login') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>  
            </div>     
        </div> 

        <div class="main-content">
            {{-- Content goes here --}}
        </div>

    </div> 

    <!-- Load JavaScript -->
    <script src="{{ asset('assets/js/Admin.js') }}"></script>
</body>
</html>

