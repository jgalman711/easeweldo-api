<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\UserRequest;
use App\Models\User;

class RegisterController extends BaseController
{
    public function register(UserRequest $request)
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
        $success['name'] =  $user->fullName;
        return $this->sendResponse($success, 'User register successfully.');
    }
}
