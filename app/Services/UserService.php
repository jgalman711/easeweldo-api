<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    private const PASSWORD_LENGTH = 6;

    public function employeeResetPassword(User $user): string
    {
        $temporaryPassword = Str::random(self::PASSWORD_LENGTH);
        $user->password = bcrypt($temporaryPassword);
        $user->save();
        return $temporaryPassword;
    }
}
