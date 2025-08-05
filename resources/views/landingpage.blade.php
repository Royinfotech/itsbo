<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITSBO - Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
    <style>
        /* Updated Color Scheme */
        :root {
            --primary-color: #800000;    /* Maroon */
            --secondary-color: #FFD700;  /* Gold */
            --dark-color: #1a1a1a;      /* Black */
            --light-color: #ffffff;      /* White */
            --gray-color: #808080;      /* Gray */
            --silver-color: #C0C0C0;    /* Silver */
            --gradient-start: #800000;   /* Maroon */
            --gradient-end: #1a1a1a;     /* Black */
            --spacing-unit: 2rem;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            line-height: 1.8;
            margin: 0;
            overflow-x: hidden;
            background: var(--light-color);
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(128, 0, 0, 0.3);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(128, 0, 0, 0.5);
        }

        /* Enhanced Navigation */
        .navbar {
            padding: 1.5rem 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1.2rem !important;
            margin: 0 0.3rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(128, 0, 0, 0.1);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Dropdown Menu Hover Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 20px;
        }

        .dropdown-item {
            padding: 0.60rem 0.6rem;
            transition: all 0.3s ease;
            
        }

        .dropdown-item:hover {
            background-color: rgba(128, 0, 0, 0.1); /* Maroon with opacity */
            color: #800000; /* Maroon */
            border-radius: 10px;
        }

        .dropdown-item i {
            margin-right: 0px;
            color: #800000; /* Maroon icon color */
        }

        /* Modern Hero Section */
        .hero {
            min-height: 100vh;
            padding: 150px 0 100px;
            background: linear-gradient(135deg, 
                rgba(128, 0, 0, 0.95), 
                rgba(26, 26, 26, 0.85)), 
                url('{{ asset("assets/pictures/LoginBackg.png") }}') no-repeat center center/cover;
            position: relative;
            overflow: hidden;
            color: var(--light-color);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
            pointer-events: none;
        }

        .hero h1 {
            font-size: 3.9rem;
            font-weight: 900;
            margin-bottom: 20px;
            line-height: 1.0;
            background: linear-gradient(45deg, var(--light-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: titleGlow 3s ease-in-out infinite;
        }

        @keyframes titleGlow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.9; }
        }

        /* Enhanced Logo Animation */
        .logo-container {
            perspective: 1000px;
            margin: var(--spacing-unit) auto;
            width: 280px;
            height: 280px;
        }

        .logo {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-15px) rotate(5deg); }
            75% { transform: translateY(15px) rotate(-5deg); }
        }

        .logo-front, .logo-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 50%;
            box-shadow: 0 2px 2px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-front img, .logo-back img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        /* Modern Features Section */
        .features {
            padding: calc(var(--spacing-unit) * 3) 0;
            background: var(--light-color);
            position: relative;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid var(--silver-color);
            transform-style: preserve-3d;
            perspective: 1000px;
            padding: calc(var(--spacing-unit) * 1.5);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            color: var(--dark-color);
        }

        .feature-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .feature-icon {
            color: var(--primary-color);
            transform: translateZ(30px);
            transition: all 0.5s ease;
            font-size: 2.5rem;
            margin-bottom: var(--spacing-unit);
        }

        .feature-card:hover .feature-icon {
            transform: translateZ(50px) scale(1.2);
            color: var(--secondary-color);
        }

        /* Modern Highlights Section */
        .highlights {
            padding: calc(var(--spacing-unit) * 3) 0;
            background: var(--light-color);
            overflow: hidden;
        }

        .highlights-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 40px 0;
        }

        .highlights-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            animation: scrollRow 20s linear infinite;
        }

        .highlights-row:nth-child(2) {
            animation-direction: reverse;
            animation-duration: 10s;
        }

        @keyframes scrollRow {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        .highlight-item {
            min-width: 200px;
            height: 200px;
            flex: 0 0 auto;
            transition: all 0.5s ease;
            position: relative;
            opacity: 0.3;
            transform: scale(0.7);
        }

        .highlight-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
            transition: all 0.5s ease;
            filter: grayscale(100%);
        }

        .highlight-item.center {
            opacity: 1;
            transform: scale(1);
        }

        .highlight-item.center .highlight-image {
            filter: grayscale(0%);
            box-shadow: 0 12px 25px rgba(0,0,0,0.3);
            z-index: 2;
        }

        /* Modern About Section */
        .about {
            padding: calc(var(--spacing-unit) * 3) 0;
            background: linear-gradient(135deg, 
                rgba(128, 0, 0, 0.05),
                rgba(255, 215, 0, 0.05));
            position: relative;
            overflow: hidden;
        }

        .about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(45deg, rgba(128, 0, 0, 0.1) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(128, 0, 0, 0.1) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, rgba(128, 0, 0, 0.1) 75%),
                linear-gradient(-45deg, transparent 75%, rgba(128, 0, 0, 0.1) 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            opacity: 0.5;
            pointer-events: none;
        }

        .about h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: var(--spacing-unit);
            position: relative;
            display: inline-block;
            color: var(--primary-color);
        }

        .about h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50%;
            height: 4px;
            background: var(--secondary-color);
            border-radius: 2px;
        }

        .about p {
            color: var(--dark-color);
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .about img {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .about img:hover {
            transform: scale(1.02);
        }

        /* Modern Footer */
        footer {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            padding: 3rem 0 1rem;
            position: relative;
            overflow: hidden;
            color: var(--light-color);
        }

        footer h4 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            color: var(--secondary-color);
        }

        footer p, footer li {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--light-color);
            opacity: 0.9;
        }

        footer a {
            color: var(--light-color);
            text-decoration: none;
            transition: all 0.3s ease;
            opacity: 0.9;
        }

        footer a:hover {
            color: var(--secondary-color);
            opacity: 1;
            transform: translateX(5px);
        }

        .social-links {
            margin-top: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            line-height: 40px;
            font-size: 1.1rem;
            margin: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--secondary-color);
            color: var(--primary-color);
            transform: translateY(-3px);
        }

        footer .container {
            max-width: 1200px;
        }

        footer hr {
            margin: 2rem 0 1rem;
            opacity: 0.2;
            border-color: var(--light-color);
        }

        footer .mb-0 {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .footer-links a {
            display: inline-block;
            padding: 0.3rem 0;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero h3 {
                font-size: 1.5rem;
            }

            .logo-container {
                width: 200px;
                height: 200px;
            }

            .feature-card {
                margin-bottom: var(--spacing-unit);
            }
        }
    </style>
</head>
<body>
    <div class="sway"></div> <!-- Sway effect overlay -->

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">ITSBO Gateway</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">
                            <i class="fas fa-star"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#highlights">
                            <i class="fas fa-images"></i> Highlights
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('student.login') }}">
                                    <i class="fas fa-user-graduate"></i> Student Login
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('login') }}">
                                    <i class="fas fa-user-shield"></i> Administrators Login
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <h1 class="animate__animated animate__fadeInDown">Welcome to ITSBO</h1>
                    <p class="lead animate__animated animate__fadeInUp">Empowering future IT professionals through leadership, innovation, and community.</p>
                    <a href="{{ route('register.form') }}" class="btn btn-light btn-lg animate__animated animate__fadeInUp">Register Now</a>
                </div>
                <div class="col-lg-6">
                    <div class="logo-container">
                        <div class="logo">
                            <div class="logo-front">
                                <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo">
                            </div>
                            <div class="logo-back">
                                <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="text-center mb-5">What We Offer</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-users feature-icon"></i>
                        <h3>Student Community</h3>
                        <p>Connect with BSIT students, share experiences, and build lasting relationships.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-calendar-alt feature-icon"></i>
                        <h3>Events & Activities</h3>
                        <p>Participate in tech workshops, seminars, and social events throughout the year.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-gavel feature-icon"></i>
                        <h3>Good Governance</h3>
                        <p>Implementing efficient practices to enhance organizational effectiveness.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Highlights Section -->
    <section class="highlights" id="highlights">
        <div class="container">
            <h2 class="text-center mb-5">Highlights</h2>
            <div class="highlights-container">
                <!-- First Row -->
                <div class="highlights-row" id="row1">
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="highlight-item">
                            <img src="{{ asset('assets/pictures/' . $i . '.jpg') }}" 
                                 alt="Highlight {{ $i }}" 
                                 class="highlight-image">
                        </div>
                    @endfor
                    <!-- Duplicate for seamless loop -->
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="highlight-item">
                            <img src="{{ asset('assets/pictures/' . $i . '.jpg') }}" 
                                 alt="Highlight {{ $i }}" 
                                 class="highlight-image">
                        </div>
                    @endfor
                </div>
                
                <!-- Second Row -->
                <div class="highlights-row" id="row2">
                    @for ($i = 11; $i <= 20; $i++)
                        <div class="highlight-item">
                            <img src="{{ asset('assets/pictures/' . $i . '.jpg') }}" 
                                 alt="Highlight {{ $i }}" 
                                 class="highlight-image">
                        </div>
                    @endfor
                    <!-- Duplicate for seamless loop -->
                    @for ($i = 11; $i <= 20; $i++)
                        <div class="highlight-item">
                            <img src="{{ asset('assets/pictures/' . $i . '.jpg') }}" 
                                 alt="Highlight {{ $i }}" 
                                 class="highlight-image">
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>About Our Organization</h2>
                    <p>The Informstion Technology Student Body Organization is dedicated to fostering good governance, professional development, and community engagement among Information Technology students.</p>
                    <p>Our mission is to implement efficient governance practices that enhance the effectiveness of our organization and prepare students for successful careers in the IT industry through practical experience and networking opportunities.</p>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('assets/pictures/LoginBackg.png') }}" alt="About Us" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h4><i class="fas fa-envelope me-2"></i>Contact Information</h4>
                    <p><i class="fas fa-envelope me-2"></i>itsbo2025@gmail.com</p>
                    <p><i class="fas fa-phone me-2"></i>(123) 456-7890</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i>T.N. Pepito Street IT Laboratory, IBA College of Mindanao Inc. Main Campus</p>
                </div>
                <div class="col-md-4">
                    <h4><i class="fas fa-link me-2"></i>Quick Links</h4>
                    <div class="footer-links">
                        <a href="#home"><i class="fas fa-chevron-right me-2"></i>Home</a>
                        <a href="#features"><i class="fas fa-chevron-right me-2"></i>Features</a>
                        <a href="#about"><i class="fas fa-chevron-right me-2"></i>About</a>
                        <a href="#highlights"><i class="fas fa-chevron-right me-2"></i>Highlights</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4><i class="fas fa-share-alt me-2"></i>Connect With Us</h4>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>

                    </div>
                    <p class="mt-3">Follow us on social media for the latest updates and news.</p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2025 ITSBO - Information Technology Student Body Organization. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll reveal animation
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            
            reveals.forEach(element => {
                var windowHeight = window.innerHeight;
                var elementTop = element.getBoundingClientRect().top;
                var elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    element.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', reveal);
        reveal(); // Initial check

        // Enhanced navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
                navbar.style.padding = '0.5rem 0';
            } else {
                navbar.classList.remove('scrolled');
                navbar.style.padding = '1.5rem 0';
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Center-focused effect for highlights
        function updateCenterItems() {
            const rows = document.querySelectorAll('.highlights-row');
            rows.forEach(row => {
                const items = row.querySelectorAll('.highlight-item');
                const containerWidth = row.offsetWidth;
                const containerCenter = containerWidth / 2;

                items.forEach(item => {
                    const rect = item.getBoundingClientRect();
                    const itemCenter = rect.left + rect.width / 2;
                    const distanceFromCenter = Math.abs(itemCenter - containerCenter);
                    const threshold = containerWidth * 0.15; // 15% of container width

                    if (distanceFromCenter < threshold) {
                        item.classList.add('center');
                    } else {
                        item.classList.remove('center');
                    }
                });
            });
        }

        // Update center items on scroll and resize
        window.addEventListener('scroll', updateCenterItems);
        window.addEventListener('resize', updateCenterItems);
        window.addEventListener('load', updateCenterItems);

        // Update center items more frequently for smoother animation
        setInterval(updateCenterItems, 50);
    </script>
</body>
</html>
