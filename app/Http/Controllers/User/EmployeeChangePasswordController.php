<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeChangePasswordController extends Controller
{
    public function update(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->temporary_password = null;
        $user->temporary_password_expires_at = null;
        $user->save();
        return $this->sendResponse($user, 'Password changed successfully.');
    }
}
