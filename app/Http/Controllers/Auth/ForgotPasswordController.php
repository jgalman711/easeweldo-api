<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/forgot-password",
     *     summary="Send password reset email",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(
     *                      property="email_address",
     *                      type="string", format="email", description="User's email address", maxLength=255),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Password reset email sent successfully",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Password reset email sent successfully."),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="404", description="User not found"),
     * )
     */
    public function forgot(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email_address'));
        if ($status === Password::RESET_LINK_SENT) {
            $response = $this->sendResponse($status, 'Reset password link sent to your email.');
        } elseif ($status === Password::RESET_THROTTLED) {
            $response = $this->sendError('Please wait before trying again.');
        } else {
            $response = $this->sendError('Unable to send reset password link.');
        }

        return $response;
    }
}
