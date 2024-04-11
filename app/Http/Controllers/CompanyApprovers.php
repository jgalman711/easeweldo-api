<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApprovers extends Controller
{
    public function index(Company $company)
    {
        $users = $company->users()->role('approver')->get();
        return $this->sendResponse(BaseResource::collection($users), "Approvers retrieved successfully.");
    }

    public function store(Request $request, Company $company)
    {
        $employee = $company->employees()->with('user')->findOrFail($request->employee_id);
        $employee->user->assignRole('approver');
        return $this->sendResponse(new EmployeeResource($employee), "Employee updated role successfully. ");
    }
}
