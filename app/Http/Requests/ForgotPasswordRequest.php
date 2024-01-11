<?php

namespace App\Http\Requests;

class ForgotPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email_address' => 'required'
        ];
    }
}
