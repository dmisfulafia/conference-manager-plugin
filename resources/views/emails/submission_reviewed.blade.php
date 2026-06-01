<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Review Update</title>
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
        .status-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
        }
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-denied {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .details-card {
            background-color: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid #004d26;
            padding: 24px;
            margin-bottom: 30px;
        }
        .details-card.denied {
            border-left-color: #991b1b;
        }
        .details-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #004d26;
            font-size: 16px;
            font-weight: 700;
        }
        .details-card.denied h3 {
            color: #991b1b;
        }
        .detail-row {
            margin-bottom: 12px;
            font-size: 14px;
        }
        .detail-label {
            font-weight: 600;
            color: #718096;
            margin-bottom: 3px;
        }
        .detail-value {
            color: #2d3748;
            font-weight: 700;
        }
        .reason-box {
            background-color: #fff5f5;
            border: 1px dashed #feb2b2;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            color: #c53030;
            font-size: 14px;
            line-height: 1.5;
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
                <p>Submission Moderation Panel</p>
            </div>
            
            <!-- Core Email Content -->
            <div class="content">
                <div class="greeting">Hello {{ $user->title }} {{ $user->first_name }} {{ $user->last_name }},</div>
                <div class="intro-text">
                    Our academic review panel has completed the moderation of your recent submission. Please review the official decision details below.
                </div>
                
                <!-- Decision Badge -->
                <div style="text-align: center;">
                    @if($status === 'approved')
                        <div class="status-badge status-approved">✓ Submission Approved</div>
                    @else
                        <div class="status-badge status-denied">✗ Revision Required / Denied</div>
                    @endif
                </div>
                
                <!-- Submission Details Card -->
                <div class="details-card {{ $status === 'denied' ? 'denied' : '' }}">
                    <h3>Submission Review Details</h3>
                    <div class="detail-row">
                        <div class="detail-label">Conference</div>
                        <div class="detail-value" style="color: #004d26;">{{ $conference->title }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Asset Evaluated</div>
                        <div class="detail-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $type) }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Submission Title</div>
                        <div class="detail-value">"{{ $submission->title }}"</div>
                    </div>
                    
                    @if($status === 'denied' && $reason)
                        <div class="reason-box">
                            <strong>Reviewer Feedback & Remarks:</strong><br>
                            {{ $reason }}
                        </div>
                    @endif
                </div>
                
                <p class="intro-text">
                    @if($status === 'approved')
                        @if($type === 'abstract')
                            <strong>Next Step:</strong> Since your abstract has been officially approved, you are now authorized to pay the full paper fee (if applicable) and upload your complete, final manuscript paper.
                        @else
                            <strong>Congratulations!</strong> Your full paper has been accepted for publication/presentation. Please print your payment receipt and prepare for the conference schedule.
                        @endif
                    @else
                        <strong>Next Step:</strong> Please log in to your portal account, address the feedback/remarks noted above, and upload a revised copy of your submission for re-evaluation.
                    @endif
                </p>
                
                <a href="{{ route('dashboard') }}" class="cta-button">Go to Member Portal</a>
            </div>
            
            <!-- Institutional Footer -->
            <div class="footer">
                <p>This is an automated notification generated securely by the <strong>Federal University of Lafia</strong>.</p>
                <p>&copy; {{ date('Y') }} Federal University of Lafia. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
