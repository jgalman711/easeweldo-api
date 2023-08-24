<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_address' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        $status = Password::sendResetLink([
            'email_address' => $request->email_address
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            $response = $this->sendResponse($status, 'Reset password link sent to your email.');
        } elseif ($status === Password::RESET_THROTTLED) {
            $response =$this->sendError('Please wait before trying again.');
        } else {
            $response = $this->sendError('Unable to send reset password link.');
        }
        return $response;
    }
}
