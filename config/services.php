<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
     'app_url'=> [
          'selcom' =>env('SELECOM_SERVICE','https://apigw.selcommobile.com/v1'),
          'api_key'=>env('API_KEY','TILL61207959-c5b789e6d083498797edfad0ee45f81f'),
          'secret_key'=>env('API_SECRET','2a1068-8d2e28-4d52b0-130f72-8a9ee6-3d'),
          'vendor_id'=>env('VENDORID','TILL61207959')
     ]   
    ],

];
