<?php

namespace App\Service;
use Illuminate\Support\Facades\Http;

class SelcomService {

    protected string $apiKey;
    protected string $apiSecret;
    protected string $apiUrl;
    protected string $vendorId;

    public function __construct()
    {

    info('Selcom API key: '.config('services.selcom_service.selcom_api_key'));
    $this->apiKey    = config('services.selcom_service.selcom_api_key') ?? env('SELCOM_API_KEY');
    $this->apiSecret = config('services.selcom_service.selcom_secret_key') ?? env('SELCOM_API_SECRET');
    $this->apiUrl    = config('services.selcom_service.selcom_url') ?? env('SELCOM_SERVICE');
    $this->vendorId  = config('services.selcom_service.selcom_vendor_id') ?? env('SELCOM_VENDORID');

    }

    /**
     * Send payment request to Selcom
     */
    // public function makePayment(array $payload): array
    // {
    //     $headers = $this->generateHeaders($payload);

    //     $response = Http::withHeaders($headers)
    //         ->post($this->apiUrl, $payload);

    //     return $response->json();
    // }
    
          public function createOrder(array $payload): array
            {
            // inject vendor if not set
            if (!isset($payload['vendor'])) {
                $payload['vendor'] = $this->vendorId;
            }

            $headers = $this->generateHeaders($payload);

            $response = Http::withHeaders($headers)
                ->post("{$this->apiUrl}/checkout/create-order-minimal", $payload);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
    }

        public function confirmOrder(array $payload): array
            {
            
            $headers = $this->generateHeaders($payload);

            $response = Http::withHeaders($headers)
                ->post("{$this->apiUrl}/checkout/wallet-payment", $payload);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
    }

     public function orderStatus(array $payload): array
     {
            $headers = $this->generateHeaders($payload);
            $response = Http::withHeaders($headers)
                ->get("{$this->apiUrl}/checkout/list-orders", $payload);

            return [
                'success'  => $response->successful(),
                'status'   => $response->status(),
                'response' => $response->json(),
            ];

     }

    /**
     * Generate headers required by Selcom API
     */
    public function generateHeaders(array $payload): array
    {
        $timestamp = now()->format('Y-m-d\TH:i:sP'); // ISO 8601 timestamp
        $signedFields = implode(',', array_keys($payload));

        // Build string to sign
        $signingString = "timestamp={$timestamp}";
        foreach ($payload as $key => $value) {
            $signingString .= "&{$key}={$value}";
        }

        // Generate HMAC SHA256 digest (Base64 encoded)
        $digest = base64_encode(
            hash_hmac('sha256', $signingString, $this->apiSecret, true)
        );

        return [
            'Content-Type'  => 'application/json',
            'Authorization' => 'SELCOM ' . base64_encode($this->apiKey),
            'Digest-Method' => 'HS256',
            'Digest'        => $digest,
            'Timestamp'     => $timestamp,
            'Signed-Fields' => $signedFields,
        ];
    }

   

}