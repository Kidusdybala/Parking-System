<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
        }
        .code-container {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #3b82f6;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .warning {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🅿️ MikiPark</div>
            <h2>Password Reset Request</h2>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password for your MikiPark account. Use the verification code below to proceed with resetting your password:</p>
            
            <div class="code-container">
                <div class="code">{{ $code }}</div>
                <p style="margin-top: 10px; color: #6b7280;">Enter this code to reset your password</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This code will expire in 30 minutes for security reasons. If you didn't request a password reset, please ignore this email.
            </div>
            
            <p>If you have any questions or need assistance, please contact our support team.</p>
            
            <p>Best regards,<br>The MikiPark Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email, please do not reply.</p>
            <p>&copy; {{ date('Y') }} MikiPark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>