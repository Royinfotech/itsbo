<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registration Success - ITSBO</title>
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: url('{{ asset("assets/pictures/LoginBackg.png") }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
        }

        /* Main Container */
        .success-container {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-width: 700px;
            width: 100%;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease forwards;
            backdrop-filter: blur(10px);
        }

        /* Success Icon */
        .success-icon {
            margin: 0 auto 30px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #006400, #228B22);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 100, 0, 0.3);
            animation: popIn 1s ease forwards 0.3s;
            transform: scale(0);
        }

        .success-icon i {
            color: white;
            font-size: 50px;
            animation: checkmark 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards 0.8s;
            opacity: 0;
            transform: scale(0);
        }

        /* Typography */
        .success-container h1 {
            margin-bottom: 20px;
            font-size: 2.5rem;
            color: #333;
            font-weight: 700;
            position: relative;
            animation: slideInDown 0.8s ease forwards 0.5s;
            opacity: 0;
        }

        .success-container h1::before,
        .success-container h1::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #006400, #800000);
            border-radius: 2px;
        }

        .success-container h1::before {
            left: -80px;
            transform: translateY(-50%);
        }

        .success-container h1::after {
            right: -80px;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #800000, #006400);
        }

        /* Message Box */
        .message-box {
            background: linear-gradient(135deg, rgba(0, 100, 0, 0.1), rgba(0, 100, 0, 0.05));
            padding: 20px;
            border-radius: 15px;
            margin: 30px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            border: 2px solid rgba(0, 100, 0, 0.2);
            animation: slideInLeft 0.8s ease forwards 0.7s;
            opacity: 0;
            transform: translateX(-30px);
        }

        .message-box i {
            font-size: 24px;
            color: #006400;
        }

        .message-box p {
            margin: 0;
            font-size: 1.1rem;
            color: #333;
        }

        /* Info Section */
        .info-section {
            margin: 40px 0;
            text-align: left;
            animation: slideInRight 0.8s ease forwards 0.9s;
            opacity: 0;
            transform: translateX(30px);
        }

        .info-section h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }

        .step-box {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
            background: linear-gradient(135deg, rgba(128, 0, 0, 0.05), rgba(128, 0, 0, 0.02));
            border-radius: 15px;
            border-left: 4px solid #800000;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .step::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(128, 0, 0, 0.1), transparent);
            transition: width 0.3s ease;
        }

        .step:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(128, 0, 0, 0.15);
        }

        .step:hover::before {
            width: 100%;
        }

        .step i {
            font-size: 28px;
            color: #800000;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .step p {
            margin: 0;
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }

        /* Contact Info */
        .contact-info {
            margin: 40px 0;
            padding: 25px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.03), rgba(0, 0, 0, 0.01));
            border-radius: 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease forwards 1.1s;
            opacity: 0;
        }

        .contact-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.4rem;
            text-align: center;
        }

        .contact-info > p {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
        }

        .contact-details {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .contact-details p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            padding: 10px 15px;
            background: rgba(0, 100, 0, 0.05);
            border-radius: 10px;
            font-weight: 500;
        }

        .contact-details i {
            color: #006400;
            font-size: 18px;
        }

        /* Button */
        .login-button {
            background: linear-gradient(135deg, #006400, #800000);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-top: 20px;
            animation: bounceIn 0.8s ease forwards 1.3s;
            opacity: 0;
            transform: scale(0.8);
        }

        .login-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 3s infinite;
        }

        .login-button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .login-button:active {
            transform: translateY(-1px) scale(1.02);
        }

        .login-button i {
            margin-right: 10px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes checkmark {
            0% { opacity: 0; transform: scale(0) rotate(-45deg); }
            50% { opacity: 1; transform: scale(1.3) rotate(-45deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.1); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        @keyframes shine {
            0% { left: -100%; }
            20% { left: 100%; }
            100% { left: 100%; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .success-container {
                padding: 30px 20px;
                margin: 10px;
                border-radius: 15px;
            }

            .success-container h1 {
                font-size: 2rem;
            }

            .success-container h1::before,
            .success-container h1::after {
                display: none;
            }

            .success-icon {
                width: 80px;
                height: 80px;
            }

            .success-icon i {
                font-size: 40px;
            }

            .step {
                padding: 15px;
                gap: 15px;
            }

            .step i {
                font-size: 24px;
            }

            .contact-details {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }

            .login-button {
                padding: 12px 30px;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 20px 15px;
            }

            .success-container h1 {
                font-size: 1.8rem;
            }

            .message-box {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .step {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Welcome to ITSBO Gateway!</h1>
        
        <div class="message-box">
            <i class="fas fa-envelope"></i>
            <p>Registration approval will be sent to <strong>{{ $email }}</strong></p>
        </div>
        
        <div class="info-section">
            <h2>Steps for Approval</h2>
            <div class="step-box">
                <div class="step">
                    <i class="fas fa-envelope-open-text"></i>
                    <p><strong>Step 1:</strong> Check your email inbox for registration approval. If you don't receive confirmation within 48 hours, proceed to step 2.</p>
                </div>
                <div class="step">
                    <i class="fas fa-building"></i>
                    <p><strong>Step 2:</strong> Visit the ITSBO Office and approach the ITSBO Secretary to approve your registration to ITSBO Gateway.</p>
                </div>
                <div class="step">
                    <i class="fas fa-sign-in-alt"></i>
                    <p><strong>Step 3:</strong> Once approved, you can login to access your account and start using the gateway.</p>
                </div>
            </div>
        </div>

        <div class="contact-info">
            <h3>Need Help?</h3>
            <p>If you don't receive an email within 48 hours, please contact us:</p>
            <div class="contact-details">
                <p><i class="fas fa-envelope"></i> support@itsbo.edu.ph</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
            </div>
        </div>

        <a href="{{ route('student.login') }}" class="login-button">
            <i class="fas fa-sign-in-alt"></i>Go to Login
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced SweetAlert with better styling
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful!',
                text: 'Your account has been created successfully. Please check your email for approval.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: false,
                position: 'center',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                customClass: {
                    popup: 'colored-toast'
                }
            });
        });

        // Add some interactive effects
        document.querySelectorAll('.step').forEach((step, index) => {
            step.style.animationDelay = `${1.5 + index * 0.2}s`;
            step.style.animation = 'slideInLeft 0.6s ease forwards';
            step.style.opacity = '0';
            step.style.transform = 'translateX(-30px)';
        });
    </script>

    <style>
        /* Additional SweetAlert customization */
        .colored-toast.swal2-icon-success {
            background-color: #ffffff !important;
        }
    </style>
</body>
</html>