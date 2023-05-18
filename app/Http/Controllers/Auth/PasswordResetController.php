<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends BaseController
{
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if (Auth::attempt(['mobile_number' => $request->mobile_number, 'password' => $request->old_password])) {
            $user = Auth::user();
            $user->forceFill([
                'password' => Hash::make($request->new_password)
            ])->save();
            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            return $this->sendResponse($success, 'Password reset successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
