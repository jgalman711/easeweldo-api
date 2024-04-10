<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Models\User;

class CompanyApprovers extends Controller
{
    public function __invoke(Company $company)
    {
        $all_users_with_all_their_roles = User::role('leave-approver')->get();

        dd($all_users_with_all_their_roles);
        return $this->sendResponse(EmployeeResource::collection($employees), 'Company approvers retrieved successfully.');
    }
}
