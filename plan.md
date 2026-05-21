# Custom Conference Management Plugin Architecture

## Overview
A custom, modular WordPress plugin designed from scratch to manage university conferences, abstract/paper submissions, attendee registration, and Credo Central-powered payments.

**Core Philosophy:** 
- **Built from Scratch**: We will not extend WooCommerce or other bulky plugins. Building from scratch ensures the plugin remains lightweight, highly modular, and optimized strictly for conference needs without unnecessary overhead. This guarantees maximum speed.
- **Speed & Modularity**: The codebase will be highly modular (separate classes for auth, payments, submissions, and QR codes), ensuring fast load times and easy maintenance.

## 1. System Implementation Flow
To ensure smooth development, the plugin will be built in the following sequential phases:
1.  **Phase 1: Foundation & User Management**: Register Custom Post Types (CPTs), setup the `conference_attendee` user role, build the custom registration form, email verification flow, and login system.
2.  **Phase 2: User Dashboard & Profiles**: Create the frontend user dashboard (`[fc_dashboard]`) where users can update their profiles, change passwords, and upload passport photos.
3.  **Phase 3: Core Conference Setup**: Admin interfaces to create conferences, set pricing (physical, virtual, accommodation), and manage basic details.
4.  **Phase 4: Payment Integration (Credo Central)**: Integrate the Credo Central API. Build the checkout flow for conference attendance and accommodation. Setup webhook listeners to verify transactions securely.
5.  **Phase 5: Submissions Module**: Build frontend forms for abstract and full paper submissions (handling any required fees via Credo). Build the admin interface to accept/reject these submissions.
6.  **Phase 6: Receipts & QR Codes**: Implement the PHP QR code generator, linking it to successful `conference_booking` records for quick check-in.
7.  **Phase 7: Admin Analytics & Exports**: Build out the advanced Bookings admin table with search, filters, and CSV export functionality.

## 2. Data Structure & Storage

### Custom Post Types (CPTs)
- **`conference`**: Core post type containing conference details, pricing, and dates.
- **`conference_abstract`**: User-submitted abstracts.
- **`conference_paper`**: User-submitted full papers.
- **`conference_booking`**: Transaction/Registration records (Acts as the receipt/invoice).

### User Management
- **Role**: `conference_attendee`
- **User Meta**: `title`, `phone`, `gender`, `occupation`, `institution`, `country`, `passport_photo_id` (Attachment ID).
- **Authentication Flow**: Custom registration form -> Sends verification email containing a unique hash -> User clicks link -> Verifies hash & activates account -> Login via custom form (Email/Password).

## 3. Plugin Modules

### A. Authentication & User Profile Module
- Custom Registration Form (shortcode: `[fc_register]`)
- Core WP Email Verification hooks.
- Custom Login Form (shortcode: `[fc_login]`)
- User Dashboard (shortcode: `[fc_dashboard]`) with tabs for:
  - Profile Update & Password Reset
  - Bookings & Receipts (QR Code display)
  - Abstract/Paper Submissions

### B. Payment & Integration Module (Credo Central)
- Direct integration with Credo Central API.
- Payment intents for: 
  - Conference Attendance
  - Abstract Submission Fee (if applicable)
  - Full Paper Submission Fee (if applicable)
  - Accommodation Fee (if needed)
  - Virtual attendance Fee (if needed)  
- Webhook listener to automatically update booking statuses upon successful payment.

### C. QR Code & Receipt Module
- Integration with a PHP QR Code library.
- Generates a unique QR code per successful booking/payment.
- QR code payload will contain the Booking ID and User ID for scanning during check-in.

### D. Submissions Management Module
- Frontend submission forms for Abstracts and Full Papers (only accessible after necessary payments or acceptance).
- Status workflow: `Pending Review` -> `Accepted` / `Rejected`.

### E. Admin Dashboard & Management Module
- Custom WP Admin sub-menus under "Conferences".
- Interfaces to review, accept/reject abstracts and full papers.
- Bookings table with CSV export functionality with search and filter
- Manual registration form for admins to add users bypassing payment.

## 4. Recommended Directory Structure
```text
fulafia-conference-manager/
├── fulafia-conference-manager.php  (Main plugin file)
├── includes/
│   ├── class-fcm-cpts.php          (Registers CPTs and Taxonomies)
│   ├── class-fcm-users.php         (Auth, Registration, Profile logic)
│   ├── class-fcm-payments.php      (Credo Central integration & Webhooks)
│   ├── class-fcm-submissions.php   (Abstract/Paper logic)
│   └── class-fcm-qr-generator.php  (QR Code logic)
├── admin/
│   ├── class-fcm-admin.php         (Admin menus and dashboards)
│   └── views/                      (Admin HTML templates)
├── public/
│   ├── class-fcm-public.php        (Shortcodes and frontend hooks)
│   └── views/                      (Frontend HTML templates)
├── assets/
│   ├── css/
│   └── js/
└── templates/                      (Overridable frontend templates)
```

## Clarification Questions
1.  **Credo Central Keys**: Do you have the Credo Central test API keys ready so we can start implementing the payment flow when we reach Phase 4? - create a secured space where I enter this
2.  **Conference Details**: I noticed you removed speakers, schedules, programs, and locations from the Custom Post Types. I assume you will be building those sections directly on the frontend using Elementor (or a similar page builder) and just linking the custom shortcodes (like registration and login) to those pages. Is this correct? - Yes, I will sort this myself
3.  **File Uploads**: For abstract and full paper submissions, what file formats should be allowed? (e.g., PDF, DOCX only?) What should be the maximum file size limit for these uploads? - use docx and pdf only and maxx size is 15mb
4.  **Admin Bypass Registration**: For the manual admin registration, should an email still be sent to the manually added user with their auto-generated password and QR code? - Yes, email should be sent