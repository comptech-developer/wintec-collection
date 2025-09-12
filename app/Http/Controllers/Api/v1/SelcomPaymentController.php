<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Service\SelcomService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SelcomPaymentController extends Controller
{
    //
public function createOrder(Request $request, SelcomService $selcom)
    {
        try {
            //  Validate request
            $validator = Validator::make($request->all(), [
                'order_id'         => 'required|string',
                'buyer_email'      => 'required|email',
                'buyer_name'       => 'required|string|max:100',
                'buyer_phone'      => 'required|string|max:20',
                'amount'           => 'required|numeric|min:200',
                'currency'         => 'required|string|size:3',
                'buyer_user_id'    => 'nullable|string|max:100',
                'payment_methods'  => 'nullable|string',
                'payer_remarks'    => 'nullable|string',
                'merchant_remarks' => 'nullable|string',
                'order_items'      => 'nullable',
                'no_of_items'      => 'required|integer|min:1',
                'webhook'          => 'nullable|url', // optional
            ],[
                'order_id.exists' => 'The provided reference does not exist on our records.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Payload
            $payload = $validator->validated();
            $payload['webhook'] = base64_encode(config('app.url').'/api/v1/payment/callback');
            //Call service
            $response = $selcom->createOrder($payload);

            //log the request on db
            //generate transid and ref
            $transid = $this->generateTransId();
            $reference = $this->generateReference();
            $payload['transid'] = $transid;
            $payload['reference'] = $reference;
               
            $this->savepayment($payload,$response);
            $response['billref'] = [
                'transid'  => $transid,
                'reference' => $reference,
                'order_id' => $request->order_id
            ];
            return response()->json($response,$response['success'] ? 200 : 400);

        } catch (Exception $e) {
            //  Catch any unexpected error
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

     public function walletpush(Request $request, SelcomService $selcom)
    {

        try {
            //  Validate request
            $validator = Validator::make($request->all(), [
                'order_id'         => 'required|string|max:100',
                'msisdn'      =>      'required|string|max:20',
               // 'amount'           => 'required|numeric|min:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Payload
            $payload = $validator->validated();
            $payload['transid'] = $this->generateTransId();

            //Call service
            $response = $selcom->confirmOrder($payload);

            return response()->json($response,$response['success'] ? 200 : 400);

        } catch (Exception $e) {
            //  Catch any unexpected error
            return response()->json([
                'success' => false,
                'message' => 'Order payment failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

    }

    private function generateTransId()
    {
   
        $transid = 'TXN' . str_replace('-', '', (string) Str::ulid());
        return $transid;

    }

    private function generateReference()
    {
       $reference = 'REF' . now()->format('YmdHis') . Str::upper(Str::random(6));
       return $reference;
        
    }


    public function savepayment($request,$response)
    {
        $originalRefno = explode('-', data_get($request,'order_id'))[0];
        $data = [
            'operator'          => 'KIDIMU',
            'transid'           => data_get($request,'transid'),
            'order_id'          => data_get($request,'order_id'),
            'reference'         => data_get($request,'reference'),
            'utilityref'        => $originalRefno,
            'amount'            => data_get($request,'amount'),
            'msisdn'            => data_get($request,'buyer_phone'),
            'vendor'            => env('SELCOM_VENDORID'),
            'phonenumber'       => data_get($request,'buyer_phone'),
            'user'              => env('ACCESS_TOKEN'),
            'email'             => data_get($request,'buyer_email','winfrid31@gmail.com'),
            'name'              => data_get($request,'buyer_name'),
            'currency'          =>data_get($request,'currency'),
            'webhookurl'        =>data_get($request,'webhook'),
            'buyer_remark'      => data_get($request,'payer_remarks'),
            'merchant_remark'   => data_get($request,'merchant_remarks'),
            'no_of_items'       => data_get($request,'no_of_items'),
            'resultcode'        => data_get($response,'response.stdClass.resultcode'),
            'result'            => data_get($response,'response.stdClass.result'),
            'message'           => data_get($response,'response.stdClass.message'),
            'response_success'  => data_get($response,'success'),
            'response_status'   => data_get($response,'status'),
            'selcom_reference'  =>data_get($response,'response.stdClass.message'),
            'payment_token'     => data_get($response,'response.data.payment_token'),
            'payment_gateway_url'=> data_get($response,'response.data.payment_gateway_url'),
            'channel'           => null,
            'request'           => json_encode($request, JSON_PRETTY_PRINT),
            'response'          => is_array($response) || is_object($response) ? json_encode($response) : $response,
        ];
        Payment::create($data);
        return true;
        
    }

    public function Orderlist(Request $request,SelcomService $selcom)
    {

         try {
            $validator = Validator::make($request->all(), [
                'fromdate'         => 'required|date',
                'todate'      =>      'required|date',
            ]);
            $payload = $validator->validate();
            $response = $selcom->orderList($payload);
            return response()->json($response,$response['success'] ? 200 : 400);
         } catch (\Throwable $th) {
            //throw $th;
            report($th);
            return response()->json([
                'success' => false,
                'message' => 'Order payment failed.',
                'error'   => $th->getMessage(),
            ], 500);
         }

    }

    public function Orderstatus(Request $request,SelcomService $selcom)
    {

         try {
            $validator = Validator::make($request->all(), [
                'order_id'         => 'required|string'
            ]);
            $payload = $validator->validate();
            $response = $selcom->orderStatus($payload);
            return response()->json($response,$response['success'] ? 200 : 400);
         } catch (\Throwable $th) {
            //throw $th;
            report($th);
            return response()->json([
                'success' => false,
                'message' => 'Order payment failed.',
                'error'   => $th->getMessage(),
            ], 500);
         }

    }

}
