<?php

namespace App\Jobs;

use App\Service\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;



class SendPaymentSMSNotification implements ShouldQueue
{
    use Queueable ,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mtumishi;
    public $amount;
    public $phone;
    /**
     * Create a new job instance.
     */
    public function __construct($mtumishi,$amount,$phone)
    {
        //
        $this->mtumishi = $mtumishi;
        $this->amount  = $amount;
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {
           $smsService = new SMSService();
           $response = $smsService->sendSMS($this->phone,$this->smsTemplate());
           Log::info($response->json());
        } catch (\Throwable $th) {
            //throw $th;
            report($th);
            Log::error($th->getMessage());
        }
    }


    private function  smsTemplate()
    {
        
       $template = "Ndugu {name} Baba Paroko anakushukuru na anakujulisha kuwa, kanisa limepokea zaka yako ya Tsh {amount}. Mwenyezi Mungu azidi kukubariki.";
       $message = strtr($template, [
        '{name}'   => Str::title($this->mtumishi),
        '{amount}' => number_format($this->amount, 2),
          ]);
     
      return $message;

    }
}
