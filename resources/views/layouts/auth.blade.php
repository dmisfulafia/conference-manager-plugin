<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FULafia Conference Manager</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --fulafia-gold: #9d7126;
            --fulafia-gold-hover: #835c1c;
            --fulafia-gold-light: #f7eecd;
            --academic-dark: #111827;
            --academic-green: #1a472a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: var(--academic-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient subtle gold background gradients */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(157, 113, 38, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            top: -100px;
            right: -100px;
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 71, 42, 0.06) 0%, rgba(255, 255, 255, 0) 70%);
            bottom: -150px;
            left: -150px;
            z-index: -1;
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        .card-auth {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            background: #ffffff;
            border: 1px solid rgba(157, 113, 38, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 550px;
            margin: 20px auto;
        }

        .card-auth-header {
            background-color: var(--academic-green);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 4px solid var(--fulafia-gold);
            position: relative;
        }

        .card-auth-header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 12px;
            background: white;
            padding: 5px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn-fulafia {
            background-color: var(--fulafia-gold);
            border-color: var(--fulafia-gold);
            color: #ffffff;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .btn-fulafia:hover, .btn-fulafia:focus {
            background-color: var(--fulafia-gold-hover);
            border-color: var(--fulafia-gold-hover);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(157, 113, 38, 0.25);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--fulafia-gold);
            box-shadow: 0 0 0 0.25rem rgba(157, 113, 38, 0.15);
        }

        .text-fulafia-gold {
            color: var(--fulafia-gold);
        }

        .text-academic-green {
            color: var(--academic-green);
        }

        .bg-fulafia-light {
            background-color: var(--fulafia-gold-light);
        }

        .auth-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>
</head>
<body>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card card-auth">
                    <div class="card-auth-header">
                        <div class="heading-font fs-3 fw-bold">FEDERAL UNIVERSITY OF LAFIA</div>
                        <div class="fs-6 opacity-75">Conference Management Portal</div>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        @yield('content')
                    </div>
                </div>
                <div class="auth-footer">
                    &copy; {{ date('Y') }} Federal University of Lafia. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS with Popper via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
