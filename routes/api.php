<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\SelecomWebhookController;


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
});

