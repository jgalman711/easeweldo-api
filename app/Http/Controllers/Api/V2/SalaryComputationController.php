<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\SalaryComputationResource;
use App\Models\Company;
use App\Models\Employee;

class SalaryComputationController extends Controller
{
    public function index(Company $company, Employee $employee)
    {
        $salaryComputation = $employee->salaryComputation;
        $salaryComputation->setRelation('employee', $employee);
        $salaryComputation->employee->setRelation('company', $company);
        return $this->sendResponse(new SalaryComputationResource($salaryComputation), 'Employee salary computations retrieved successfully.');
    }
}
