<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ];
    }
}
