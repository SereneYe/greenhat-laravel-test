<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - {{ $companyName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6; color: #333; background-color: #f8fafc;
        }
        .email-wrapper { width: 100%; max-width: 600px; margin: 0 auto; background: #fff; }
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            padding: 40px 30px; text-align: center; color: white;
        }
        .header h1 { font-size: 28px; font-weight: 600; margin-bottom: 10px; }
        .header p { font-size: 16px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 24px; color: #2d3748; margin-bottom: 20px; font-weight: 500; }
        .message { font-size: 16px; color: #4a5568; margin-bottom: 30px; line-height: 1.7; }
        .code-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fecaca; border-radius: 12px;
            padding: 30px; margin: 30px 0; text-align: center;
        }
        .code-box h3 {
            color: #dc2626; font-size: 18px; margin-bottom: 20px;
            display: flex; align-items: center; justify-content: center;
        }
        .code-box h3:before { content: "🔐"; margin-right: 10px; font-size: 22px; }
        .verification-code {
            font-family: 'Courier New', monospace; background: #fff;
            padding: 20px 30px; border-radius: 8px; font-size: 32px;
            color: #dc2626; font-weight: bold; letter-spacing: 8px;
            border: 2px solid #ef4444; display: inline-block;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.1);
        }
        .code-instructions {
            margin-top: 20px; font-size: 14px; color: #6b7280;
            line-height: 1.5;
        }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0; }
        .info-item {
            background: #f8fafc; padding: 20px; border-radius: 8px;
            border-left: 4px solid #ef4444;
        }
        .info-item h4 {
            color: #2d3748; font-size: 14px; font-weight: 600; margin-bottom: 8px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .info-item p { color: #4a5568; font-size: 16px; }
        .reset-button {
            display: inline-block; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff; text-decoration: none; padding: 15px 30px; border-radius: 6px;
            font-weight: 600; font-size: 16px; text-align: center; margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .reset-button:hover { transform: translateY(-2px); }
        .security-notice {
            background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px;
            padding: 20px; margin: 30px 0;
        }
        .security-notice h4 {
            color: #d97706; font-size: 16px; margin-bottom: 10px;
            display: flex; align-items: center;
        }
        .security-notice h4:before { content: "⚠️"; margin-right: 10px; }
        .security-notice p { color: #92400e; font-size: 14px; line-height: 1.6; }
        .footer {
            background: #2d3748; color: #a0aec0; padding: 30px; text-align: center;
        }
        .footer h3 { color: #fff; margin-bottom: 15px; font-size: 18px; }
        .footer p { font-size: 14px; line-height: 1.6; margin-bottom: 10px; }
        .footer a { color: #ef4444; text-decoration: none; }
        .expiration-warning {
            background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px;
            margin: 20px 0; border-radius: 0 6px 6px 0;
        }
        .expiration-warning p {
            color: #991b1b; font-size: 14px; font-weight: 500;
            margin: 0;
        }
        @media (max-width: 600px) {
            .header, .content, .footer { padding: 20px; }
            .info-grid { grid-template-columns: 1fr; gap: 15px; }
            .greeting { font-size: 20px; }
            .verification-code { font-size: 28px; letter-spacing: 4px; padding: 15px 20px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Password Reset Request</h1>
            <p>We received a request to reset your password</p>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $firstName }}! 👋</div>

            <div class="message">
                We received a request to reset the password for your {{ $companyName }} account.
                To proceed with resetting your password, please use the verification code below.
            </div>

            <div class="code-box">
                <h3>Your Verification Code</h3>
                <div class="verification-code">{{ $verificationCode }}</div>
                <div class="code-instructions">
                    Enter this 6-digit code on the password reset page to continue.
                </div>
            </div>

            <div class="expiration-warning">
                <p>⏰ This verification code will expire in {{ $expirationTime }}. Please use it promptly.</p>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <h4>Account Email</h4>
                    <p>{{ $userEmail }}</p>
                </div>
                <div class="info-item">
                    <h4>Request Time</h4>
                    <p>{{ $sentAt }}</p>
                </div>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ $resetUrl }}" class="reset-button">🔑 Reset Your Password</a>
            </div>

            <div class="security-notice">
                <h4>Security Notice</h4>
                <p>
                    If you didn't request this password reset, please ignore this email and your password will remain unchanged.
                    For security reasons, never share this verification code with anyone.
                </p>
            </div>

            <div class="message">
                <strong>How to reset your password:</strong>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Click the "Reset Your Password" button above or visit <a href="{{ $resetUrl }}" style="color: #ef4444;">{{ $resetUrl }}</a></li>
                    <li>Enter the 6-digit verification code: <strong>{{ $verificationCode }}</strong></li>
                    <li>Create a new, secure password</li>
                    <li>Confirm your new password</li>
                    <li>Log in with your new credentials</li>
                </ol>
            </div>
        </div>

        <div class="footer">
            <h3>{{ $companyName }} Security Team</h3>
            <p>If you have any questions about this password reset request, please contact us immediately.</p>
            <p>Email: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.7;">
                This email was sent automatically for security purposes. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
