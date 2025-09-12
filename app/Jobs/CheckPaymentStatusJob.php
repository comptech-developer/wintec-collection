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
    public function __construct(private SelcomService $selcomService)
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
            $q->whereNull('status')
              ->orWhere('status', 'pending');
        })
        ->whereNull('last_checked_at')
        ->get();

    foreach ($pendingPayments as $payment) {
        try {
           
            $payload = ['order_id'=>$payment->order_id];
            $response = $this->selcomService->orderStatus($payload);
            if (data_get($response,'success') ==200) {
                

                    $payment->update([
                        'payment_status' => strtolower(data_get($response,'response.data.payment_status')), // e.g. completed
                        'channel'        => strtolower(data_get($response,'response.data.channel')),
                        'selcom_reference'  =>strtolower(data_get($response,'response.data.reference')),
                        'selcom_transid' =>  strtolower(data_get($response,'response.data.transid')),
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
