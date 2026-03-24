<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .verification-code {
            text-align: center;
            font-size: 32px;
            letter-spacing: 5px;
            font-weight: bold;
            margin: 30px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        
        <p>Hello {{ $first_name }} {{ $last_name }},</p>
        
        <p>Thank you for registering! To complete your registration, please use the verification code below to verify your email address:</p>
        
        <div class="verification-code">{{ $verification_code }}</div>
        
        <p>This code will expire in 60 minutes.</p>
        
        <p>If you did not create an account, no further action is required.</p>
        
        <p>Best regards,<br>DocTrack Team</p>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
