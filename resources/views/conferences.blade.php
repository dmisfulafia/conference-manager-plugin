@extends('layouts.member')

@section('title', 'Browse Conferences')
@section('page_title', 'Conferences Catalog')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 rounded-3 bg-white" style="border: 1px solid rgba(157, 113, 38, 0.08) !important; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h3 class="heading-font fw-extrabold text-academic-green mb-1">Academic Events & Conferences</h3>
                        <p class="text-muted mb-0 small">Browse, filter, and register for active ongoing academic sessions at the Federal University of Lafia.</p>
                    </div>
                    <div>
                        <ul class="nav nav-pills" id="conferenceTabs" role="tablist" style="background: rgba(0,0,0,0.04); padding: 5px; border-radius: 50px;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill px-4 py-2 small fw-bold" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing-pane" type="button" role="tab" aria-controls="ongoing-pane" aria-selected="true" style="transition: all 0.2s;">
                                    <i class="bi bi-calendar-check-fill me-1"></i> Ongoing
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-2 small fw-bold" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-pane" type="button" role="tab" aria-controls="past-pane" aria-selected="false" style="transition: all 0.2s;">
                                    <i class="bi bi-calendar-x-fill me-1"></i> Past Events
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Content -->
    <div class="tab-content" id="conferenceTabsContent">
        <!-- Ongoing Pane -->
        <div class="tab-pane fade show active" id="ongoing-pane" role="tabpanel" aria-labelledby="ongoing-tab" tabindex="0">
            <div class="row g-4">
                @if($ongoingConferences->isEmpty())
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-5 text-center text-muted bg-white rounded-3">
                            <i class="bi bi-calendar-x" style="font-size: 4rem; color: rgba(157, 113, 38, 0.3);"></i>
                            <h4 class="mt-3 fw-bold text-dark">No Active Conferences</h4>
                            <p class="fs-6 text-muted">There are currently no ongoing or upcoming academic conferences configured. Please check back later.</p>
                        </div>
                    </div>
                @else
                    @foreach($ongoingConferences as $conf)
                        @php
                            $userRegistration = $myRegistrations->where('conference_id', $conf->id)->first();
                        @endphp
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm p-4 rounded-3 h-100 bg-white d-flex flex-column justify-content-between" style="border: 1px solid rgba(157, 113, 38, 0.08) !important; transition: transform 0.2s; position: relative;">
                                @if($userRegistration)
                                    <div class="position-absolute top-0 end-0 m-3">
                                        @if($userRegistration->is_attendance_paid)
                                            <span class="badge bg-success px-3 py-2 rounded-pill fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Registered & Paid</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold"><i class="bi bi-clock-history me-1"></i> Registered (Pending Pay)</span>
                                        @endif
                                    </div>
                                @endif

                                <div>
                                    <h4 class="heading-font fw-extrabold text-academic-green mb-3 pe-5">{{ $conf->title }}</h4>
                                    <p class="text-muted small mb-4" style="line-height: 1.6;">{{ $conf->description }}</p>
                                    
                                    <div class="bg-light p-3 rounded-3 mb-4 border small">
                                        <div class="mb-2 text-dark"><i class="bi bi-geo-alt-fill text-fulafia-gold me-2"></i><strong>Venue:</strong> {{ $conf->venue }}</div>
                                        <div class="mb-2 text-dark"><i class="bi bi-calendar3 text-fulafia-gold me-2"></i><strong>Dates:</strong> {{ $conf->start_date->format('M d, Y') }} - {{ $conf->end_date->format('M d, Y') }}</div>
                                        <div class="mb-0 text-dark"><i class="bi bi-house-door-fill text-fulafia-gold me-2"></i><strong>Accommodation Rate:</strong> ₦{{ number_format($conf->accommodation_fee, 2) }} / night</div>
                                    </div>
                                </div>

                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    @php
                                        $activeTypes = $conf->attendeeTypes->where('fee', '>', 0);
                                    @endphp
                                    <div class="text-muted small">
                                        @if($activeTypes->isNotEmpty())
                                            Fees starting from <strong class="text-academic-green">₦{{ number_format($activeTypes->min('fee'), 2) }}</strong>
                                        @else
                                            Registration pricing pending
                                        @endif
                                    </div>

                                    @if($userRegistration)
                                        @if($userRegistration->is_attendance_paid)
                                            <a href="{{ route('dashboard') }}#submissions-section" class="btn btn-academic rounded-pill px-4 py-2 shadow-sm fw-bold">
                                                <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Manage Submissions
                                            </a>
                                        @else
                                            <a href="{{ route('dashboard') }}" class="btn btn-gold rounded-pill px-4 py-2 shadow-sm fw-bold">
                                                <i class="bi bi-wallet2 me-1"></i> Complete Checkout
                                            </a>
                                        @endif
                                    @else
                                        @if($activeTypes->isNotEmpty())
                                            <button class="btn btn-gold rounded-pill px-4 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal{{ $conf->id }}">
                                                <i class="bi bi-bookmark-plus-fill me-1"></i> Register & Pay
                                            </button>
                                        @else
                                            <button class="btn btn-secondary rounded-pill px-4 py-2 fw-bold" disabled>Upcoming</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($activeTypes->isNotEmpty() && !$userRegistration)
                        <!-- Registration Form Modal -->
                        <div class="modal fade" id="registerModal{{ $conf->id }}" tabindex="-1" aria-labelledby="registerModalLabel{{ $conf->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                    <div class="modal-header border-bottom-0 pb-0" style="background-color: var(--academic-green); color: white; border-top-left-radius: 16px; border-top-right-radius: 16px; padding: 20px;">
                                        <h5 class="modal-title heading-font fw-bold text-white" id="registerModalLabel{{ $conf->id }}">Conference Registration Form</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('payment.checkout') }}" method="POST" class="p-4 registration-form" data-conf-id="{{ $conf->id }}">
                                        @csrf
                                        <input type="hidden" name="conference_id" value="{{ $conf->id }}">
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark mb-2">1. Select Your Attendee Category</h6>
                                            <p class="text-muted small">Please select the appropriate category you belong to. Note that some categories may require official ID verification upon arrival.</p>
                                            
                                            @foreach($activeTypes as $type)
                                                <div class="form-check p-3 mb-2 border rounded-3 attendee-category-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                    <input class="form-check-input attendee-radio" type="radio" name="attendee_type_id" id="type_{{ $type->id }}" value="{{ $type->id }}" data-fee="{{ $type->fee }}" required>
                                                    <label class="form-check-label fw-bold d-block text-dark" for="type_{{ $type->id }}" style="cursor: pointer;">
                                                        {{ $type->name }} <span class="float-end text-fulafia-gold">₦{{ number_format($type->fee, 2) }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark mb-3">2. Additional Accommodation & Material Add-ons</h6>
                                            
                                            <div class="form-check p-3 mb-2 border rounded-3 wants-accommodation-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                <input class="form-check-input accommodation-checkbox" type="checkbox" name="wants_accommodation" id="accommodation_{{ $conf->id }}" value="1" data-fee="{{ $conf->accommodation_fee }}">
                                                <label class="form-check-label fw-bold d-block text-dark" for="accommodation_{{ $conf->id }}" style="cursor: pointer;">
                                                    Request University Hostel/Guest Lodge Accommodation <span class="float-end text-muted font-monospace">+₦{{ number_format($conf->accommodation_fee, 2) }}</span>
                                                </label>
                                            </div>

                                            <div class="form-check p-3 mb-2 border rounded-3 wants-materials-row" style="cursor: pointer; transition: background-color 0.2s;">
                                                <input class="form-check-input materials-checkbox" type="checkbox" name="wants_materials" id="materials_{{ $conf->id }}" value="1" data-fee="{{ $conf->conference_material_fee }}">
                                                <label class="form-check-label fw-bold d-block text-dark" for="materials_{{ $conf->id }}" style="cursor: pointer;">
                                                    Purchase Conference Materials Bag & Programs Pack <span class="float-end text-muted font-monospace">+₦{{ number_format($conf->conference_material_fee, 2) }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-light rounded-3 mb-4 border" style="border-style: dashed !important;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold text-dark">Calculated Payment Total:</div>
                                                    <div class="text-muted small">Inclusive of baseline attendee registration & selected add-ons.</div>
                                                </div>
                                                <span class="fs-3 fw-extrabold text-academic-green total-display">₦0.00</span>
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-academic btn-lg py-3 shadow-sm" style="border-radius: 12px;">
                                                <i class="bi bi-wallet2 me-2"></i> Proceed to Checkout
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Past Pane -->
        <div class="tab-pane fade" id="past-pane" role="tabpanel" aria-labelledby="past-tab" tabindex="0">
            <div class="row g-4">
                @if($pastConferences->isEmpty())
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-5 text-center text-muted bg-white rounded-3">
                            <i class="bi bi-calendar-range" style="font-size: 4rem; color: rgba(0,0,0,0.15);"></i>
                            <h4 class="mt-3 fw-bold text-dark">No Past Events</h4>
                            <p class="fs-6 text-muted">There are no records of previous academic events in this portal folder.</p>
                        </div>
                    </div>
                @else
                    @foreach($pastConferences as $conf)
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm p-4 rounded-3 h-100 bg-light opacity-80" style="border: 1px solid rgba(0,0,0,0.05) !important;">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary px-3 py-1 rounded-pill small">Concluded</span>
                                        <small class="text-muted">{{ $conf->start_date->format('M Y') }}</small>
                                    </div>
                                    <h4 class="heading-font fw-bold text-dark mb-3">{{ $conf->title }}</h4>
                                    <p class="text-muted small mb-3">{{ Str::limit($conf->description, 250) }}</p>
                                </div>

                                <div class="bg-white p-3 rounded-3 border small">
                                    <div class="mb-2 text-dark"><i class="bi bi-geo-alt-fill text-muted me-2"></i><strong>Venue:</strong> {{ $conf->venue }}</div>
                                    <div class="mb-0 text-dark"><i class="bi bi-calendar3 text-muted me-2"></i><strong>Dates:</strong> {{ $conf->start_date->format('M d, Y') }} - {{ $conf->end_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        #conferenceTabs .nav-link {
            color: #6b7280;
            background: transparent;
        }
        #conferenceTabs .nav-link.active {
            color: #ffffff !important;
            background-color: var(--academic-green) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .attendee-category-row:hover,
        .wants-accommodation-row:hover,
        .wants-materials-row:hover {
            background-color: #fafbfc;
        }
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
@endsection

@section('scripts')
    <!-- Dynamic Price Calculation Scripts -->
    <script>
        $(document).ready(function() {
            // Check for payment callback indicators in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payment_success')) {
                window.history.replaceState({}, document.title, window.location.pathname);
                $('<div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">' +
                    '<i class="bi bi-patch-check-fill me-2"></i> <strong>Congratulations!</strong> Your payment has been successfully processed and verified!' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                  '</div>').insertBefore('.main-content > div').first();
            } else if (urlParams.has('payment_error')) {
                window.history.replaceState({}, document.title, window.location.pathname);
                $('<div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">' +
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Payment Unsuccessful:</strong> The payment verification was unsuccessful or cancelled.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                  '</div>').insertBefore('.main-content > div').first();
            }
            // Function to recalculate registration price inside a specific modal/form
            function calculateTotal($form) {
                let total = 0.00;

                // 1. Get selected attendee type fee
                let $selectedRadio = $form.find('.attendee-radio:checked');
                if ($selectedRadio.length > 0) {
                    total += parseFloat($selectedRadio.data('fee')) || 0;
                }

                // 2. Add accommodation fee if checked
                let $accommodationCheck = $form.find('.accommodation-checkbox');
                if ($accommodationCheck.is(':checked')) {
                    total += parseFloat($accommodationCheck.data('fee')) || 0;
                }

                // 3. Add material fee if checked
                let $materialsCheck = $form.find('.materials-checkbox');
                if ($materialsCheck.is(':checked')) {
                    total += parseFloat($materialsCheck.data('fee')) || 0;
                }

                // Format total as Currency
                let formattedTotal = '₦' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $form.find('.total-display').text(formattedTotal);
            }

            // Bind triggers on modal radio and checkboxes changes
            $('.registration-form').on('change', '.attendee-radio, .accommodation-checkbox, .materials-checkbox', function() {
                let $form = $(this).closest('.registration-form');
                calculateTotal($form);
            });

            // Highlight selected attendee row
            $('.registration-form').on('click', '.attendee-category-row, .wants-accommodation-row, .wants-materials-row', function(e) {
                // Trigger natural input state
                let $input = $(this).find('input');
                if (e.target !== $input[0]) {
                    if ($input.attr('type') === 'radio') {
                        $input.prop('checked', true).trigger('change');
                    } else if ($input.attr('type') === 'checkbox') {
                        $input.prop('checked', !$input.is(':checked')).trigger('change');
                    }
                }
                
                // Toggle active style
                let $form = $(this).closest('.registration-form');
                $form.find('.attendee-category-row').removeClass('bg-warning-subtle border-warning');
                $form.find('.attendee-radio:checked').closest('.attendee-category-row').addClass('bg-warning-subtle border-warning');
                
                // Toggle checkboxes styles
                $form.find('.wants-accommodation-row').toggleClass('bg-success-subtle border-success', $form.find('.accommodation-checkbox').is(':checked'));
                $form.find('.wants-materials-row').toggleClass('bg-success-subtle border-success', $form.find('.materials-checkbox').is(':checked'));
            });

            // Credo Inline Payment Interceptor
            $(document).on('submit', 'form[action*="payment/checkout"]', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $form.find('button[type="submit"]');
                let originalText = $btn.html();
                
                // Show loading spinner
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading Checkout...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Close any open Bootstrap modal
                            let $modal = $form.closest('.modal');
                            if ($modal.length > 0) {
                                bootstrap.Modal.getInstance($modal[0]).hide();
                            }

                            // Open secure centered popup window pointing directly to the payment link
                            let width = 500;
                            let height = 700;
                            let left = (screen.width / 2) - (width / 2);
                            let top = (screen.height / 2) - (height / 2);
                            
                            let popup = window.open(response.payment_link, 'CredoCheckout', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',resizable=yes,scrollbars=yes,status=yes');
                            
                            $btn.html('<i class="bi bi-hourglass-split animate-pulse"></i> Awaiting Payment...');

                            // Track popup state
                            let timer = setInterval(function() {
                                if (popup.closed) {
                                    clearInterval(timer);
                                    window.location.reload();
                                }
                            }, 1500);
                        } else {
                            alert(response.message || "Unable to initiate payment popup. Please try again.");
                            $btn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        let errMsg = "An error occurred during checkout setup. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                let parsed = JSON.parse(xhr.responseText);
                                if (parsed.error) errMsg = parsed.error;
                            } catch(err) {}
                        }
                        alert(errMsg);
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection
