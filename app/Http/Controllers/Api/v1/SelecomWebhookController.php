<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Waumin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class SelecomWebhookController extends Controller
{
    //


    public function Paymentvalidation(Request $request)
    {

        try {
            //TODO handle latter
        $validated = Validator::make($request->all(),[
        'operator'    => 'required|string|max:50',
        'transid'     => 'required|string|max:100',
        'reference'   => 'required|string|max:100',
        'utilityref'  => 'required|string|exists:student,refno',
        'amount'      => 'required|numeric|min:1',
        'msisdn'      => 'required|digits_between:10,15',
        ],[
           'utilityref.exists' => 'The provided utility reference does not exist in the system.'
        ]
        );
          
           //return validatoin error
            if ($validated->fails()) {
            return response()->json([
                "reference"  => $request->reference,
                "transid"    => $request->transid,
                "resultcode" => 422,
                "result"     => "FAILED",
                "message"    => "Validation error ",
                "errors"     => $validated->errors(),
                "data"       => []
            ], 422);
        }

        //fetch record and validate amount if required 
          $data = Waumin::where('refno',$request->utilityref)->first();
          //create
          $validator = $validated->validated(); 
        // Add additional fields
        $validator['user'] = $request->header('Authorization');
        $validator['request'] = json_encode($request->all());

        // Create record
          Payment::create($validator);
            return response()->json(
               [
            "reference"  => $request->reference,
            "transid"    => $request->transid,
            "resultcode" => "000",
            "result"     => "SUCCESS",
            "message"    => "successful",
            "name"=>$data->sname,
            "data"       => [
                [
                    'billing_name'=>$data->sname,
                    'billing_reference'=>$data->refno
                ]
            ]
            ],200);
        }
         catch (\Throwable $th) {
            //throw $th;
            report($th);
            return response()->json(
             [
            "reference"  => mt_rand(1000000000, 9999999999),
            "transid"    => Str::random(8),
            "resultcode" => 415,
            "result"     => "FAILED",
            "message"    => "Un expected error occured",
            "data"       => [
                []
            ]
            ],500);
        }

         

    }

    public function paymentNotification(Request $request)
    {

           try {
            //TODO handle latter
        $validated = Validator::make($request->all(),[
        'operator'    => 'required|string|max:50',
        'transid'     => 'required|string|exists:payments,transid',
        'reference'   => 'required|string|exists:payments,reference',
        'utilityref'  => 'required|string|exists:student,refno',
        'amount'      => 'required|numeric|min:1',
        'msisdn'      => 'required|digits_between:10,15',
        ],[
           'utilityref.exists' => 'The provided utility reference does not exist in the system.',
           'transid.exists' => 'The provided transid does not existing', 
         'reference.exists' => 'The provided reference does not existing',

        ],
        );
          
           //return validatoin error
            if ($validated->fails()) {
            return response()->json([
                "reference"  => $request->reference,
                "transid"    => $request->transid,
                "resultcode" => 422,
                "result"     => "FAILED",
                "message"    => "Validation error ",
                "errors"     => $validated->errors(),
                "data"       => []
            ], 422);
        }
        //fetch record and validate amount if required 
          $data = Waumin::where('refno',$request->utilityref)->first();
            return response()->json(
               [
            "reference"  => $request->reference,
            "transid"    => $request->transid,
            "resultcode" => "000",
            "result"     => "SUCCESS",
            "message"    => "successful",
            "name"=>$data->sname,
            "data"       => [
                [
                    'billing_name'=>$data->sname,
                    'billing_reference'=>$data->refno
                ]
            ]
            ],200);
        }
         catch (\Throwable $th) {
            //throw $th;
            report($th);
            return response()->json(
             [
            "reference"  => mt_rand(1000000000, 9999999999),
            "transid"    => Str::random(8),
            "resultcode" => 415,
            "result"     => "FAILED",
            "message"    => "Un expected error occured",
            "data"       => [
                []
            ]
            ],500);
        }

    }


    public function selecomcallback(Request $request)
    {
      
       try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'result'          => 'required|string',
                'resultcode'      => 'required|string',
                'order_id'        => 'required|string',
                'transid'         => 'required|string',
                'reference'       => 'required|string',
                'channel'         => 'required|string',
                'amount'          => 'required|numeric|min:1',
                'phone'           => 'required|string',
                'payment_status'  => 'required|string|in:COMPLETED,CANCELLED,PENDING,USERCANCELED',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Find payment by transid
            $payment = Payment::where('transid', $data['transid'])->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found for this transaction ID',
                ], 404);
            }

            // Map external status to internal
            $statusMap = [
                'COMPLETED'      => 'paid',
                'PENDING'        => 'pending',
                'CANCELLED'      => 'failed',
                'USERCANCELED'   => 'failed',
            ];

            //$payment->status = $statusMap[$data['payment_status']] ?? 'pending';
            //$payment->amount = $data['amount']; // update amount if needed
            $payment->payment_status = $data['payment_status'];
            $payment->selcom_reference = $data['reference'];
            $payment->channel   = $data['channel'];
            $payment->result = $data['result'];
            $payment->resultcode  = $data['resultcode'];
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data'    => $payment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while handling callback',
                'error'   => $e->getMessage(),
            ], 500);
        }
        
    }


     private function rules($request): array
    {
        $validated = $request->validate([
        'operator'    => 'required|string|max:50',
        'transid'     => 'required|string|max:100',
        'reference'   => 'required|string|max:100',
        'utilityref'  => 'required|string|max:100',
        'amount'      => 'required|numeric|min:1',
        'msisdn'      => 'required|digits_between:10,15',
    ]);
    return $validated;
    }
}
