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
    ],

     'selcom_service'=> [
          'selcom_url' =>env('SELCOM_SERVICE','https://apigw.selcommobile.com/v1'),
          'selcom_api_key'=>env('SELCOM_API_KEY','TILL61207959-c5b789e6d083498797edfad0ee45f81f'),
          'selcom_secret_key'=>env('SELCOM_API_SECRET','2a1068-8d2e28-4d52b0-130f72-8a9ee6-3d'),
          'selcom_vendor_id'=>env('SELCOM_VENDORID','TILL61207959'),
          'selcom_access_token' => env('ACCESS_TOKEN', 'BwYQ1QrevN38R6cMQZyo8xFWFBKXLnBxzh77MSf8rbYMvwEZLlDZLrXYDTM1KFXJ'),

     ],
     'sms_service' => [
        'sms_url' => env('SMS_URL','https://messaging-service.co.tz/api/sms/v1/text/single'),
        'sms_apikey' => env('SMS_APIKEY','winfrid'),
        'sms_secret' => env('SMS_SECRET','WinGene@2025'),
        'sms_senderid' =>env('SMS_SENDERID','KIDIMPARISH'),
     ]
    

];
