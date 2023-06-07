<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class PasswordResetController extends BaseController
{
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:username',
            'username' => 'required_without:email',
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $credentials = $request->only(['username', 'email', 'password']);
        $credentials['password'] = $request->old_password;
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->forceFill([
                'password' => Hash::make($request->new_password)
            ])->save();
            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            PersonalAccessToken::where('tokenable_id', $user->id)
                ->where('tokenable_type', get_class($user))
                ->delete();
            return $this->sendResponse($success, 'Password reset successfully.');
        } else {
            return $this->sendError('Unauthorised.', [
                'error' => 'The provided username/email does not match the old password.'
            ]);
        }
    }
}
