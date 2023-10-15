<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalLoginController extends LoginController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        try {
            list($success, $message) = $this->loginService->login($credentials, $this->loginService::TYPE_PERSONAL);
            return $this->sendResponse($success, $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
