<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification - FULafia Conference Portal</title>
    <!-- Google Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .status-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 420px;
            width: 100%;
            padding: 40px;
            text-align: center;
            border: 1px solid rgba(157, 113, 38, 0.08);
        }
        .status-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            font-size: 2.2rem;
        }
        .status-success {
            background-color: #d1fae5;
            color: #059669;
        }
        .status-error {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body>

    <div class="status-card animate__animated animate__fadeIn">
        @if($status === 'success')
            <div class="status-icon status-success">
                <i class="bi bi-patch-check-fill animate__animated animate__scaleIn"></i>
            </div>
            <h3 class="heading-font fw-bold text-dark mb-2">Payment Successful!</h3>
            <p class="text-muted small mb-4">Your registration has been fully activated. We are redirecting you back to your member dashboard...</p>
            <div class="spinner-border text-success spinner-border-sm" role="status"></div>
        @else
            <div class="status-icon status-error">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h3 class="heading-font fw-bold text-dark mb-2">Verification Failed</h3>
            <p class="text-muted small mb-4">{{ $message ?? 'Unable to process transaction or payment was cancelled.' }}</p>
            <button onclick="closePopup()" class="btn btn-danger w-100 py-2.5 rounded-3 fw-bold">
                Close Window & Return
            </button>
        @endif
    </div>

    <script>
        function closePopup() {
            if (window.opener) {
                window.opener.location.href = "{{ route('dashboard') }}?payment_error=1";
                window.close();
            } else {
                window.location.href = "{{ route('dashboard') }}";
            }
        }

        // On successful processing, trigger automated redirect and closure
        @if($status === 'success')
            setTimeout(function() {
                if (window.opener) {
                    window.opener.location.href = "{{ route('dashboard') }}?payment_success=1";
                    window.close();
                } else {
                    window.location.href = "{{ route('dashboard') }}";
                }
            }, 3000);
        @endif
    </script>
</body>
</html>
