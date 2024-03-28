<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Resources\LoginResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalLoginController extends AuthController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        try {
            $user = $this->loginService->login($credentials, $this->loginService::TYPE_PERSONAL);
            $message = $this->loginService->getSuccessMessage($user);

            return $this->sendResponse(new LoginResource($user), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
