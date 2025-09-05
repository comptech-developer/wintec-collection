<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
 

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     // List of sensitive fields to mask
    protected $sensitive = [
        'password',
        'password_confirmation',
        'token',
        'card_number',
        'cvv',
        'secret',
    ];

    public function handle(Request $request, Closure $next): Response
    {
             // Mask sensitive request data
        $requestData = $request->all();
        foreach ($this->sensitive as $field) {
            if (isset($requestData[$field])) {
                $requestData[$field] = '******';
            }
        }
        
         
          // Log incoming request
        Log::channel('api')->info('Incoming API Request', [
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body'    => $requestData,
        ]);

        // Get response
        $response = $next($request);

        // Optionally, mask sensitive fields in response too
        $responseContent = $response->getContent();
        $responseContent2 = $response->getContent();
        // Simple masking example (if JSON)
        if ($this->isJson($responseContent)) {
            $responseData = json_decode($responseContent, true);
            foreach ($this->sensitive as $field) {
                if (isset($responseData[$field])) {
                    $responseData[$field] = '******';
                }
            }
            $responseContent = json_encode($responseData);
        }

        // Log outgoing response
        Log::channel('api')->info('Outgoing API Response', [
            'status' => $response->getStatusCode(),
            'body'   => json_decode($responseContent2)
            
        ]);

        return $response;

       //return $next($request);
    }

     protected function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    

}
