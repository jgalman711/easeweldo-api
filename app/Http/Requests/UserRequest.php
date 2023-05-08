<?php

namespace App\Http\Requests;

class UserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => [
                'required',
                'regex:/^(09|\+639)\d{9}$/',
                'unique:users,mobile_number'
            ],
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6'
        ];
    }
}
