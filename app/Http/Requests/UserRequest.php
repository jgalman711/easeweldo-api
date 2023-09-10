<?php

namespace App\Http\Requests;

class UserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email_address' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ];
    }
}
