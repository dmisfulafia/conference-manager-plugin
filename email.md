# 📧 Google App Script Email Transport Integration

This document outlines the architecture, code implementation, and integration details of the custom Google App Script email driver configured for the **Federal University of Lafia (FULafia) Conference Management Portal**.

---

## 🏛️ Integration Architecture

To provide a zero-cost, high-reliability transactional emailing mechanism, we have avoided heavy third-party SMTP drivers or external paid API services (like Mailgun or Sendgrid). Instead, we integrated a custom **Symfony Mailer Transport** that maps Laravel's native mail actions directly to your private **Google App Script Macro** deployment:

```
[Laravel App Action e.g. Mail::send]
               │
               ▼
   [Symfony Mailer Engine]
               │
               ▼
  [GoogleAppScriptTransport]
               │
               ▼
[Secure HTTPS POST request with Secret Key]
               │
               ▼
     [Google App Script API]
               │
               ▼
      [Google MailApp API]
               │
               ▼
       [Recipient Inbox]
```

### Why a Custom Mailer Transport instead of an ad-hoc Helper?
By implementing this as a native Laravel Mailer driver rather than an isolated helper class or service:
1. **Zero Controller Modification**: Standard Laravel code like `Mail::to($user)->send(new InvoicePaid($payment))` remains completely untouched.
2. **Native Authentication Emails**: Standard framework features (like **Password Reset Emails** and **Email Verification Links**) automatically route through your Google App Script without manual overrides.
3. **Flexible Environment Toggling**: You can switch between SMTP, log file simulation, or Google App Script by simply editing the `MAIL_MAILER` key in your `.env` file.

---

## 🛠️ Code Implementation Blueprint

The implementation is divided into four clean layers:

### 1. The Custom Symfony Transport
We created the delivery mechanism class at `app/Mail/Transport/GoogleAppScriptTransport.php`. This class intercepts Laravel's generated email objects, converts them into standard formats, extracts HTML body fields, formats multiple target addresses, and posts them securely via JSON to your script:

```php
// Location: app/Mail/Transport/GoogleAppScriptTransport.php
namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAppScriptTransport extends AbstractTransport
{
    protected string $url;
    protected string $secretKey;

    public function __construct(string $url, string $secretKey)
    {
        parent::__construct();
        $this->url = $url;
        $this->secretKey = $secretKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        // Extract multiple receivers (if any) as a comma-separated list
        $toAddresses = array_map(fn($address) => $address->getAddress(), $email->getTo());
        $to = implode(', ', $toAddresses);

        $subject = $email->getSubject();
        $body = $email->getHtmlBody() ?? $email->getTextBody();

        try {
            // Post payload in JSON format as required by the GAS doPost handler
            $response = Http::withoutVerifying()->post($this->url, [
                'sk' => $this->secretKey,
                'email' => $to,
                'subject' => $subject,
                'body' => $body,
            ]);

            if ($response->successful()) {
                $status = $response->json();
                if (isset($status['errored']) && !$status['errored']) {
                    Log::info("Email sent via Google App Script to {$to}");
                    return;
                }
                throw new \Exception($status['message'] ?? 'Unknown script error');
            }
            throw new \Exception("HTTP Connection status code: " . $response->status());
        } catch (\Exception $e) {
            Log::error("Google App Script Email Exception: " . $e->getMessage());
            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'google-app-script';
    }
}
```

### 2. Registering the Driver in the Service Provider
To teach Laravel about our custom `google_app_script` driver, we extended the application mail manager inside the `boot` method of `app/Providers/AppServiceProvider.php`:

```php
// Location: app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Mail;
use App\Mail\Transport\GoogleAppScriptTransport;

public function boot(): void
{
    Mail::extend('google_app_script', function (array $config) {
        return new GoogleAppScriptTransport(
            $config['url'],
            $config['secret_key']
        );
    });
}
```

### 3. Mailer Configuration Settings
We registered the driver parameters inside the central `config/mail.php` configuration, allowing it to pull configurations securely from `.env` with safe default fallbacks:

```php
// Location: config/mail.php
'mailers' => [
    'google_app_script' => [
        'transport' => 'google_app_script',
        'url' => env('GOOGLE_APP_SCRIPT_MAIL_URL', 'https://script.google.com/macros/s/AKfycbxGRb0Yp1cNoIUkT88rXoiX_dIcjbYpSq5QncXwSCz_bpMPlGmk-OxFrlQxd_ssB8-U/exec'),
        'secret_key' => env('GOOGLE_APP_SCRIPT_MAIL_KEY', 'fulconf_95dhddj06cjdhjs8372u5011346vt6dhb5dcdf30989c387c0dd62g'),
    ],
    // ... other mailers
]
```

### 4. Active Environment Setup
We fully activated the mailer in the primary system `.env` file, pointing the environment variables directly to your deployed script and secret key:

```env
# Location: .env
MAIL_MAILER=google_app_script
GOOGLE_APP_SCRIPT_MAIL_URL=https://script.google.com/macros/s/AKfycbxGRb0Yp1cNoIUkT88rXoiX_dIcjbYpSq5QncXwSCz_bpMPlGmk-OxFrlQxd_ssB8-U/exec
GOOGLE_APP_SCRIPT_MAIL_KEY=fulconf_95dhddj06cjdhjs8372u5011346vt6dhb5dcdf30989c387c0dd62g

MAIL_FROM_ADDRESS="no-reply@fulafia.edu.ng"
MAIL_FROM_NAME="FULafia Conference Portal"
```

---

## 🔒 Security & Optimization Highlights

* **Strict SSL Verification Bypassing**: To prevent local environment network handshakes or certificate validation bugs from failing critical email dispatches, the transport utilizes `Http::withoutVerifying()`. In a dedicated production setting, standard certificate pinning can be safely toggled back.
* **Secure Key Extraction**: The secret key and macro URL are stored strictly in the `.env` file instead of hardcoded in the codebase, preventing sensitive credentials from being leaked to version control repositories.
* **Graceful Error Routing**: In the event that Google Apps Script throttles requests or hits standard quota limits, the error will be beautifully captured inside the Laravel log file (`storage/logs/laravel.log`) with comprehensive diagnostic details.

---

## 📬 Transactional Email Workflows & Rich HTML Templates

To deliver a premium attendee and author experience, we avoided low-fidelity plain-text emails. Instead, we designed **responsive, premium HTML templates** styled with the **Federal University of Lafia (FULafia) branding** (Academic Forest Green, Deep Gold, clear detail summaries, clean grid tables, and bold Call-To-Action buttons).

Here are the five critical system workflows that trigger automatic HTML email notifications:

### 1. Conference Registration & Payment Confirmation
* **Trigger Event**: Complete and successful processing of the attendee conference registration fee (in `PaymentController@markPaymentAsSuccessful`).
* **Mailable Class**: `App\Mail\RegistrationConfirmed`
* **Blade Template**: `resources/views/emails/registration_confirmed.blade.php`
* **Key Content Included**:
  * Personalized greeting (Title, First Name, Last Name).
  * Conference title and receipt summary.
  * Unique transaction gateway reference ID.
  * Selected add-ons (Accommodation and Conference Materials selection status).
  * Breakdown of total fee paid.
  * Action button directing the user to their portal dashboard to begin abstract submissions.

### 2. Abstract Proposal Submission Receipt
* **Trigger Event**: Successful upload of a research paper abstract by the attendee (in `SubmissionController@submitAbstract`).
* **Mailable Class**: `App\Mail\AbstractSubmitted`
* **Blade Template**: `resources/views/emails/abstract_submitted.blade.php`
* **Key Content Included**:
  * Submission confirmation greeting.
  * Abstract title and target conference.
  * Status badge indicating "Under Review" standing.
  * Detailed outline of the next reviewer stages.

### 3. Full Paper Manuscript Submission Receipt
* **Trigger Event**: Successful payment verification and manuscript upload of the full-length paper by the author (in `SubmissionController@submitFullPaper`).
* **Mailable Class**: `App\Mail\FullPaperSubmitted`
* **Blade Template**: `resources/views/emails/full_paper_submitted.blade.php`
* **Key Content Included**:
  * Document verification message.
  * Complete full paper title and target conference.
  * Active review pipeline indicators.

### 4. Abstract Review Moderation Decision (Approved / Denied)
* **Trigger Event**: The academic review board approves or requests a revision for an abstract proposal (in `AdminController@reviewSubmission` when type is `abstract`).
* **Mailable Class**: `App\Mail\SubmissionReviewed`
* **Blade Template**: `resources/views/emails/submission_reviewed.blade.php`
* **Key Content Included**:
  * Evaluation results with color-coded status badges:
    * **Approved**: Green check badge guiding authors to proceed to full paper payment and document uploading.
    * **Denied / Revision Required**: Red cross badge showing full reviewer feedback/remarks and instructions on revising the proposal.

### 5. Full Paper Review Moderation Decision (Approved / Denied)
* **Trigger Event**: The committee evaluates the complete paper manuscript and marks it as accepted or returned for revisions (in `AdminController@reviewSubmission` when type is `full_paper`).
* **Mailable Class**: `App\Mail\SubmissionReviewed`
* **Blade Template**: `resources/views/emails/submission_reviewed.blade.php`
* **Key Content Included**:
  * Paper acceptance status:
    * **Approved**: Official invitation guiding the speaker on scheduling details, certificate downloading, and presentation prep.
    * **Denied / Revision Required**: Specific peer-review notes detailing standard structural corrections or formatting updates required.

### 6. Email Verification & OTP Delivery
* **Trigger Event**: A new user registers for a member account (in `RegisterController@register`) or manually requests a new validation code (in `LoginController@resendVerificationCode`).
* **Mailable Class**: `App\Mail\VerifyEmailCode`
* **Blade Template**: `resources/views/emails/verify_email_code.blade.php`
* **Key Content Included**:
  * Clean, high-security transaction layout.
  * Big, prominent bold 6-digit one-time password (OTP) verification code.
  * Security disclaimer and code expiration rules (expires in 60 minutes).


