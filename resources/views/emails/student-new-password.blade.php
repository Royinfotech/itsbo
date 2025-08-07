<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Student Portal</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.7;
            color: #1a1a1a;
            background: linear-gradient(135deg, #800020 0%, #a0002a 50%, #600015 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header {
            background: linear-gradient(135deg, #800020 0%, #a0002a 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .lock-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(10px);
        }
        
        .header h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #374151;
        }
        
        .greeting strong {
            color: #800020;
            font-weight: 600;
        }
        
        .intro-text {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .password-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .password-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #800020, #a0002a, #c41e3a);
        }
        
        .password-label {
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .password-display {
            background: white;
            border: 2px dashed #800020;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
        }
        
        .password-text {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #800020;
            letter-spacing: 3px;
            word-break: break-all;
            user-select: all;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .password-text:hover {
            color: #a0002a;
            transform: scale(1.05);
        }
        
        .copy-hint {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 10px;
            font-style: italic;
        }
        
        .security-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .security-notice-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .warning-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .security-notice h3 {
            color: #92400e;
            font-size: 16px;
            font-weight: 600;
        }
        
        .security-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .security-list li {
            color: #92400e;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }
        
        .security-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #f59e0b;
            font-weight: bold;
        }
        
        .login-section {
            text-align: center;
            margin: 40px 0 30px;
        }
        
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #800020 0%, #a0002a 100%);
            color: white;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(128, 0, 32, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(128, 0, 32, 0.4);
        }
        
        .help-text {
            font-size: 15px;
            color: #6b7280;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid #ef4444;
        }
        
        .help-text strong {
            color: #ef4444;
        }
        
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 8px;
        }
        
        .footer .brand {
            font-weight: 600;
            color: #800020;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e5e7eb 50%, transparent 100%);
            margin: 20px 0;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .email-wrapper {
                border-radius: 16px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .password-section {
                padding: 20px;
            }
            
            .password-text {
                font-size: 20px;
                letter-spacing: 2px;
            }
            
            .login-button {
                padding: 14px 30px;
                font-size: 14px;
            }
        }
        
        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
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
    </style>
</head>
<body>
    <div class="email-wrapper fade-in">
        <div class="header">
            <div class="header-content">
                <div class="lock-icon">🔐</div>
                <h1>Password Reset</h1>
                <p>ITSBO Gateway - Student Portal</p>
            </div>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $studentName }}</strong>,
            </div>
            
            <div class="intro-text">
                We've successfully generated a new temporary password for your student portal account. Please use the credentials below to access your account.
            </div>
            
            <div class="password-section">
                <div class="password-label">Your New Temporary Password</div>
                <div class="password-display">
                    <div class="password-text" onclick="selectPassword(this)">{{ $newPassword }}</div>
                    <div class="copy-hint">Click to select and copy</div>
                </div>
            </div>
            
            <div class="security-notice">
                <div class="security-notice-header">
                    <span class="warning-icon">⚠️</span>
                    <h3>Important Security Requirements</h3>
                </div>
                <ul class="security-list">
                    <li>Change this password immediately after your first login</li>
                    <li>Never share your password with anyone</li>
                    <li>Use a strong, unique password for better security</li>
                    <li>Log out completely when using shared computers</li>
                </ul>
            </div>
            
            <div class="login-section">
                <a href="{{ $loginUrl }}" class="login-button">Access ITSBO Gateway</a>
            </div>
            
            <div class="divider"></div>
            
            <div class="help-text">
                <strong>Didn't request this password reset?</strong> Please contact our support team immediately to secure your account.
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated security notification.</p>
            <p>Please do not reply to this email.</p>
            <div class="divider"></div>
            <p class="brand">ITSBO Gateway - Student Portal System</p>
        </div>
    </div>
    
    <script>
        function selectPassword(element) {
            if (window.getSelection) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(element);
                selection.removeAllRanges();
                selection.addRange(range);
                
                // Try to copy to clipboard
                try {
                    document.execCommand('copy');
                    element.style.background = '#dcfce7';
                    element.style.color = '#16a34a';
                    setTimeout(() => {
                        element.style.background = '';
                        element.style.color = '#800020';
                    }, 1000);
                } catch (err) {
                    console.log('Could not copy to clipboard');
                }
            }
        }
        
        // Add subtle animations on load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.password-section, .security-notice, .login-section');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });
    </script>
</body>
</html>