<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $companyName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8fafc;
        }
        .email-wrapper {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        .credentials-box {
            background-color: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .credentials-box h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .credentials-box h3:before {
            content: "🔑";
            margin-right: 10px;
            font-size: 20px;
        }
        .credential-item {
            margin: 15px 0;
            padding: 12px;
            background-color: #ffffff;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .credential-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: #f1f5f9;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 16px;
            color: #1a202c;
            word-break: break-all;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        .info-item {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .info-item h4 {
            color: #2d3748;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-item p {
            color: #4a5568;
            font-size: 16px;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .security-notice {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .security-notice h4 {
            color: #c53030;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .security-notice h4:before {
            content: "🔒";
            margin-right: 10px;
        }
        .security-notice p {
            color: #742a2a;
            font-size: 14px;
            line-height: 1.6;
        }
        .footer {
            background-color: #2d3748;
            color: #a0aec0;
            padding: 30px;
            text-align: center;
        }
        .footer h3 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .footer p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .header, .content, .footer {
                padding: 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .greeting {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>Welcome to {{ $companyName }}!</h1>
            <p>Your employee account has been successfully created</p>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $firstName }}! 👋
            </div>

            <div class="message">
                Congratulations! Your employee account at <strong>{{ $companyName }}</strong> has been successfully created. We're excited to have you join our team as a <strong>{{ $employeeRole }}</strong>.
            </div>

            <!-- Login Credentials -->
            <div class="credentials-box">
                <h3>Your Login Credentials</h3>
                <div class="credential-item">
                    <div class="credential-label">Email Address</div>
                    <div class="credential-value">{{ $userEmail }}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Temporary Password</div>
                    <div class="credential-value">{{ $generatedPassword }}</div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="info-grid">
                <div class="info-item">
                    <h4>Full Name</h4>
                    <p>{{ $userName }}</p>
                </div>
                <div class="info-item">
                    <h4>Registration Date</h4>
                    <p>{{ $registrationDate }}</p>
                </div>
            </div>

            <!-- Login Button -->
            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ $loginUrl }}" class="login-button">
                    🚀 Login to Your Account
                </a>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <h4>Important Security Notice</h4>
                <p>
                    For your security, please change your temporary password immediately after your first login.
                    Never share your login credentials with anyone, and always log out when using shared devices.
                </p>
            </div>

            <!-- Next Steps -->
            <div class="message">
                <strong>Next Steps:</strong>
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Click the login button above or visit <a href="{{ $loginUrl }}" style="color: #667eea;">{{ $loginUrl }}</a></li>
                    <li>Use your email and the temporary password provided above</li>
                    <li>Change your password to something secure and memorable</li>
                    <li>Complete your employee profile setup</li>
                    <li>Explore your dashboard and available features</li>
                </ol>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h3>{{ $companyName }} Team</h3>
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            <p>
                Email: <a href="mailto:support@greenhat.net">support@greenhat.net</a><br>
                Web: <a href="{{ url('/') }}">{{ url('/') }}</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.7;">
                This email was sent automatically. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
