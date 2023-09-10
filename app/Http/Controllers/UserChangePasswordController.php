<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserChangePasswordController extends Controller
{
    public function update(ChangePasswordRequest $request, User $user)
    {
        $request->validated();
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->sendError('The old password does not match your current password.');
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->sendResponse($user, 'Password changed successfully.');
    }
}
