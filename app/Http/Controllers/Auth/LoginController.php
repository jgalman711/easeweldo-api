<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'email_address', 'password']);
        try {
            list($success, $message) = $this->loginService->login($credentials, $this->loginService::TYPE_BUSINESS);
            return $this->sendResponse($success, $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return $this->sendResponse($user, 'User successfully logged out.');
    }
}
