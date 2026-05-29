# 💳 Credo Payment Gateway Integration Architecture

This document provides a comprehensive technical overview of the payment integration implemented for the **Federal University of Lafia (FULafia) Conference Management Portal**. It outlines the secure server-to-server transaction architecture, dual checkout modalities, system state machines, the database design, and details all files involved in the implementation.

---

## 🏛️ Architecture & Verification Security

To guarantee financial integrity and eliminate transaction tampering, the portal is built on a **secure server-to-server checkout verification loop**. Client-side values are never trusted. All fee lookups, calculations, gateway initializations, and final confirmations occur strictly in the backend, utilizing secure credentials.

### 🔄 End-to-End Payment Flow

The following sequence outlines the complete payment cycle, from attendee selection to database state fulfillment:

1. **Selection & Form Submission**: The attendee selects an ongoing conference and attendee pricing tier (along with optional accommodations and materials) and clicks checkout.
2. **Server-side Security & Calculations**: The backend `PaymentController::checkout` intercepts the request and performs core security checks:
   - Validates that the selected pricing tier belongs to the specific conference.
   - Gated check for student discounts to ensure the user has a verified Student ID.
   - Performs a complete backend fee calculation (Attendee Type Base Fee + Optional Accommodation + Optional Materials) to avoid form manipulation.
3. **Pending Record Creation**: The application inserts a new `registrations` record (if not exists) and initiates a unique transaction reference (e.g. `FUL_timestamp_random`). A pending `payments` record is created in the database.
4. **Server-side Initialization**: The backend invokes `CredoService::initializeTransaction` which calls Credo's secure `/transaction/initialize` API using the Public Key. It maps NGN to NGN lowest currency unit (Kobo) and returns an authorization payload.
5. **Visual Handoff & Dual Checkout Modal**:
   - **Inline Pop-up Flow (Primary UX)**: Intercepts form submissions using jQuery AJAX. It retrieves the payload dynamically and launches `CredoWidget` (an elegant iframe overlay widget) directly in the browser.
   - **Gateway Redirect Flow (Fallback UX)**: If accessed natively without AJAX, redirects the attendee's browser to the secure, Credo-hosted domain using the generated `authorizationUrl`.
6. **Payment Authorization**: The user fulfills payment securely via card, USSD, or bank transfer.
7. **Gateway Callback Landing**: Upon completion, the gateway redirects the user back to the portal callback URL: `/payment/callback?reference=FUL_XXX`.
8. **Server-side API Verification**: The `PaymentController::callback` captures the reference and securely queries Credo's `/transaction/{reference}/verify` API using the merchant's **Secret Key** to confirm transaction authenticity.
9. **Atomic Database State Update**: If the gateway returns `successful`, the controller runs a database transaction to:
   - Mark the payment as `successful`.
   - Store the complete JSON `gateway_response` payload for future audit.
   - Set the registration indicators to paid (`is_attendance_paid = true`, and grant `is_accommodation_paid` and `is_materials_paid` matching their checkbox choices).
10. **Success Redirect & Invoice Receipt**: Loads the callback landing screen which automatically triggers a member dashboard refresh and displays an option to print or download an official university receipt with a watermark `★ PAID ★` stamp.

---

## 🏛️ Database Schema & Entities

Payments are recorded and verified using two primary database models: `Payment` and `Registration`.

### 1. `payments` Table
Tracks every attempt to initialize a transaction, maintaining state and preserving the complete gateway payload for audits.

*   `id`: Auto-incrementing primary key.
*   `user_id`: Foreign key referencing the attendee (`users`).
*   `registration_id`: Foreign key referencing the associated `registrations` record.
*   `reference`: Unique transaction reference string generated securely (`FUL_` + `timestamp` + `rand`).
*   `amount`: Decimal representation of the transaction total (in NGN).
*   `purpose`: String label indicating the payment context (`attendance`, `abstract`, etc.).
*   `status`: Enum/string representing state transitions (`pending`, `successful`, `failed`).
*   `gateway_response`: JSON field preserving the comprehensive payload returned from Credo API (contains kobo values, bank names, customer IPs, etc.).
*   `created_at` / `updated_at`: Database timestamps.

### 2. `registrations` Table
Tracks user registration records per conference and controls user entitlements depending on payment statuses.

*   `user_id`: Reference to the user.
*   `conference_id`: Reference to the conference.
*   `attendee_type_id`: Reference to the chosen attendee pricing tier.
*   `wants_accommodation`: Boolean flag indicating if accommodation lodge was selected.
*   `wants_materials`: Boolean flag indicating if materials package was selected.
*   `is_attendance_paid`: Boolean representing baseline attendance payment clearance.
*   `is_accommodation_paid`: Boolean representing accommodation payment clearance.
*   `is_materials_paid`: Boolean representing materials package payment clearance.

---

## 🛠️ Step-by-Step Payment Integration Mechanics

### 1. Dynamic Backend Calculations & Checkout Handler
When an attendee initiates checkout, the backend recalculates fees dynamically using configured pricing tiers rather than trusting forms values:

```php
// Backend Fee Compilation inside PaymentController::checkout
$total = floatval($type->fee); // Baseline Attendee Type Fee
if ($request->has('wants_accommodation') && $request->wants_accommodation) {
    $total += floatval($conf->accommodation_fee); // Dynamic Hostel lodging fee
}
if ($request->has('wants_materials') && $request->wants_materials) {
    $total += floatval($conf->conference_material_fee); // Program material fee
}
```

### 2. Server-side Credo API Transaction Handlers
The core of our gateway integration relies on the `CredoService` class, which handles standard HTTP communications with the Credo Central endpoints:

*   **Lowest Currency Unit Mapping**: Credo expects payment amounts in Kobo (e.g. `₦1,000` is computed as `100000` kobo).
*   **Security Gating**: SSL verification overrides (`withoutVerifying()`) are implemented locally to ensure network handshake stability, while public/secret authentication keys are separated strictly inside `.env` configurations.

### 3. Dual-Mode Frontend Checkout Interface
To guarantee visual polish, the portal utilizes a **Dual-Mode Integration**:

1.  **AJAX Pop-up Flow (Primary UX)**: Intercepts form submissions using jQuery, requests payment parameters in JSON from the controller, and initializes `CredoWidget` (an elegant iframe overlay widget in the browser). This provides a seamless, professional experience without forcing the attendee to navigate away from FULafia.
2.  **Server Direct Redirect (Robust Fallback)**: Standard form submission routes standard requests by performing a `302 redirect` directly to Credo's hosted portal using the backend-generated `authorizationUrl`.

### 4. Real-time Self-service Re-verification
In cases where network drops, power failures, or client closes occur before callback triggers, a **Manual Re-verification System** is implemented:
*   Allows members to input their transaction reference (e.g., `FUL_1716584283_3821`).
*   The system executes an on-demand GET status request directly to Credo.
*   If the transaction was completed, it runs the DB transaction (`markPaymentAsSuccessful`), activates the registration, and resolves outstanding issues instantly without administrative intervention.

---

## 📂 Codebase Footprint (Files Affected)

The complete payment system architecture is implemented across the following key files:

### 1. Backend Core & Services
*   **`app/Services/CredoService.php`**: The API bridge. Sets up construct keys, initializes transactions, handles lowest-currency-unit computations, and verifies merchant/gateway references via secure endpoints.
*   **`app/Http/Controllers/PaymentController.php`**: The transaction coordinator. Manages checkout security, calculations, student verification checks, database migrations updates, callback logic, self-service queries, and PDF-style print handlers.

### 2. Database Models
*   **`app/Models/Payment.php`**: Entity mapping for `payments`. Standardizes casts like casting JSON `gateway_response` into a native array and formatting amounts.
*   **`app/Models/Registration.php`**: Configures relationships between Users, Payments, Submissions, Conferences, and manages the paid boolean variables.

### 3. Frontend Blade Views
*   **`resources/views/payments/index.blade.php`**: Payer account panel. Features statistics summaries, visual transaction listings with state badges, payment links, the manual status checking widget, and the jQuery `CredoWidget` popup loader script.
*   **`resources/views/receipt.blade.php`**: The official invoice layout. A highly-styled printable invoice featuring standard green/gold branding, dynamic calculations breakdown, payer details, and an official circular `★ PAID ★` watermark.
*   **`resources/views/payment_status.blade.php`**: Secure callback landing page. Renders state transitions (spinner loader, checklist checked status, error flags) and utilizes browser scripting to trigger auto-dashboard refreshes and pop-up closure.

### 4. Routing & Configuration
*   **`routes/web.php`**: Defines routes under verified auth groups, protecting payments from guest access and securing callback channels.
*   **`.env`**: Stores critical integration keys:
    *   `CREDO_API_URL`
    *   `CREDO_PUBLIC_KEY`
    *   `CREDO_SECRET_KEY`
    *   `CREDO_PAYMENT_CODE`

---

## 🛡️ Key Safety Measures

> [!IMPORTANT]
> **Dynamic Pricing Gating:** Form injection or field manipulation in browsers is blocked. Price evaluation is handled strictly inside the DB query during controller request processing.

> [!TIP]
> **Verified Student ID Gating:** When an attendee chooses a discounted `Student` price tier, the controller checks the `users.occupation` and gates the request. Registrations remain locked unless `users.student_id_verified` is true on the profile.

> [!WARNING]
> **Atomic Database Transactions:** Payment status modifications and attendee entitlement activations occur inside a `DB::transaction()` block. If any query fails, the process is rolled back completely to prevent double-spend or incomplete registration states.
