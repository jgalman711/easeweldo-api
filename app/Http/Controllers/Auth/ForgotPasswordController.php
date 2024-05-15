<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email_address'));
        if ($status === Password::RESET_LINK_SENT) {
            $response = $this->sendMessage('Reset password link sent to your email.');
        } elseif ($status === Password::RESET_THROTTLED) {
            $response = $this->sendError('Please wait before trying again.');
        } else {
            $response = $this->sendError('Unable to send reset password link.');
        }

        return $response;
    }
}
