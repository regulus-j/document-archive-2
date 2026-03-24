<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .verification-code {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            margin: 25px 0;
            color: #2563eb;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Document Archive System</h1>
    </div>

    <div class="content">
        <h2>Verification Code</h2>
        
        <p>Hello {{ $first_name }} {{ $last_name }},</p>
        
        <p>Thank you for using our Document Archive System. To complete your verification, please use the following code:</p>
        
        <div class="verification-code">{{ $verification_code }}</div>
        
        <p>This code will expire in 30 minutes for security reasons.</p>
        
        @if(isset($login_url))
        <p style="text-align: center;">
            <a href="{{ $login_url }}" class="button">Go to Login Page</a>
        </p>
        @endif
        
        <p>If you did not request this verification code, please ignore this email or contact support if you have concerns.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Document Archive System. All rights reserved.</p>
        <p>This is an automated message, please do not reply directly to this email.</p>
    </div>
</body>
</html>
