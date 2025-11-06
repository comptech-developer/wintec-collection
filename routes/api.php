<?php

use App\Http\Controllers\Api\v1\ContributionReportController;
use App\Http\Controllers\Api\v1\SelcomPaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SelecomWebhookController;
use App\Http\Controllers\Api\v1\SMSNotifationController;

// Fallback route
Route::fallback(function () {
    return response()->json([
        'status'  => 'error',
        'message' => 'Endpoint not found'
    ], 404);
});

Route::prefix('v1/payment')->group(function () {
    Route::post('/validation', [SelecomWebhookController::class, 'paymentValidation']);
    Route::post('/notification', [SelecomWebhookController::class, 'paymentNotification']);
    Route::post('/callback', [SelecomWebhookController::class, 'selecomcallback']);
    Route::post('/checkout/order', [SelcomPaymentController::class, 'createOrder']);
    Route::post('/checkout/walletpush', [SelcomPaymentController::class, 'walletPush']);
    Route::post('/checkout/orderlist', [SelcomPaymentController::class, 'Orderlist']);
    Route::post('/checkout/orderstatus', [SelcomPaymentController::class, 'Orderstatus']);
});

Route::prefix('v1/sms')->group(function () {
  Route::post('/sendnotification',[SMSNotifationController::class, 'sendNotication']);
});

Route::prefix('v1/report')->group(function(){
Route::post('/reports/pdf', [ContributionReportController::class, 'generatePdfPost']);
});