<?php

use App\Notifications\SlackErrorNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-slack', function () {
    // This will trigger your global exception handler
    throw new \Exception("This is a test exception for Slack notification!");
     
});