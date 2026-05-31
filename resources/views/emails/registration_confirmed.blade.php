<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Payment Confirmed</title>
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
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .detail-label {
            display: table-cell;
            font-weight: 600;
            color: #718096;
            width: 40%;
        }
        .detail-value {
            display: table-cell;
            color: #2d3748;
            font-weight: 700;
            text-align: right;
        }
        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 15px 0;
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
        .footer strong {
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header section with branding -->
            <div class="header">
                <h1>FEDERAL UNIVERSITY OF LAFIA</h1>
                <p>Official Conference Management Portal</p>
            </div>
            
            <!-- Core Email Content -->
            <div class="content">
                <div class="greeting">Hello {{ $user->title }} {{ $user->first_name }} {{ $user->last_name }},</div>
                <div class="intro-text">
                    Congratulations! Your registration payment for the upcoming academic conference has been successfully verified. You have officially secured your seat.
                </div>
                
                <!-- Transaction Summary Card -->
                <div class="details-card">
                    <h3>Receipt Details</h3>
                    <div class="detail-row">
                        <div class="detail-label">Conference</div>
                        <div class="detail-value" style="color: #004d26;">{{ $conference->title }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Attendee Type</div>
                        <div class="detail-value">{{ $registration->attendeeType->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Ref ID</div>
                        <div class="detail-value" style="font-family: monospace;">{{ $payment->reference }}</div>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Accommodation</div>
                        <div class="detail-value">
                            {{ $registration->wants_accommodation ? 'Lodge Selected' : 'Not Requested' }}
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Conference Materials</div>
                        <div class="detail-value">
                            {{ $registration->wants_materials ? 'Included' : 'Not Included' }}
                        </div>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="detail-row" style="font-size: 16px;">
                        <div class="detail-label" style="color: #004d26;">Total Amount Paid</div>
                        <div class="detail-value" style="color: #004d26; font-size: 18px; font-weight: 800;">₦{{ number_format($payment->amount, 2) }}</div>
                    </div>
                </div>
                
                <p class="intro-text" style="margin-bottom: 10px;">
                    <strong>What's next?</strong> You are now authorized to submit your abstract proposals. Access your portal dashboard to manage your research submissions.
                </p>
                
                <a href="{{ route('dashboard') }}" class="cta-button">Go to Member Dashboard</a>
            </div>
            
            <!-- Institutional Footer -->
            <div class="footer">
                <p>This is an automated receipt generated securely by the <strong>Federal University of Lafia</strong>.</p>
                <p>&copy; {{ date('Y') }} Federal University of Lafia. All rights reserved.<br>
                Lafia-Akwanga Road, Lafia, Nasarawa State, Nigeria.</p>
            </div>
        </div>
    </div>
</body>
</html>
