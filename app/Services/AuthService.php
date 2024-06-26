<?php

namespace App\Services;

use App\Mail\ResetTemporaryPassword;
use App\Models\User;
use App\Traits\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use Password;

    public function changePassword(User &$user, array $data): void
    {
        $user->password = Hash::make($data['password']);
        $user->temporary_password = null;
        $user->temporary_password_expires_at = null;
        $user->save();
    }

    public function temporaryPasswordReset(User $user): User
    {
        $user->temporary_password = $this->generateTemporaryPassword();
        $user->temporary_password_expires_at = now()->addMinutes(60);
        $user->save();
        Mail::to($user->email_address)->send(new ResetTemporaryPassword($user));
        return $user;
    }
}
