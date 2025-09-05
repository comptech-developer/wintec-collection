<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Log;

class LogOutgoingHttp
{
    /**
     * Create the event listener.
     */
    protected array $sensitive = [
        'pin', 'password', 'authorization', 'token', 'api_key', 'apiSecret'
    ];

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   public function handle(ResponseReceived $event)
    {
        $request = $event->request;
        $response = $event->response;

        // Mask sensitive fields in request body
        $body = $request->body();
        if ($this->isJson($body)) {
            $data = json_decode($body, true);
            foreach ($this->sensitive as $field) {
                if (isset($data[$field])) $data[$field] = '******';
            }
            //$body = json_encode($data);
            $body = $data;
        }

        // Mask sensitive fields in response
        $responseBody = $response->body();
        $responseBody2 = $response->body();
        if ($this->isJson($responseBody)) {
            $data = json_decode($responseBody, true);
            foreach ($this->sensitive as $field) {
                if (isset($data[$field])) $data[$field] = '******';
            }
            $responseBody = json_encode($data);
        }

        // Log everything
        Log::channel('api')->info('Outgoing HTTP Request', [
            'url'      => $request->url(),
            'method'   => $request->method(),
            'headers'  => $request->headers(),
            'body'     => $body,
            'status'   => $response->status(),
            'response' => json_decode($responseBody2),
        ]);
    }

    protected function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
