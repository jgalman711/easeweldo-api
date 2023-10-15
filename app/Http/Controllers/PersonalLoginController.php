<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalLoginController extends LoginController
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
}
