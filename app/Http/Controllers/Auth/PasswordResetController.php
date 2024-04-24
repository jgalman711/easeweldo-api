<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public const INVALID_TOKEN_MESSAGE = 'Token is invalid.';

    public function index(Request $request): JsonResponse
    {
        $token = DB::table('password_reset_tokens')
            ->where('email', $request->email_address)
            ->first();
        if ($token) {
            return $this->sendResponse($token, 'Reset password token retrieved successfullly.');
        }

        return $this->sendError(self::INVALID_TOKEN_MESSAGE);
    }

    public function reset(PasswordResetRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email_address', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $response = $this->sendResponse($status, 'Password reset successfully.');
        } elseif ($status === Password::INVALID_TOKEN) {
            Log::info($status);
            $response = $this->sendError(self::INVALID_TOKEN_MESSAGE);
        } else {
            Log::info($status);
            $response = $this->sendError('Unable to reset password.');
        }

        return $response;
    }
}
