<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproverRequest;
use App\Http\Resources\UserResource;
use App\Models\Company;
use Exception;
use Illuminate\Support\Facades\Log;

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
            Log::info($company->users->pluck('id'));
            $user = $company->users()->findOrFail($request->user_id);
            if ($request->has('role_name') && $request->role_name) {
                $user->syncRoles($request->role_name);
            } else {
                $user->roles()->detach();
            }
            return $this->sendResponse(new UserResource($user), "User role updated successfully.");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError("Unable to update role.");
        }
    }
}
