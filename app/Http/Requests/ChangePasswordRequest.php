<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        $user = self::retrieveUser();

        return [
            'old_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (self::checkOldPassword($user, $value)) {
                        $fail('The old and current password do not match.');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'The old password is required.',
            'old_password' => 'The old and current password do not match.',
            'password.required' => 'The new password is required.',
            'password.min' => 'The new password must be at least :min characters.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ];
    }

    private function checkOldPassword(User $user, string $value): bool
    {
        return
            ($user->temporary_password == null && ! Hash::check($value, $user->password)) ||
            ($user->temporary_password != null && $user->temporary_password != $value);
    }

    private function retrieveUser(): User
    {
        if ($this->employee instanceof Employee) {
            $employee = $this->employee;
        } elseif ($this->employee) {
            $employee = Employee::findOrFail($this->employee);
        } else {
            $employee = null;
        }

        return $employee->user;
    }
}
