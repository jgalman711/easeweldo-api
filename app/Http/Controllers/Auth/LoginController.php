<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user()->load(['companies.companySubscriptions', 'employee', 'roles']);

            if ($this->isTemporaryPasswordExpired($user)) {
                return $this->sendError('Your temporary password has expired.');
            }

            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            $success['user'] = $user;
            $message = 'User login successfully.';

            if ($this->hasTemporaryPassword($user)) {
                $message .= " Please go to your profile and change your temporary password.";
            }
            return $this->sendResponse($success, $message);
        } else {
            return $this->sendError('Incorrect email or password.');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return $this->sendResponse($user, 'User successfully logged out.');
    }

    private function hasTemporaryPassword(User $user): bool
    {
        return $user->temporary_password && $user->temporary_password_expires_at;
    }

    private function isTemporaryPasswordExpired(User $user): bool
    {
        return $this->hasTemporaryPassword($user) && now()->gt($user->temporary_password_expires_at);
    }
}
