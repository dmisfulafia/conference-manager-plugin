<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Paper Submission Received</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 77, 38, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #004d26 0%, #003319 100%);
            padding: 35px 40px;
            text-align: center;
            border-bottom: 4px solid #9d7126; /* FULafia Gold Accent */
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .header p {
            color: #f7eecd;
            margin: 5px 0 0 0;
            font-size: 14px;
            font-weight: 500;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 15px;
        }
        .intro-text {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .details-card {
            background-color: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid #9d7126;
            padding: 24px;
            margin-bottom: 30px;
        }
        .details-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #004d26;
            font-size: 16px;
            font-weight: 700;
        }
        .detail-row {
            margin-bottom: 15px;
            font-size: 14px;
        }
        .detail-label {
            font-weight: 600;
            color: #718096;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #2d3748;
            font-weight: 700;
        }
        .cta-button {
            display: block;
            text-align: center;
            background: linear-gradient(135deg, #004d26 0%, #003319 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            font-weight: 700;
            border-radius: 8px;
            margin: 30px 0;
            font-size: 15px;
            box-shadow: 0 4px 6px rgba(0, 77, 38, 0.15);
        }
        .footer {
            background-color: #f7fafc;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #edf2f7;
            font-size: 12px;
            color: #a0aec0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header section with branding -->
            <div class="header">
                <h1>FEDERAL UNIVERSITY OF LAFIA</h1>
                <p>Conference Full Paper Submission</p>
            </div>
            
            <!-- Core Email Content -->
            <div class="content">
                <div class="greeting">Hello {{ $user->title }} {{ $user->first_name }} {{ $user->last_name }},</div>
                <div class="intro-text">
                    Thank you for submitting your full paper manuscript for the upcoming conference. We have successfully received your file and it has been routed to our reviewers for evaluation.
                </div>
                
                <!-- Submission Details Card -->
                <div class="details-card">
                    <h3>Submission Summary</h3>
                    <div class="detail-row">
                        <div class="detail-label">Conference</div>
                        <div class="detail-value" style="color: #004d26;">{{ $conference->title }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Paper Title</div>
                        <div class="detail-value" style="font-style: italic;">"{{ $submission->title }}"</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Current Status</div>
                        <div class="detail-value">
                            <span style="background-color: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 12px; text-transform: uppercase;">
                                Under Review
                            </span>
                        </div>
                    </div>
                </div>
                
                <p class="intro-text">
                    <strong>What's next?</strong> Our academic committee will evaluate your complete full paper. You will receive an email update as soon as the review panel has finalized their report.
                </p>
                
                <a href="{{ route('dashboard') }}" class="cta-button">Go to Member Dashboard</a>
            </div>
            
            <!-- Institutional Footer -->
            <div class="footer">
                <p>This is an automated notification generated securely by the <strong>Federal University of Lafia</strong>.</p>
                <p>&copy; {{ date('Y') }} Federal University of Lafia. All rights reserved.<br>
                Lafia-Akwanga Road, Lafia, Nasarawa State, Nigeria.</p>
            </div>
        </div>
    </div>
</body>
</html>
