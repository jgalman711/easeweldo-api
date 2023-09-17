<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserChangePasswordController extends Controller
{
    public function update(ChangePasswordRequest $request, User $user)
    {
        $request->validated();
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->sendResponse($user, 'Password changed successfully.');
    }
}
