<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudName;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
    }

    /**
     * Upload a file to Cloudinary.
     *
     * @param UploadedFile $file
     * @param string $folder (abstract, passports, full-paper-submissions)
     * @return string Secure URL of the uploaded asset
     * @throws \Exception
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $timestamp = time();
        
        // Prepare parameters for signing
        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];

        // Generate signature
        // Signature string: folder={folder}&timestamp={timestamp}{api_secret}
        $sigString = "folder=" . $folder . "&timestamp=" . $timestamp . $this->apiSecret;
        $signature = sha1($sigString);

        try {
            // Send Multipart API call to Cloudinary
            $response = Http::withoutVerifying()->asMultipart()
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload", [
                    'api_key' => $this->apiKey,
                    'timestamp' => $timestamp,
                    'folder' => $folder,
                    'signature' => $signature,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['secure_url'])) {
                    Log::info("Cloudinary Upload Success: {$data['secure_url']} in folder {$folder}");
                    return $data['secure_url'];
                }
            }

            Log::error("Cloudinary Upload Fail API Response: " . $response->body());
            throw new \Exception("Cloudinary upload failed: " . ($response->json('error.message') ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error("Cloudinary upload exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete an asset from Cloudinary by its secure URL.
     *
     * @param string $secureUrl
     * @return bool
     */
    public function delete(string $secureUrl): bool
    {
        $publicId = $this->extractPublicId($secureUrl);
        if (!$publicId) {
            return false;
        }

        $timestamp = time();
        
        // Generate signature
        $sigString = "public_id=" . $publicId . "&timestamp=" . $timestamp . $this->apiSecret;
        $signature = sha1($sigString);

        try {
            $response = Http::withoutVerifying()->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy", [
                'public_id' => $publicId,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

            if ($response->successful() && $response->json('result') === 'ok') {
                Log::info("Cloudinary Asset Destroyed Success: {$publicId}");
                return true;
            }

            // Try raw destroy if image destroy returns not found (e.g. for PDFs/Word files)
            $responseRaw = Http::withoutVerifying()->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/raw/destroy", [
                'public_id' => $publicId,
                'api_key' => $this->apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);

            if ($responseRaw->successful() && $responseRaw->json('result') === 'ok') {
                Log::info("Cloudinary Raw Asset Destroyed Success: {$publicId}");
                return true;
            }

            Log::warning("Cloudinary Asset Destroy Fail for: {$publicId}. Result: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Cloudinary delete exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract the public_id (with folder prefix) from a secure Cloudinary URL.
     *
     * @param string $url
     * @return string|null
     */
    protected function extractPublicId(string $url): ?string
    {
        // Example URL: https://res.cloudinary.com/dmisfulafia/image/upload/v1234567890/passports/xxxx.png
        // We want: passports/xxxx
        $pattern = '/\/v\d+\/(.+)\.[a-zA-Z0-9]+$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        // Fallback pattern if version vXXXX is missing
        $patternFallback = '/\/upload\/(.+)\.[a-zA-Z0-9]+$/';
        if (preg_match($patternFallback, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
