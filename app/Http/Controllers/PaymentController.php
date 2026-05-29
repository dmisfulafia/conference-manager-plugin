<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conference;
use App\Models\AttendeeType;
use App\Models\Registration;
use App\Models\Payment;
use App\Services\CredoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $credo;

    public function __construct(CredoService $credo)
    {
        $this->credo = $credo;
    }

    /**
     * Display a listing of the member's payments.
     */
    public function index()
    {
        $user = Auth::user();
        
        $totalPayments = $user->payments()
            ->where('status', 'successful')
            ->sum('amount');
            
        $payments = $user->payments()
            ->with(['registration.conference', 'registration.attendeeType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('payments.index', compact('payments', 'totalPayments'));
    }

    /**
     * Render the official printable receipt for a successful payment.
     */
    public function receipt(Payment $payment)
    {
        // Security check: Only the owner or an admin can access
        if ($payment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        if ($payment->status !== 'successful') {
            return redirect()->route('payments.index')->with('error', 'Receipts can only be generated for successful payments.');
        }

        // Load relations for receipt details
        $payment->load(['user', 'registration.conference', 'registration.attendeeType']);

        return view('receipt', compact('payment'));
    }

    /**
     * Initialize payment and redirect to Credo Gateway checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'attendee_type_id' => 'required|exists:attendee_types,id',
            'wants_accommodation' => 'nullable|boolean',
            'wants_materials' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $conf = Conference::findOrFail($request->conference_id);
        $type = AttendeeType::findOrFail($request->attendee_type_id);

        // Security check: Verify attendee type pricing belongs to the conference
        if ($type->conference_id !== $conf->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid attendee pricing option selected.'
                ], 400);
            }
            return back()->with('error', 'Invalid attendee pricing option selected.');
        }

        // Calculate dynamic total
        $total = floatval($type->fee);
        
        $wantsAccommodation = $request->has('wants_accommodation') && $request->wants_accommodation;
        $wantsMaterials = $request->has('wants_materials') && $request->wants_materials;

        if ($wantsAccommodation) {
            $total += floatval($conf->accommodation_fee);
        }

        if ($wantsMaterials) {
            $total += floatval($conf->conference_material_fee);
        }

        if ($total <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid checkout total. Cannot checkout free registrations.'
                ], 400);
            }
            return back()->with('error', 'Invalid checkout total. Cannot checkout free registrations.');
        }

        // Check if student verification is required
        if (stripos($type->name, 'student') !== false) {
            if (stripos($user->occupation, 'student') === false || !$user->student_id_verified) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must have a verified Student ID Card on your profile to register with student rates.'
                    ], 403);
                }
                return redirect()->route('profile.show')->with('error', 'You must have a verified Student ID Card on your profile to register with student rates.');
            }
        }

        DB::beginTransaction();

        try {
            // 1. Create or retrieve ongoing registration record
            $registration = Registration::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'conference_id' => $conf->id,
                ],
                [
                    'attendee_type_id' => $type->id,
                    'wants_accommodation' => $wantsAccommodation,
                    'wants_materials' => $wantsMaterials,
                    'is_attendance_paid' => false,
                    'is_accommodation_paid' => false,
                    'is_materials_paid' => false,
                ]
            );

            // Ensure registration details are kept updated if they selected different checkboxes
            $registration->update([
                'attendee_type_id' => $type->id,
                'wants_accommodation' => $wantsAccommodation,
                'wants_materials' => $wantsMaterials,
            ]);

            // 2. Create Payment record in DB
            $reference = 'FUL_' . time() . '_' . rand(1000, 9999);
            
            $payment = Payment::create([
                'user_id' => $user->id,
                'registration_id' => $registration->id,
                'reference' => $reference,
                'amount' => $total,
                'purpose' => 'attendance',
                'status' => 'pending',
            ]);

            // 3. Initialize Transaction with Credo Service (Safe Server-side Initialization)
            $callbackUrl = route('payment.callback');
            $response = $this->credo->initializeTransaction($total, $user->email, $reference, $callbackUrl);

            if ($response && isset($response['authorizationUrl'])) {
                DB::commit();
                Log::info("User ID {$user->id} checkout pre-initialized. Ref: {$reference}");

                // If AJAX JSON checkout for inline SDK, return parameters including secure payment_link
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'reference' => $reference,
                        'payment_link' => $response['authorizationUrl'],
                        'email' => $user->email,
                        'amount' => (int) round($total * 100),
                        'public_key' => env('CREDO_PUBLIC_KEY'),
                        'callback_url' => $callbackUrl,
                        'service_code' => env('CREDO_PAYMENT_CODE'),
                    ]);
                }

                // Fallback Redirect user to Credo secure checkout page
                return redirect()->away($response['authorizationUrl']);
            }

            throw new \Exception("Credo payment initiation returned an invalid response.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Initialization Error: " . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to initiate checkout transaction: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Unable to initiate checkout transaction. Please try again later.');
        }
    }

    /**
     * Handle payment return redirect from Credo.
     */
    public function callback(Request $request)
    {
        // Credo redirect will include 'reference' or 'transRef'
        $reference = $request->query('reference') ?? $request->query('transRef');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Missing transaction reference from gateway redirect.');
        }

        // Retrieve pending payment
        $payment = Payment::where('reference', $reference)->first();
        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'Payment transaction reference not found.');
        }

        // If already marked successful, skip verifying again
        if ($payment->status === 'successful') {
            return view('payment_status', ['status' => 'success']);
        }

        // Verify with gateway API (Safe Server-side Verification)
        $verifyData = $this->credo->verifyTransaction($reference);

        if ($verifyData) {
            // According to Credo API specifications, a status code of 0 indicates a successful payment.
            // Any string/number that is not exactly 0 or '0' (e.g. '00', '1', 'true', etc.) is considered failed.
            $gatewayStatus = isset($verifyData['status']) ? $verifyData['status'] : ($verifyData['businessStatus'] ?? null);
            
            if ($gatewayStatus !== null && ($gatewayStatus === 0 || $gatewayStatus === '0')) {
                $this->markPaymentAsSuccessful($payment, $verifyData);
                return view('payment_status', ['status' => 'success']);
            }
        }

        // Keep it pending or update to failed
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $verifyData ?? null,
        ]);

        return view('payment_status', [
            'status' => 'failed',
            'message' => 'Your payment verification was unsuccessful or is still processing.'
        ]);
    }

    /**
     * Feature: Re-verify payment using payment reference in case where payment didn't verify automatically.
     */
    public function reverify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $reference = trim($request->reference);

        // Fetch payment record
        $payment = Payment::where('reference', $reference)
            ->orWhere('gateway_response->credoReference', $reference)
            ->first();

        if (!$payment) {
            return back()->with('error', 'Payment transaction record not found with the provided reference.');
        }

        // Authorization check: User can only reverify their own payments, except Admins
        if ($payment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized access to verify this transaction.');
        }

        // If already successful, return early
        if ($payment->status === 'successful') {
            return back()->with('success', 'This transaction was already successfully verified and processed.');
        }

        // Call Credo verification
        $verifyData = $this->credo->verifyTransaction($reference);

        if ($verifyData) {
            // According to Credo API specifications, a status code of 0 indicates a successful payment.
            // Any string/number that is not exactly 0 or '0' (e.g. '00', '1', 'true', etc.) is considered failed.
            $gatewayStatus = isset($verifyData['status']) ? $verifyData['status'] : ($verifyData['businessStatus'] ?? null);

            if ($gatewayStatus !== null && ($gatewayStatus === 0 || $gatewayStatus === '0')) {
                $this->markPaymentAsSuccessful($payment, $verifyData);
                return back()->with('success', 'Transaction successfully verified! The registration has been activated.');
            }

            // Update details
            $payment->update([
                'gateway_response' => $verifyData,
            ]);

            $displayStatus = is_scalar($gatewayStatus) ? (string)$gatewayStatus : 'failed';
            return back()->with('error', "Transaction status is still: " . strtoupper($displayStatus) . ". No value was granted.");
        }

        return back()->with('error', 'Unable to verify payment status from Credo API. Please confirm your reference is correct.');
    }

    /**
     * Helper to atomically set payment to successful and update registration payment flags.
     */
    protected function markPaymentAsSuccessful(Payment $payment, array $verifyData)
    {
        DB::transaction(function () use ($payment, $verifyData) {
            // Update Payment details
            $payment->update([
                'status' => 'successful',
                'gateway_response' => $verifyData,
            ]);

            // Update registration payment indicators
            if ($payment->registration) {
                $reg = $payment->registration;

                // Grant indicators
                $reg->update([
                    'is_attendance_paid' => true,
                    'is_accommodation_paid' => $reg->wants_accommodation ? true : $reg->is_accommodation_paid,
                    'is_materials_paid' => $reg->wants_materials ? true : $reg->is_materials_paid,
                ]);

                Log::info("Registration ID {$reg->id} marked active. Attendance paid.");
            }
        });
    }
}
