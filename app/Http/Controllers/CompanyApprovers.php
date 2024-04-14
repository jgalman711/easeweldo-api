<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproverRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use Exception;

class CompanyApprovers extends Controller
{
    public function index(Company $company)
    {
        $users = $company->users()->role('approver')->get();
        return $this->sendResponse(UserResource::collection($users), "Approvers retrieved successfully.");
    }

    public function store(ApproverRequest $request, Company $company)
    {
        try {
            $user = $company->users()->findOrFail($request->user_id);
            if ($request->has('role_name') && $request->role_name) {
                $user->assignRole('approver');
            } else {
                foreach ($user->roles as $role) {
                    $user->removeRole($role);
                }
            }
            return $this->sendResponse(new UserResource($user), "User role updated successfully.");
        } catch (Exception $_ENV) {
            return $this->sendError("Unable to update role.");
        }
    }
}
