<?php

namespace App\Http\Requests;

class PasswordResetRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email_address' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ];
    }
}
