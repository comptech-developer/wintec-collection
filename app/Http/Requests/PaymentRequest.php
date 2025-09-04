<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       // return true;
       return $this->hasValidApiToken();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        'operator'    => 'required|string|max:50',
        'transid'     => 'required|string|max:100',
        'reference'   => 'required|string|max:100',
        'utilityref'  => 'required|string|max:100',
        'amount'      => 'required|numeric|min:1',
        'msisdn'      => 'required|digits_between:10,15',
        ];
    }

    private function hasValidApiToken(): bool
{
    // Uses Laravel’s built-in API guard (like Sanctum or Passport)
    return auth('api')->check();
}
}
