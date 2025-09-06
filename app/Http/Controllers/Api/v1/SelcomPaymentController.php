<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Service\SelcomService;
use Exception;
use Illuminate\Support\Str;

class SelcomPaymentController extends Controller
{
    //
public function createOrder(Request $request, SelcomService $selcom)
    {
        try {
            //  Validate request
            $validator = Validator::make($request->all(), [
                'order_id'         => 'required|string|max:100',
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Payload
            $payload = $validator->validated();

            //Call service
            $response = $selcom->createOrder($payload);

            return response()->json($response, $response['success'] ? 200 : 400);

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
                'amount'           => 'required|numeric|min:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Payload
            $payload = $validator->validated();
            $payload['transid'] = Str::orderedUuid();

            //Call service
            $response = $selcom->confirmOrder($payload);

            return response()->json($response, $response['success'] ? 200 : 400);

        } catch (Exception $e) {
            //  Catch any unexpected error
            return response()->json([
                'success' => false,
                'message' => 'Order payment failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }

    }


}
