<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Service\SelcomService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckPaymentStatusJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
        
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $pendingPayments = Payment::where(function ($q) {
            $q->whereNull('payment_status')
              ->orWhere('payment_status', '')
              ->orWhere('payment_status', 'pending');
        })
        ->whereNull('last_checked_at')
        ->get();
      Log::info(count($pendingPayments));
    foreach ($pendingPayments as $payment) {
        try {
            $selcomService  =   new SelcomService();
            $payload = ['order_id'=>$payment->order_id];
            $response = $selcomService->orderStatus($payload);
            if (data_get($response,'success') ==200) {
                
                  
                    $payment->update([
                        'payment_status' => strtolower(data_get($response,'response.data.0.payment_status')), // e.g. completed
                        'channel'        => strtolower(data_get($response,'response.data.0.channel')),
                        'selcom_reference'  =>strtolower(data_get($response,'response.data.0.reference')),
                        'selcom_transid' =>  strtolower(data_get($response,'response.data.0.transid')),
                        'last_checked_at'=> now(),
                    ]);
 
               
            } else {
                Log::error("Payment API failed for order {$payment->order_id}", [
                    'status' => data_get($response,'status'),
                    'response'   => data_get($response,'response')
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error checking status for order {$payment->order_id}: " . $e->getMessage());
        }
    }
    }
}
