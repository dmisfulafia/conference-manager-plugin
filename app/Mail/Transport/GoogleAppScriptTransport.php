<?php

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

        // Extract receiver, subject, and body
        $toAddresses = array_map(fn($address) => $address->getAddress(), $email->getTo());
        $to = implode(', ', $toAddresses);

        $subject = $email->getSubject();
        
        // Get HTML body or fallback to text body
        $body = $email->getHtmlBody() ?? $email->getTextBody();

        try {
            $response = Http::withoutVerifying()
                ->post($this->url, [
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
                
                Log::error("Google App Script Email Error: " . ($status['message'] ?? 'Unknown script error'));
                throw new \Exception("Google App Script Email Error: " . ($status['message'] ?? 'Unknown script error'));
            }

            Log::error("Google App Script API HTTP Error: Status " . $response->status() . " Response: " . $response->body());
            throw new \Exception("Google App Script connection failed.");
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
