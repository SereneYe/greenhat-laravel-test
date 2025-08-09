<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Enrollment Confirmation - {{ $companyName }}</title>
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
        .course-box {
            background-color: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        .course-box h3 {
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .course-box h3:before {
            content: "🎓";
            margin-right: 10px;
            font-size: 20px;
        }
        .course-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .course-description {
            color: #4a5568;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .categories {
            margin: 15px 0;
        }
        .category-tag {
            display: inline-block;
            background-color: #667eea;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 5px;
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
        .dashboard-button {
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
        .dashboard-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .enrollment-info {
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        .enrollment-info h4 {
            color: #2f855a;
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .enrollment-info h4:before {
            content: "✅";
            margin-right: 10px;
        }
        .enrollment-info p {
            color: #2d3748;
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
            <h1>Course Enrollment Confirmed!</h1>
            <p>You're successfully enrolled in your new course</p>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $firstName }}! 🎉
            </div>

            <div class="message">
                Congratulations! You have successfully enrolled in <strong>{{ $courseTitle }}</strong>. We're excited to support your learning journey at <strong>{{ $companyName }}</strong>.
            </div>

            <!-- Course Information -->
            <div class="course-box">
                <h3>Course Details</h3>

                <div class="course-title">{{ $courseTitle }}</div>

                @if($courseDescription)
                <div class="course-description">{{ $courseDescription }}</div>
                @endif

                @if($courseCategories && $courseCategories->count() > 0)
                <div class="categories">
                    @foreach($courseCategories as $category)
                        <span class="category-tag">{{ $category->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Course Info Grid -->
            <div class="info-grid">
                <div class="info-item">
                    <h4>👨‍🏫 Instructor</h4>
                    <p>{{ $courseInstructor ?: 'TBA' }}</p>
                </div>

                <div class="info-item">
                    <h4>⏱️ Duration</h4>
                    <p>{{ $courseDuration }} {{ $courseDuration == 1 ? 'hour' : 'hours' }}</p>
                </div>

                <div class="info-item">
                    <h4>💰 Price</h4>
                    <p>${{ number_format($coursePrice, 2) }}</p>
                </div>

                <div class="info-item">
                    <h4>📊 Level</h4>
                    <p>{{ ucfirst($courseLevel) }}</p>
                </div>
            </div>

            <!-- Enrollment Success Notice -->
            <div class="enrollment-info">
                <h4>Enrollment Successful</h4>
                <p>
                    <strong>Enrolled on:</strong> {{ $enrolledAt }}<br>
                    <strong>Student:</strong> {{ $employeeName }} ({{ $employeeEmail }})<br>
                    You can now access this course from your dashboard and begin your learning journey.
                </p>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $dashboardUrl }}" class="dashboard-button">
                    Access Your Dashboard
                </a>
            </div>

            <div class="message">
                If you have any questions about your enrollment or need assistance, please don't hesitate to reach out to our support team.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <h3>{{ $companyName }}</h3>
            <p>Thank you for choosing us for your professional development.</p>
            <p>This is an automated confirmation email for your course enrollment.</p>
            <p>
                <a href="{{ $dashboardUrl }}">Dashboard</a> |
                <a href="mailto:support@greenhat.com">Support</a>
            </p>
        </div>
    </div>
</body>
</html>
