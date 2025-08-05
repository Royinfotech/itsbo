<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treasurer</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Treasurer.css') }}">
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
            <img src="{{ $treasurer && $treasurer->image_path ? asset('storage/' . $treasurer->image_path) : asset('assets/pictures/Secretary.jpg') }}" 
                 alt="treasurer Photo" 
                 class="treasurer-photo">
            <h2 class="treasurer-name">{{ $treasurer ? $treasurer->first_name . ' ' . $treasurer->last_name : 'Treasurer Name' }}</h2>
            <div class="treasurer-details">
                <p>ITSBO {{ $treasurer ? $treasurer->position : 'Treasurer' }}</p>
                <p>Email: {{ $treasurer ? $treasurer->email : 'treasurer@example.com' }}</p>
            </div>    
            <ul>
                <li><a href="{{ route('treasurer.dashboard') }}" id="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('treasurer.payment') }}" id="payment"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="{{ route('treasurer.fines') }}" id="fines"><i class="fas fa-money-bill-wave"></i> Fines</a></li>
                <li><a href="{{ route('treasurer.transaction') }}" id="transactions"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                <li><a href="{{ route('treasurer.eventsam') }}" id="events"><i class="fas fa-calendar"></i> Events</a></li>
                <li><a href="{{ route('treasurer.orgstruct') }}" id="itsboOfficers"><i class="fas fa-user-tie"></i> ITSBO Officers</a></li>
            </ul>     
            <div class="logout-container">
                <a href="{{ route('login') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i> LOGOUT</a>
            </div>     
        </div> 
        <div class="main-content"></div>
    </div> 
    <script>
        // Pass the default page to JavaScript
        window.defaultPage = "{{ $defaultPage ?? 'Struct' }}";
    </script>
    <script src="{{ asset('assets/js/Treasurer.js') }}"></script>
</body>
</html>