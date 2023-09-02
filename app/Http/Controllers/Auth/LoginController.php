<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user()->load(['companies.companySubscriptions', 'employee', 'roles']);
            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            $success['user'] = $user;
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Incorrect email or password.');
        }
    }
}
