<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrators Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/login-bootstrap.css') }}" rel="stylesheet">

    <style>
        /* Logo Animation */
        .logo-container {
            perspective: 1000px;
            width: 500px;
            height: 500px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .logo {
            width: 95%;
            height: 95%;
            position: relative;
            transform-style: preserve-3d;
            animation: logoRotate 3s ease-in-out infinite;
        }

        @keyframes logoRotate {
            0% { transform: rotateY(0deg); }
            50% { transform: rotateY(180deg); }
            100% { transform: rotateY(360deg); }
        }

        .logo-front, .logo-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 60%;
            overflow: hidden;
            transition: all 0.9s ease;
            background: transparent;
        }

        .logo-back {
            transform: rotateY(180deg);
        }

        .logo-front img, .logo-back img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: all 0.3s ease;
            background: transparent;
        }

        .logo:hover {
            animation-play-state: paused;
        }

        /* Loader Style */
        .loader {
            width: 150px;
            height: 150px;
            background: #4d1515;
            display: block;
            margin: 20px auto;
            position: relative;
            box-sizing: border-box;
            animation: rotationBack 1s ease-in-out infinite reverse;
            z-index: 10;
        }

        .loader::before {
            content: '';
            box-sizing: border-box;
            left: 0;
            top: 0;
            transform: rotate(45deg);
            position: absolute;
            width: 150px;
            height: 150px;
            background: #490202;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.15);
        }

        .loader img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            object-fit: contain;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.15);
            background: white;
        }

        @keyframes rotationBack {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }

        .loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.6);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        /* Responsive for tablets (768px and below) */
@media (max-width: 768px) {
    .logo-container {
        width: 150px;
        height: 150px;
    }

    .loader {
        width: 100px;
        height: 100px;
    }

    .loader::before {
        width: 100px;
        height: 100px;
    }

    .loader img {
        width: 64px;
        height: 64px;
    }
}

/* Responsive for phones (480px and below) */
@media (max-width: 480px) {
    .logo-container {
        width: 100px;
        height: 100px;
    }

    .loader {
        width: 80px;
        height: 80px;
    }

    .loader::before {
        width: 80px;
        height: 80px;
    }

    .loader img {
        width: 50px;
        height: 50px;
    }
}
    </style>
</head>
<body>
    <div class="main-container">
        <div class="row g-0 h-100">
            <div class="col-md-6 logo-side">
                <div class="logo-container">
                    <div class="logo">
                        <div class="logo-front">
                            <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo Front">
                        </div>
                        <div class="logo-back">
                            <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo Back">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 login-side">
                <div class="login-content">
                    <div class="text-center mb-4">
                        <h1 class="itsbo">ITSBO <span class="gateway">Gateway</span></h1>
                    </div>

                  <form method="POST" action="{{ route('login') }}" class="login-form">
    @csrf

    <div class="form-group mb-3">
        <label for="username" class="form-label">
            <i class="fas fa-user"></i> USERNAME
        </label>
        <input type="text" class="form-control @error('username') is-invalid @enderror" 
               id="username" name="username" placeholder="Enter your username" value="{{ old('username') }}" required autofocus>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">
            <i class="fas fa-lock"></i> PASSWORD
        </label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               id="password" name="password" placeholder="Enter your password" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-login">Login</button>
</form>
                 <div class="text-center mt-3">
                 <a href="#" class="forgot-password-link">
                 <i class="fas fa-unlock-alt me-1"></i> Forgot Password?</a>
</div>


                    
                    <div class="text-center mt-3">
                        <p class="text-muted">
                            &copy; {{ date('Y') }} ITSBO. All rights reserved.
                        </p>
                    </div>
                </div>


                    {!! NoCaptcha::renderJs() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Loader Overlay -->
<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-content">
        <div class="dot-spinner-wrapper">
            <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo" class="logo-shadow"/>
            <div class="dot-spinner">
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
                <div class="dot-spinner__dot"></div>
            </div>
        </div>
    </div>
</div>

   <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.querySelector('.login-form').addEventListener('submit', function () {
        document.getElementById('loaderOverlay').style.display = 'flex';
    });
</script>
</body>
</html>
