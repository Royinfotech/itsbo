<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - ITSBO</title>
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="{{ asset('assets/css/studentlog.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
    
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
.modal-content {
    background-color: #fdfdfd;
    border-radius: 10px;
    padding: 20px;
    width: 100%;
}

.modal-header {
    background-color: #4d1515;
    color: white;
    border-bottom: 2px solid #ddd;
}

.modal-header .modal-title {
    font-weight: bold;
    color: #ddd;
}

.modal-body {
    color: #333;
    font-size: 0.95rem;
    line-height: 1.6;
}

.modal-body p {
    margin-bottom: 1rem;
}

.modal-body ul {
    padding-left: 1.2rem;
}

.modal-body ul li {
    margin-bottom: 0.5rem;
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

.modal-header .btn-close {
        color: white;
        opacity: 1;
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    }

    .modal-header .btn-close:hover {
        opacity: 0.75;
        transform: scale(1.1);
        transition: all 0.2s ease;
    }
.register-link {
    text-align: center;
    margin-top: 1.5rem;
    padding: 1rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

/* Link styling */
.register-link a {
    color: #0551a2;
    text-decoration: none;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    position: relative;
    margin-left: 0.25rem;
}

.register-link a:hover {
    color: #840b0b;
    text-decoration: none;
}

.register-link a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background-color: #6d0707;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.register-link a:hover::after {
    width: 100%;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .register-link {
        margin-top: 1rem;
        padding: 0.75rem;
    }
    
    .register-link p {
        font-size: 0.9rem;
    }
}

/* Alternative modern variant with gradient */
.register-link.modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.register-link.modern p {
    color: rgba(255, 255, 255, 0.9);
}

.register-link.modern a {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.register-link.modern a:hover {
    background-color: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

/* Minimal variant */
.register-link.minimal {
    background: transparent;
    border: none;
    padding: 0.5rem;
}

.register-link.minimal:hover {
    background: transparent;
    box-shadow: none;
    transform: none;
}

/* Forgot Password Link Styling */
.forgot-password-link {
    color: #6c757d;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.forgot-password-link:hover {
    color: #4d1515;
    text-decoration: underline;
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
            <div class="col-md-6 login-side student-login-side">
                <div class="login-content">
                    <div class="text-center mb-4">
                        <h1 class="itsbo">Student <span class="gateway">Portal</span></h1>
                    </div>
                    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

                    <form method="POST" action="{{ route('student.login.submit') }}" class="login-form">
    @csrf
    <!-- Add this for debugging -->
    @if(session('debug'))
        <div class="alert alert-info">
            {{ session('debug') }}
        </div>
    @endif

    <div class="form-group mb-3">
        <label for="student_id" class="form-label">
            <i class="fas fa-id-card"></i> STUDENT ID
        </label>
        <input type="text" class="form-control @error('student_id') is-invalid @enderror" 
               id="student_id" name="student_id" placeholder="Enter your student ID" 
               value="{{ old('student_id') }}" required autofocus>
        @error('student_id')
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

    <button type="submit" class="btn btn-login">
        <i class="fas fa-sign-in-alt"></i> Login
    </button>
</form>

                    <!-- Forgot Password Link - Fixed positioning -->
                    <div class="text-center mt-3">
                        <a href="#" class="forgot-password-link">
                            <i class="fas fa-unlock-alt me-1"></i> Forgot Password?
                        </a>
                    </div>
                    
                    <div class="register-link text-center mt-3">
                        <p class="text-muted">
                            Don't have an account? 
                            <a href="{{ route('register.form') }}" class="text-decoration-none">Register Now</a>
                        </p>
                    </div>
                 
                    <div class="text-center mt-3">
                        <p class="text-muted">
                            By logging in, you agree to our 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-decoration-none">Terms of Service</a> and 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-decoration-none">Privacy Policy</a>
                        </p>
                    </div>
                    <div class="text-center mt-3">
                        <p class="text-muted">
                            &copy; {{ date('Y') }} ITSBO. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Terms of Service Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsLabel">Terms of Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>📄 ITSBO Gateway – Terms of Service</h6>
<p>Welcome to the ITSBO Gateway, a digital platform by the Information Technology Student Body Organization (ITSBO).</p>
<p>By using this system, you agree to:</p>
<ul>
    <li>Provide accurate registration information</li>
    <li>Use the platform only for academic and organizational purposes</li>
    <li>Respect the platform rules and other users</li>
</ul>
<h6>Key Terms:</h6>
<ul>
    <li><strong>Eligibility:</strong> Officially enrolled IT students and authorized staff only</li>
    <li><strong>Account Responsibility:</strong> Keep credentials secure and confidential</li>
    <li><strong>Content Use:</strong> No redistribution without permission</li>
    <li><strong>Termination:</strong> ITSBO may revoke access for violations</li>
</ul>

      </div>
    </div>
  </div>
</div>

<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="privacyLabel">Privacy Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>🔒 ITSBO Gateway – Privacy Policy</h6>
<p>We value your privacy. Here's what you need to know:</p>
<ul>
    <li><strong>Information Collected:</strong> Name, student ID, email, system activity, device info</li>
    <li><strong>Usage:</strong> Authentication, communication, improvement of services</li>
    <li><strong>Sharing:</strong> Only with consent or legal obligation</li>
    <li><strong>Security:</strong> Protected with technical safeguards</li>
    <li><strong>Retention:</strong> Stored as needed for operations</li>
</ul>
<p><strong>Contact:</strong> itsbo@gmail.com | ITSBO Office, IBA College of Mindanao</p>

      </div>
    </div>
  </div>
</div>

    <!-- Load jQuery FIRST, then other scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {!! NoCaptcha::renderJs() !!}

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
    
    <script>
        // Login form loader
        document.querySelector('.login-form').addEventListener('submit', function() {
            document.getElementById('loaderOverlay').style.display = 'flex';
        });

        // Forgot Password functionality
        $(document).ready(function() {
            console.log('jQuery loaded and ready'); // Debug log
            
            $('.forgot-password-link').on('click', function(e) {
                e.preventDefault();
                console.log('Forgot password link clicked'); // Debug log
                
                Swal.fire({
                    title: 'Forgot Password?',
                    text: 'The system will send an automated 8-character password (mixed of Uppercase, Lowercase, Numbers and Symbols) to your registered email address.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Send Password!',
                    cancelButtonText: 'Cancel',
                    input: 'email',
                    inputPlaceholder: 'Enter your registered email address',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please enter your email address!';
                        }
                        // Basic email validation
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            return 'Please enter a valid email address!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const email = result.value;
                        console.log('Email entered:', email); // Debug log
                        
                        // Show loading
                        Swal.fire({
                            title: 'Sending...',
                            text: 'Please wait while we send your new password.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // AJAX request to send new password
                        $.ajax({
                            url: '/student/forgot-password',
                            type: 'POST',
                            data: {
                                email: email,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Success response:', response); // Debug log
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Password Sent!',
                                    text: 'A new password has been sent to your email address. Please check your inbox and spam folder.',
                                    confirmButtonColor: '#28a745'
                                });
                            },
                            error: function(xhr) {
                                console.log('Error response:', xhr); // Debug log
                                let errorMessage = 'Something went wrong. Please try again.';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>