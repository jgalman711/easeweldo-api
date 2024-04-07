<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;

class UpdateRoleController extends Controller
{
    public function __invoke(UpdateRoleRequest $request, Company $company)
    {
        $input = $request->validated();
        $employee = $company->employees()->with('user')->find($input['employee_id']);
        $employee->user->assignRole($input['role_name']);
        return $this->sendResponse(new EmployeeResource($employee), 'Employee role updated successfully.');
    }
}
