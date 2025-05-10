<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
        }
        .header {
            background-color: #2c3e50;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            font-size: 0.8em;
            color: #6c757d;
            border-radius: 0 0 5px 5px;
            margin: 20px -20px -20px;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin: 20px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
            letter-spacing: 2px;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Library Management System</h2>
        </div>
        
        <p>Dear {{ $userName }},</p>
        
        <p>We received a request to reset your password for your library account. To verify your identity, please use the OTP code below:</p>
        
        <div class="otp">{{ $otp }}</div>
        
        <p>This OTP will expire in 15 minutes. If you didn't request a password reset, please ignore this email or contact support if you have concerns.</p>
        
        <div class="warning">
            <strong>Security Notice:</strong> Never share your OTP with anyone, including library staff. Our team will never ask for your OTP.
        </div>
        
        <p>Thank you,<br>The Library Management Team</p>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} Library Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>