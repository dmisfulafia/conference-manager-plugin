# Implementation Plan: FULafia Conference Manager

This document outlines the architecture, database schema, design specifications, and implementation steps for building the **Federal University of Lafia (FULafia)** Conference Management System using **Laravel 11**, **Tailwind CSS** (utilizing the brand color `#9d7126`), and the **Credo** payment gateway.

---

## 🏛️ Project Specifications & Brand Identity

* **Institution:** Federal University of Lafia (FULafia)
* **Primary Brand Color:** `#9d7126` (Gold)
* **Secondary Color:** `#1a472a` (Forest Green - representing the classic academic vibe)
* **Payment Gateway:** Credo Central API
* **Tech Stack:**
  * **Backend:** Laravel 11, PHP 8.2+
  * **Frontend:** Blade Templates with bootstrap
  * **Datatables:** jQuery DataTables with HTML5 Export Buttons (Excel/CSV)
  * **Database:** MySQL

---

## 🛠️ System Architecture & Database Schema

To support the requirements, we need a robust database structure. Below is the proposed entity-relationship design.

### Database Tables (Migrations)

#### 1. `users`

Standard Laravel users table extended with registration fields.

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('title'); // Prof, Dr, Mr, Mrs, Ms, etc.
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('phone');
    $table->enum('gender', ['Male', 'Female', 'Other']);
    $table->string('occupation');
    $table->string('institution')->nullable();
    $table->string('country');
    $table->string('password');
    $table->enum('role', ['super_admin', 'admin', 'user'])->default('user');
    
    // Profiles details
    $table->string('passport_photo')->nullable(); // Path to passport
    $table->string('student_id_card')->nullable(); // Path to student ID (mandatory for students verification)
    
    $table->rememberToken();
    $table->timestamps();
});
```

#### 2. `verification_codes`

For handling out-of-the-box secure otp/email verification.

```php
Schema::create('verification_codes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('code');
    $table->timestamp('expires_at');
    $table->timestamps();
});
```

#### 3. `conferences`

```php
Schema::create('conferences', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->date('start_date');
    $table->date('end_date');
    // add address, next of kin, next of kin phone
    $table->string('address')->nullable();
    $table->string('next_of_kin')->nullable();
    $table->string('next_of_kin_phone')->nullable();
    $table->enum('status', ['ongoing', 'past'])->default('ongoing');
    $table->string('venue');
    $table->decimal('accommodation_fee', 10, 2)->default(0.00);
    $table->decimal('conference_material_fee', 10, 2)->default(0.00);
    $table->timestamps();
});
```

#### 4. `attendee_types`

Configured per conference (e.g. Student, Virtual, Physical, International).

```php
Schema::create('attendee_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('conference_id')->constrained()->onDelete('cascade');
    $table->string('name'); // e.g., "Virtual Attendee", "Student Attendee"
    $table->decimal('fee', 10, 2);
    $table->timestamps();
});
```

#### 5. `registrations`

Maps user to a conference with their selected attendee type, accommodation, and extra items.

```php
Schema::create('registrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('conference_id')->constrained()->onDelete('cascade');
    $table->foreignId('attendee_type_id')->constrained()->onDelete('cascade');
    $table->boolean('wants_accommodation')->default(false);
    $table->boolean('wants_materials')->default(false);
    
    // Financial Trackers
    $table->boolean('is_attendance_paid')->default(false);
    $table->boolean('is_accommodation_paid')->default(false);
    $table->boolean('is_materials_paid')->default(false);
    
    $table->timestamps();
});
```

#### 6. `submissions`

Handles abstracts and full papers per registration.

```php
Schema::create('submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('registration_id')->constrained()->onDelete('cascade');
    $table->string('title');
    
    // Abstract Section
    $table->text('abstract_text')->nullable();
    $table->string('abstract_file_path')->nullable();
    $table->boolean('is_abstract_paid')->default(false);
    $table->enum('abstract_status', ['pending', 'approved', 'denied'])->default('pending');
    $table->text('abstract_rejection_reason')->nullable();
    
    // Full Paper Section
    $table->string('full_paper_file_path')->nullable();
    $table->boolean('is_full_paper_paid')->default(false);
    $table->enum('full_paper_status', ['pending', 'approved', 'denied'])->default('pending');
    $table->text('full_paper_rejection_reason')->nullable();
    
    $table->timestamps();
});
```

#### 7. `payments`

Tracks transactions verified via Credo.

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('registration_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('reference')->unique();
    $table->decimal('amount', 10, 2);
    $table->string('purpose'); // attendance, abstract, full_paper, accommodation, materials
    $table->string('status')->default('pending'); // pending, successful, failed
    $table->json('gateway_response')->nullable();
    $table->timestamps();
});
```

#### 8. `complaints`

```php
Schema::create('complaints', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('subject');
    $table->text('message');
    $table->enum('status', ['pending', 'resolved'])->default('pending');
    $table->text('admin_reply')->nullable();
    $table->timestamps();
});
```

---

## 🔄 Core User Flow & Implementation Details

```mermaid
graph TD
    A[User Registers] --> B[Verification Code/Link Sent to Email]
    B --> C[User Verifies Account]
    C --> D[Login to FULafia Dashboard]
    D --> E[Profile Complete: Upload ID Card & Passport] (not compulsory for now)
    D --> F[Explore Conferences]
    F --> G[Select Ongoing Conference]
    G --> H[Choose Attendee Type + Add-ons]
    H --> I[Pay Attendance Fee via Credo]
    I --> J{Wants to Submit Paper?}
    J -- Yes --> K[Pay Abstract Fee via Credo]
    K --> L[Upload Abstract PDF/Word <=20MB]
    L --> M[Admin Reviews Abstract]
    M -- Denied --> N[Email Sent + Correct abstract & re-submit]
    M -- Approved --> O[Email Sent + Pay Full Paper Fee via Credo]
    O --> P[Upload Full Paper <=20MB]
    P --> Q[Admin Reviews Full Paper]
```

### 1. Registration & Security Flow

* **Form fields:** `Title` (Select option), `Name`, `Phone`, `Gender`, `Occupation`, `Institution (Optional)`, `Country` (Select dropdown), `Password`, `Confirm Password`.
* **Verification:** Upon registration, fire a custom Laravel Event `RegisteredUser`. This sends a beautiful template-based email with a 6-digit OTP code + signed activation link valid for 60 minutes.
* **Middleware:** Implement `unverified` and `verified` custom middleware configurations. If a user logs in but has not verified, redirect them exclusively to `/verify-email`.

### 2. Dashboard Navigation & Theme

* **Sidebar layout** with custom dashboard pages:
  * **Profile:** Details update, Photo passport upload, Student ID card upload (with warning indicator if register as a student and card not verified).
  * **Conferences:** Listing ongoing/past conferences and registration interfaces.
  * **Payments:** Detailed invoice table with download receipts and Credo status trackers.
  * **Complaints:** Form to file complaints and view tickets.
* **Visual Style:** Modern, premium dark/light layout utilizing HSL golden gradients:
  * Primary Gold Color: `#9d7126` (`rgb(157, 113, 38)`)
  * Interactive components have dynamic hover highlights, clean cards, transitions, and loading skeletons.

### 3. Conference Selection & Attendee Logic

* Users can select ongoing conferences.
* **Dynamic pricing calculator:** Calculates the final registration cost in real-time as the user selects combinations:
  * Attendee Type (e.g. Student: ₦5,000, Virtual: ₦10,000, International: $100).
  * Accommodation Option (e.g. ₦15,000 extra).
  * Materials Option (e.g. ₦5,000 extra).
* **Payment Trigger:** Users are redirected to the Credo checkout modal. Once payment is verified via Credo API webhook/callback, the registration is active.

### 4. Dual-Stage Paper Submission & Strict Upload Limits

* **Stage 1: Abstract Submission**
  * Locked behind Registration and Payment.
  * Requires separate Abstract Payment.
  * Validation: Must be a Word Document (`.doc`, `.docx`) or PDF (`.pdf`) under 20MB.
  * *Trigger:* On upload, fire `AbstractSubmitted` email to user.
* **Stage 2: Full Paper Submission**
  * Locked until **Abstract Status is set to "Approved"** by Admin.
  * Requires separate Full Paper Payment.
  * Validation: PDF or Word, <= 20MB.
  * *Trigger:* On upload, fire `FullPaperSubmitted` email to user.

---

## 💳 Credo Payment Gateway Integration

We will implement a custom `CredoService` wrapper class in Laravel to communicate with Credo's payment API.

### Flow of Payment

1. User requests to pay a specific fee (e.g., Attendance Fee).
2. The application generates a unique reference: `REF-FULAFIA-{USER_ID}-{TIME}`.
3. Call Credo's transaction initialization endpoint (`https://api.credo.co/v1/payments/initiate`).
4. Redirect the user to the Credo payment interface or open Credo's inline checkout.
5. **Callback Route** verifies transaction:

   ```php
   public function verifyPayment(Request $request) {
       $reference = $request->query('transRef');
       // Make server-to-server HTTP GET call to Credo verify endpoint
       // Update payment status in database
       // Redirect user to success or failure page
   }
   ```

6. **Webhook Handler** is configured to handle async payment notifications to ensure reliability (even if the user closes their browser).

---

## 👑 Administrative Control Panel Features

The admin section is reserved for `admin` and `super_admin` roles.

### 1. Attendee List with Advanced Datatables

* Interactive DataTables (using jQuery DataTable & responsive extension).
* Buttons to export data to **Excel**, **CSV**, and **PDF**.
* Columns: Name, Email, Phone, Attendee Type, Payment Status, Submissions, Verification Status (for Student ID).
* Quick toggle to verify Student IDs with modal image previews.

### 2. User & Admin Management

* **Admins:** Reset any user's password securely (generating a random strong password or manually inputting one).
* **Super Admins:** Complete CRUD operations on other Admin accounts, including changing their passwords, setting their permission scopes, or disabling access.

### 3. Payment Logs & Financial Exports

* Unified panel for tracking transaction history.
* Filter payments by **Conference**, **Payment Purpose** (Accommodation, Abstract, Attendance), **Status**, and **Date Range**.
* Export filtered list to Excel to ease work for the university bursary.

### 4. Ticket System (Complaints)

* Table of user complaints.
* Mark as "Resolved" with optional reply box which triggers a resolution email back to the attendee.

### 5. Submission Moderation & Rejection Reason Flow

* Interactive submission queue showing Abstracts and Full Papers.
* Preview PDFs directly in the browser.
* Buttons: `Approve` / `Deny`.
* **Deny Modal:** Mandatory `rejection_reason` text field. When denied, the submission status changes to `denied` and the user is sent an email detailing the exact corrections required, allowing them to re-upload.

### 6. Conference Lifecycle Manager

* CRUD operations on Conferences.
* Assign fields for Accommodation Fee, Material Fee, and set up dynamic Attendee Type Pricing.
* Easily toggle status between `ongoing` and `past`.

---

## ✉️ Automated Email Notifications (Mailables)

We will use Laravel's highly expressive `Mail` API with rich markdown templates styled with FULafia Branding (`#9d7126` headers and buttons).

| Trigger Event | Email Mailable Class | Recipient | Key Information Included |
| :--- | :--- | :--- | :--- |
| **New User Registration** | `VerifyEmailMail` | User | 6-digit OTP verification code + signed URL link |
| **Abstract Received** | `AbstractSubmittedMail` | User | Abstract title, date of submission, acknowledgement text |
| **Abstract Decision** | `AbstractReviewedMail` | User | Status (Approved / Denied), Reason if denied, Next steps (Payment link if approved) |
| **Full Paper Received** | `FullPaperSubmittedMail` | User | Full paper confirmation message, review timeline explanation |
| **Full Paper Decision** | `FullPaperReviewedMail` | User | Status (Approved / Denied), Reason if denied, Presentation slots |
| **Complaint Resolved** | `ComplaintResolvedMail` | User | Ticket ID, original message, admin resolution feedback |

---

## 🚀 Step-by-Step Implementation Roadmap

```
├── Phase 1: Environment & Base Setup (Days 1-2)
│   ├── Create fresh Laravel 11 project
│   ├── Configure bootstrap CSS with FULafia Gold Theme (#9d7126)
│   └── Establish base layouts, dashboard shells, and routing
│
├── Phase 2: Core Authentication & Profile Verification (Days 3-4)
│   ├── Run migrations for custom User, Verification, and Profile assets
│   ├── Build registration with custom OTP + Email Link Verification
│   └── Implement Profile updates, including Passport & Student ID uploads
│
├── Phase 3: Conference Catalog & Credo Integration (Days 5-7)
│   ├── Build Conference CRUD models and dynamic pricing structures
│   ├── Write CredoService payment classes (Initialize, Verify, Webhook)
│   └── Build multi-choice checkout with dynamic bill calculation
│
├── Phase 4: Structured Submissions & Storage Logic (Days 8-9)
│   ├── Implement Abstract upload with payment verification middleware
│   ├── Create approval logic and state machines for full paper locking
│   └── Set validation strictures (PDF/Word, max 20MB limit)
│
├── Phase 5: Supercharged Admin Command Center (Days 10-12)
│   ├── Implement Datatable lists with Excel / PDF export features
│   ├── Build Password Resets, Submission Moderation (Approve/Deny modals)
│   └── Build Conference Lifecycle Editor & Complaint resolver
