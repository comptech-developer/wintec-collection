<?php

namespace App\Jobs;

use App\Models\Waumin;
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
class SendMonthlySMSNotification implements ShouldQueue
{
    use Queueable,Dispatchable, InteractsWithQueue, Queueable, SerializesModels;



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
        try {
            Log::info('-----Monthly SMS Job start--------');
           $smsService = new SMSService();
           $watumishi = DB::table('student')->get();
           foreach ($watumishi as $key => $value) {
            $smsService->sendSMS($value->contact,$this->smsTemplate($value->sname));
           }
        } catch (\Throwable $th) {
            report($th);
            Log::error($th->getMessage());
        }
    }

    private function  smsTemplate($name)
    {

       $template = "Ndugu {name}, Baba paroko anakukumbusha kulipa zaka yako ya mwezi wa {month} mapema. Mtolee Mungu zaka yako, naye atakubariki wewe na uzao wako; Amina";
       $message = strtr($template, [
        '{name}'   => Str::title($name),
        '{month}' => $this->monthlyMapping(),
          ]);
     
      return $message;

    }

    private function monthlyMapping($month=null)
    {

         $months = 
         [
            'December' =>'kumi na mbili',
            'November' =>'kumi na moja',
            'October' => 'kumi',
            'September' =>'tisa',
            'August' => 'nane',
            'July' => 'saba',
            'June' =>'sita',
            'May' =>'tano',
            'April' =>'nne',
            'March' =>'tatu',
            'February' =>'pili',
            'January' =>'kwanza'
         ];

         $currentMonth = Carbon::now()->format('F'); 
         $swahiliMonth = $months[$currentMonth] ?? $currentMonth;
         return $swahiliMonth;
    }
}
