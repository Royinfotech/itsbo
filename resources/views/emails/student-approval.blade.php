<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Approval Notification</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
</head>
<body style="margin: 0; font-family: 'Poppins', sans-serif; background-color: #f5f5f5; padding: 30px;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #f5f5f5; border-radius: 10px;">
        <tr>
            <td align="center">
                <!-- Content Wrapper -->
                <table width="550" cellpadding="0" cellspacing="0" style="
                    background-color: #ffffff;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
                ">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #800000; padding: 20px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">ITSBO Gateway</h1>
                            <div style="height: 3px; background-color: #ffc402; width: 60px; margin: 15px auto 0;"></div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #333;">Hello {{ $student->student_name }},</h2>

                            <p style="font-size: 16px; color: #555;">
                                We're thrilled to inform you that your registration with 
                                <strong style="color: #800000;">ITSBO Gateway</strong> has been 
                                <strong style="color: #ffc400;">approved</strong>!
                            </p>

                            <p style="font-size: 16px; color: #555;">
                                You can now access your account with the following credentials:
                            </p>

                            <ul style="font-size: 15px; color: #555; padding-left: 20px;">
                                <li><strong>Student ID:</strong> {{ $student->student_id }}</li>
                                <li><strong>Email:</strong> {{ $student->email }}</li>
                                <li><strong>Password:</strong> (the one you registered with)</li>
                            </ul>

                            <p style="margin-top: 50px; text-align: center;">
                                <a href="{{ route('student.login') }}" style="background-color: #800000; color: #fff; padding: 12px 50px; text-decoration: none; border-radius: 5px; font-weight: 600;">
                                    Login Now
                                </a>
                            </p>

                            <p style="margin-top: 30px; color: #444;">
                                We're excited to have you onboard. Let's achieve great things together.
                            </p>

                            <p style="color: #800000; margin-top: 20px;"><strong>— The ITSBO Gateway Team</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px; font-size: 12px; color: #aaa; text-align: center;">
                            &copy; {{ date('Y') }} ITSBO Gateway. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
