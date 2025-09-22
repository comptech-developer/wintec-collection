<?php

namespace App\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SMSService
{

    protected string $apiUrl;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $senderId;
    public function __construct()
    {
     $this->apiUrl    = config('services.sms_service.sms_url') ?? env('SMS_URL');
     $this->apiKey   = config('services.sms_service.sms_apikey');
     $this->apiSecret  = config('services.sms_service.sms_secret');
     $this->senderId = config('services.sms_service.sms_senderid');
        
    }

    public function sendSMS($phone,$message)
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)->withHeaders([
            'Content-Type' => ' application/json',
            'Accept' => 'application/json',
        ])->post($this->apiUrl, [
            'from' => $this->senderId,
            'to' => trim($this->formatPhoneNumber($phone)),
            'text' => $message,
        ]);
    
        return $response;

    }

    private function formatPhoneNumber($phone)
    {
        // Remove spaces, dashes, plus signs
        $phone = preg_replace('/\D/', '', $phone);
    
        if (Str::startsWith($phone, '0')) {
            // Must be 10 digits if local
            if (strlen($phone) !== 10) {
                return false; // invalid
            }
            // Remove first zero and prepend 255
            return '255' . substr($phone, 1);
        }
    
        if (Str::startsWith($phone, '255')) {
            // Must be 12 digits if international
            if (strlen($phone) !== 12) {
                return false; // invalid
            }
            return $phone;
        }
    
        // Not valid
        return false;
    }

}
