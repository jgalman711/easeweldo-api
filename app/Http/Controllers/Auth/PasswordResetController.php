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

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset user's password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="token", type="string", description="Password reset token"),
     *                 @OA\Property(property="email_address", type="string", format="email", description="User's email address", maxLength=255),
     *                 @OA\Property(property="password", type="string", format="password", description="New password", minLength=6),
     *                 @OA\Property(property="password_confirmation", type="string", format="password", description="Password confirmation"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Password reset successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Password reset successfully."),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="404", description="Token or user not found"),
     * )
     */
    public function reset(PasswordResetRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email_address', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $response = $this->sendResponse($status, 'Password reset successfully.');
        } elseif($status === Password::INVALID_TOKEN) {
            $response = $this->sendError(self::INVALID_TOKEN_MESSAGE);
        } else {
            Log::info($status);
            $response = $this->sendError('Unable to reset password.');
        }
        return $response;
    }
}
