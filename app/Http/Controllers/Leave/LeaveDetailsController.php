<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveDetailsResource;
use App\Models\Company;
use App\Models\Employee;

class LeaveDetailsController extends Controller
{
    public function __invoke(Company $company, Employee $employee)
    {
        return $this->sendResponse(new LeaveDetailsResource($employee), "Leave details retrieved successfully.");
    }
}
