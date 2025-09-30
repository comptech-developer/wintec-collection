<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Service\SMSService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendSingleSMSNotification implements ShouldQueue
{
    use Queueable , Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
   
    

    public $tries = 3;      // retry 3 times max
    public $backoff = 60;   // wait 60 seconds before retry

    protected $contact;
    protected $message;

    public function __construct($contact, $message)
    {
        $this->contact = $contact;
        $this->message = $message;
    }

    public function handle(): void
    {
        try {
            $smsService = new SMSService();
            $smsService->sendSMS($this->contact, $this->message);
            Log::info("✅ SMS sent to {$this->contact}");
        } catch (\Throwable $e) {
            Log::error("❌ Failed sending SMS to {$this->contact}: " . $e->getMessage());
            throw $e; // let Laravel retry
        }
    }
}
