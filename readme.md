# Conference Manager Plugin

A lightweight, custom-built WordPress plugin designed to handle university conferences, abstract and paper submissions, attendee registration, and payments via Credo Central.

## Features
- **Custom Post Types**: Seamlessly integrates `Conferences`, `Abstracts`, `Papers`, and `Bookings` into the WordPress core without bloating your database.
- **User Dashboard**: A dedicated frontend portal for attendees to update their profile, upload passport photos, view payment receipts, and get their check-in QR codes.
- **Credo Central Integration**: Dynamic payment calculation and processing for physical/virtual attendance, accommodation, and document submissions.
- **Submissions Desk**: Allows users to upload `.pdf` and `.docx` files up to 15MB. Admins can review, accept, or reject these submissions from the backend.
- **Analytics & Export**: Built-in reporting table with filters and a one-click CSV export for attendee data.

## Installation & Setup
1. Ensure the plugin folder `conference-manager` is located in `wp-content/plugins/`.
2. Navigate to **Plugins** in your WordPress dashboard and **Activate** the "Conference Manager" plugin.
3. Upon activation, the `conference_attendee` user role is created automatically.

## Configuration
Before accepting registrations or payments:
1. Go to **Conferences > Settings** in the WordPress admin menu.
2. Select your **Credo Mode** (Test or Live).
3. Enter your **Credo Public Key** and **Credo Secret Key**.
4. Click **Save Changes**.

## Available Shortcodes
Use these shortcodes on your WordPress pages (or inside Elementor modules) to display the frontend interfaces:

*   `[fc_register]` : Displays the user registration form.
*   `[fc_login]` : Displays the user login form.
*   `[fc_dashboard]` : Displays the tabbed user dashboard (Profile, Bookings, Submissions).
*   `[fc_checkout]` : Displays the checkout form where users select a conference and generate a payment intent.
*   `[fc_submit_abstract]` : Displays the abstract document upload form.
*   `[fc_submit_paper]` : Displays the full paper document upload form.

## Managing Conferences
1. Go to **Conferences > Add New**.
2. Enter the title and description of the event.
3. Scroll down to the **Conference Details & Pricing** meta box.
4. Set the Start and End dates.
5. Set the pricing (in local currency) for Physical Attendance, Virtual Attendance, Accommodation, and Submission fees. Leave them blank or `0` if they are free.
6. **Publish** the conference.

## Managing Submissions
1. When a user submits an abstract or paper, it will appear under **Conferences > Abstracts** or **Conferences > Full Papers**.
2. Click to edit the submission.
3. On the right-hand sidebar, locate the **Submission Status & File** widget.
4. Click the button to view/download their document.
5. Change the dropdown status to **Accepted** or **Rejected** and update the post.

## Exporting Data
1. Go to **Conferences > Analytics & Export**.
2. Use the dropdowns to filter by a specific Conference or Payment Status.
3. Click **Filter** to view the results on-screen.
4. Click **Export to CSV** to download a spreadsheet of the currently filtered attendees for badge printing or reporting.
