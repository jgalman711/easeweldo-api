<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public const LOGIN_TYPES = [
        self::TYPE_BUSINESS,
        self::TYPE_PERSONAL
    ];
    public const TYPE_BUSINESS = 'business';
    public const TYPE_PERSONAL = 'personal';

    public function login(array $credentials, ?bool $remember = false): User
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user()->load([
                'companies.subscriptions',
                'companies.companySubscriptions',
                'employee',
                'roles'
            ]);

            $user->token = $user->createToken(env('APP_NAME'))->plainTextToken;
            throw_if($this->isTemporaryPasswordExpired($user), new Exception('Your temporary password has expired.'));
         
            return $user;
        } else {
            throw new Exception('Incorrect email or password.');
        }
    }

    public function getSuccessMessage(User $user): string
    {
        $message = 'User login successfully.';
        if ($this->hasTemporaryPassword($user)) {
            $message .= " Please go to your profile and change your temporary password.";
        }
        return $message;
    }

    protected function hasTemporaryPassword(User $user): bool
    {
        return $user->temporary_password && $user->temporary_password_expires_at;
    }

    protected function isTemporaryPasswordExpired(User $user): bool
    {
        return $this->hasTemporaryPassword($user) && now()->gt($user->temporary_password_expires_at);
    }
}
