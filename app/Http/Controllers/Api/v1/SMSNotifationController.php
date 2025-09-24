<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Jobs\SendPaymentSMSNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SMSNotifationController extends Controller
{
    //

    public function sendNotication(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required|string',
            'amount'    => 'required|int',
            'phone'    => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        
        $validated = $validator->validated(); 

        try {
            SendPaymentSMSNotification::dispatch($validated['name'],$validated['amount'],$validated['phone']);
            return response()->json('success send to job',200);
        } catch (\Throwable $th) {
            report($th);
            return response()->json('Error :'. $th->getMessage(),500);
        }

      
        
    }
    
}
