<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\Auth;

class VerifyTokenController extends Controller
{
    public function verify()
    {
        $user = Auth::user();
        if ($user) {
            $user = $user->load(['companies.companySubscriptions', 'employee', 'roles']);
            return $this->sendResponse(new BaseResource($user), 'Token is valid');
        } else {
            return $this->sendError('Token is not valid');
        }
    }
}
