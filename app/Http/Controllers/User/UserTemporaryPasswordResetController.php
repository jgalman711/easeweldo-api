<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;

class UserTemporaryPasswordResetController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function update(User $user)
    {
        $user = $this->userService->temporaryPasswordReset($user);
        return $this->sendResponse($user, "User's temporary password: {$user->temporary_password}. It expires after an hour. Please change it upon login.");
    }
}
