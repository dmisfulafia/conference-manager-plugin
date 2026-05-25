<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt_{{ $payment->reference }} - FULafia Conference Portal</title>
    <!-- Google Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --fulafia-gold: #9d7126;
            --academic-green: #1a472a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 40px 0;
        }

        .receipt-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: 0 auto;
            padding: 50px;
            border: 1px solid rgba(157, 113, 38, 0.12);
            position: relative;
            overflow: hidden;
        }

        .heading-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Official PAID Stamp styling */
        .paid-stamp {
            position: absolute;
            top: 40px;
            right: 50px;
            width: 140px;
            height: 140px;
            border: 4px double #059669;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #059669;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            transform: rotate(-12deg);
            opacity: 0.85;
            background-color: rgba(255,255,255,0.9);
            z-index: 10;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.05);
        }

        .paid-stamp .stamp-title {
            font-size: 0.65rem;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .paid-stamp .stamp-status {
            font-size: 1.5rem;
            line-height: 1;
            letter-spacing: 0.5px;
            font-weight: 800;
            border-top: 2px solid #059669;
            border-bottom: 2px solid #059669;
            padding: 2px 8px;
            margin: 2px 0;
        }

        .paid-stamp .stamp-date {
            font-size: 0.6rem;
            font-weight: 600;
        }

        .receipt-header {
            border-bottom: 3px solid var(--fulafia-gold);
            padding-bottom: 25px;
            margin-bottom: 35px;
        }

        .institution-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--academic-green);
            letter-spacing: 0.5px;
            margin: 0;
        }

        .portal-subtitle {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--fulafia-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--academic-green);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #111827;
        }

        .table-itemized th {
            font-family: 'Outfit', sans-serif;
            background-color: var(--academic-green);
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .table-itemized td {
            padding: 14px 12px;
            font-size: 0.9rem;
        }

        .table-itemized .total-row {
            background-color: #f9fafb;
            font-weight: 700;
            font-size: 1rem;
            border-top: 2px solid var(--fulafia-gold);
        }

        .receipt-footer {
            border-top: 1px dashed #d1d5db;
            margin-top: 40px;
            padding-top: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Action Buttons Area */
        .actions-wrapper {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-gold {
            background-color: var(--fulafia-gold);
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .btn-gold:hover {
            background-color: #835c1c;
            color: #ffffff;
        }

        /* Print Media Overrides */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                color: #000000;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                padding: 20px;
                max-width: 100%;
            }

            .actions-wrapper {
                display: none !important;
            }

            .paid-stamp {
                top: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Actions -->
    <div class="actions-wrapper">
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Payments
        </a>
        <button onclick="window.print()" class="btn btn-gold rounded-3 shadow-sm">
            <i class="bi bi-printer-fill me-2"></i> Print official Receipt
        </button>
    </div>

    <!-- Official Receipt Container -->
    <div class="receipt-container">
        
        <!-- Paid Official Stamp -->
        <div class="paid-stamp">
            <span class="stamp-title">FULafia Portal</span>
            <span class="stamp-status">★ PAID ★</span>
            <span class="stamp-date">{{ $payment->created_at->format('d-M-Y') }}</span>
        </div>

        <!-- Receipt Title & Branding -->
        <div class="receipt-header text-center text-md-start">
            <h2 class="institution-title heading-font">FEDERAL UNIVERSITY OF LAFIA</h2>
            <div class="portal-subtitle">CONFERENCE MANAGEMENT PORTAL</div>
            <div class="mt-3 text-muted small">Official Transaction Receipt & Invoice Confirmation</div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Invoice Meta Information -->
            <div class="col-md-6">
                <div class="section-title">Invoice Details</div>
                <div class="row g-2">
                    <div class="col-5 info-label">Receipt No:</div>
                    <div class="col-7 info-value text-fulafia-gold fw-bold">FUL-REC-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                    
                    <div class="col-5 info-label">Payment Ref:</div>
                    <div class="col-7 info-value font-monospace small fw-bold">{{ $payment->reference }}</div>
                    
                    <div class="col-5 info-label">Payment Date:</div>
                    <div class="col-7 info-value">{{ $payment->created_at->format('F d, Y h:i A') }}</div>
                    
                    <div class="col-5 info-label">Payment Status:</div>
                    <div class="col-7 info-value text-success fw-bold"><i class="bi bi-patch-check-fill me-1"></i> SUCCESSFUL</div>
                </div>
            </div>

            <!-- Payer details -->
            <div class="col-md-6">
                <div class="section-title">Payer Information</div>
                <div class="row g-2">
                    <div class="col-4 info-label">Name:</div>
                    <div class="col-8 info-value fw-bold">{{ $payment->user->title }} {{ $payment->user->name }}</div>
                    
                    <div class="col-4 info-label">Email:</div>
                    <div class="col-8 info-value">{{ $payment->user->email }}</div>
                    
                    <div class="col-4 info-label">Phone:</div>
                    <div class="col-8 info-value">{{ $payment->user->phone }}</div>
                    
                    <div class="col-4 info-label">Institution:</div>
                    <div class="col-8 info-value text-truncate" title="{{ $payment->user->institution }}">{{ $payment->user->institution ?? 'Federal University of Lafia' }}</div>
                </div>
            </div>
        </div>

        <!-- Conference details -->
        <div class="mb-4">
            <div class="section-title">Conference Information</div>
            <div class="bg-light p-3 rounded border">
                @if($payment->registration && $payment->registration->conference)
                    <h5 class="heading-font fw-bold text-academic-green mb-1">{{ $payment->registration->conference->title }}</h5>
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt-fill text-fulafia-gold me-1"></i> {{ $payment->registration->conference->venue }} 
                        <span class="mx-2">|</span> 
                        <i class="bi bi-calendar3 text-fulafia-gold me-1"></i> {{ $payment->registration->conference->start_date->format('M d, Y') }} - {{ $payment->registration->conference->end_date->format('M d, Y') }}
                    </div>
                @else
                    <h5 class="heading-font fw-bold text-academic-green mb-1">FULafia Portal Fee</h5>
                    <div class="small text-muted">Generic service fee on FULafia Conference Registry</div>
                @endif
            </div>
        </div>

        <!-- Itemized Table -->
        <div class="mb-4">
            <div class="section-title">Payment Summary Breakdown</div>
            <div class="table-responsive">
                <table class="table table-bordered table-itemized align-middle">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Description of Item / Fee Category</th>
                            <th class="text-end" style="width: 150px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $sn = 1; @endphp
                        @if($payment->registration && $payment->registration->attendeeType)
                            <!-- Baseline Fee Row -->
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td>
                                    <span class="fw-bold">Baseline Attendance Registration Fee</span>
                                    <div class="small text-muted mt-0.5">Attendee pricing tier: <strong>{{ $payment->registration->attendeeType->name }}</strong></div>
                                </td>
                                <td class="text-end fw-bold text-dark">₦{{ number_format($payment->registration->attendeeType->fee, 2) }}</td>
                            </tr>

                            <!-- Accommodation add-on if chosen -->
                            @if($payment->registration->wants_accommodation)
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td>
                                    <span class="fw-bold">Premium Accommodation Add-on Service</span>
                                    <div class="small text-muted mt-0.5">Assigned University Hostel / Guest lodge booking</div>
                                </td>
                                <td class="text-end fw-bold text-dark">₦{{ number_format($payment->registration->conference->accommodation_fee, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Material package add-on if chosen -->
                            @if($payment->registration->wants_materials)
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td>
                                    <span class="fw-bold">Conference Materials Bag & Program Pack</span>
                                    <div class="small text-muted mt-0.5">Official souvenir portfolio bag, ID name tag, print digests & journals</div>
                                </td>
                                <td class="text-end fw-bold text-dark">₦{{ number_format($payment->registration->conference->conference_material_fee, 2) }}</td>
                            </tr>
                            @endif
                        @else
                            <!-- Generic Single Purpose Row -->
                            <tr>
                                <td>{{ $sn++ }}</td>
                                <td class="text-capitalize fw-bold">{{ str_replace('_', ' ', $payment->purpose) }} Fee</td>
                                <td class="text-end fw-bold text-dark">₦{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endif

                        <!-- Grand Total -->
                        <tr class="total-row">
                            <td colspan="2" class="text-end text-uppercase">Grand Total Paid:</td>
                            <td class="text-end text-success fs-5">₦{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Receipt footer message -->
        <div class="receipt-footer">
            <p class="mb-1 fw-bold">Thank you for your payment!</p>
            <p class="mb-2">This is a system-generated electronic receipt issued from the Federal University of Lafia Conference Portal. No signature is required.</p>
            <div class="d-flex justify-content-center gap-4 mt-3 text-muted small">
                <span><i class="bi bi-globe me-1"></i> www.fulafia.edu.ng</span>
                <span><i class="bi bi-envelope me-1"></i> support@fulafia.edu.ng</span>
            </div>
        </div>

    </div>

</body>
</html>
