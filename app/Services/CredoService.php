<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CredoService
{
    protected $apiUrl;
    protected $publicKey;
    protected $secretKey;

    public function __construct()
    {
        $this->apiUrl = env('CREDO_API_URL', 'https://api.credodemo.com');
        $this->publicKey = env('CREDO_PUBLIC_KEY', '0PUB0857hjIP3g89AETS8tEBowvaz6Lt');
        $this->secretKey = env('CREDO_SECRET_KEY', '0PRI0857SXLxLQm5BY5ZcBP9RZ244yQl');
    }

    /**
     * Initialize a payment transaction with Credo.
     *
     * @param float $amount Amount in NGN
     * @param string $email Customer email address
     * @param string $reference Unique merchant transaction reference
     * @param string $callbackUrl URL to redirect the user back to
     * @return array|null Contains 'authorizationUrl' and response details
     */
    public function initializeTransaction(float $amount, string $email, string $reference, string $callbackUrl): ?array
    {
        // Credo expects the amount in the lowest currency unit (Kobo for NGN)
        $amountInKobo = (int) round($amount * 100);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->publicKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post("{$this->apiUrl}/transaction/initialize", [
                'amount' => $amountInKobo,
                'email' => $email,
                'currency' => 'NGN',
                'bearer' => 0,
                'channels' => ['CARD', 'BANK'],
                'reference' => $reference,
                'callbackUrl' => $callbackUrl,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Credo Payment Initialized: Ref: {$reference}, Status: " . ($data['status'] ?? 'unknown'));
                return $data['data'] ?? $data;
            }

            Log::error("Credo Initialization Failure response: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Credo Initialization Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify a transaction status using the Credo reference or merchant reference.
     *
     * @param string $reference Transaction reference
     * @return array|null Returns verification response details
     */
    public function verifyTransaction(string $reference): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->get("{$this->apiUrl}/transaction/{$reference}/verify");

            if ($response->successful()) {
                $data = $response->json();
                Log::info("Credo Verification Success for Ref: {$reference}. Status: " . ($data['data']['status'] ?? $data['status'] ?? 'unknown'));
                return $data['data'] ?? $data;
            }

            Log::error("Credo Verification Failure response: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Credo Verification Exception: " . $e->getMessage());
            return null;
        }
    }
}
