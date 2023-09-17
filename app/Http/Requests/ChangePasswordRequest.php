<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user->password)) {
                        $fail('The old password does not match your current password.');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
        ];
    }
}
