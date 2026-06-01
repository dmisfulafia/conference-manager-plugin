<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
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
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 15px;
            text-align: left;
        }
        .intro-text {
            font-size: 15px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: left;
        }
        .otp-box {
            background-color: #f7fafc;
            border: 2px dashed #9d7126;
            border-radius: 8px;
            padding: 20px;
            display: inline-block;
            margin: 10px auto 30px auto;
            min-width: 200px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            color: #004d26;
            letter-spacing: 8px;
            font-family: 'Courier New', Courier, monospace;
        }
        .form-text {
            font-size: 12px;
            color: #a0aec0;
            margin-top: 5px;
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
                <p>Conference Management Portal</p>
            </div>
            
            <!-- Core Email Content -->
            <div class="content">
                <div class="greeting">Hello {{ $user->title }} {{ $user->first_name }} {{ $user->last_name }},</div>
                <div class="intro-text">
                    Welcome to the Federal University of Lafia Conference Management Portal! To complete your registration and activate your account, please verify your email address using the one-time password (OTP) code below:
                </div>
                
                <!-- OTP Display Card -->
                <div class="otp-box">
                    <div class="otp-code">{{ $code }}</div>
                    <div class="form-text">This code will expire in 60 minutes.</div>
                </div>
                
                <p class="intro-text" style="font-size: 14px; color: #718096;">
                    If you did not initiate this registration request, please disregard this email or contact support.
                </p>
            </div>
            
            <!-- Institutional Footer -->
            <div class="footer">
                <p>This is an automated security verification generated securely by the <strong>Federal University of Lafia</strong>.</p>
                <p>&copy; {{ date('Y') }} Federal University of Lafia. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
