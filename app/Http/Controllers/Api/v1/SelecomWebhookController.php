<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SelecomWebhookController extends Controller
{
    //


    public function Paymentvalidation(Request $request):JsonResponse
    {

          return response()->json(['status' => true], 200);

    }

    public function paymentNotification(Request $request):JsonResponse
    {

          return response()->json(['status' => true], 200);

    }
}
