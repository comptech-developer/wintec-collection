<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SelecomWebhookController extends Controller
{
    //


    public function Paymentvalidation(Request $request):JsonResponse
    {

         return response()->json(
               [
            "reference"  => mt_rand(1000000000, 9999999999),
            "transid"    => Str::random(8),
            "resultcode" => "000",
            "result"     => "SUCCESS",
            "message"    => "successful",
            "data"       => [
                []
            ]
            ],200);

    }

    public function paymentNotification(Request $request):JsonResponse
    {

          return response()->json([
            "reference"  => mt_rand(1000000000, 9999999999),
            "transid"    => Str::random(8),
            "resultcode" => "000",
            "result"     => "SUCCESS",
            "message"    => "successful",
            "data"       => [
                []
            ]
            ], 200);

    }
}
