<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryComputationRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\SalaryComputation;
use Illuminate\Http\JsonResponse;

class SalaryComputationController extends Controller
{
    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(
            new BaseResource($employee->salaryComputation),
            'Salary computation retrieved successfully.'
        );
    }

    public function store(SalaryComputationRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $company->getEmployeeById($employeeId);
        $input = $request->validated();
        $input['employee_id'] = $employeeId;
        $salaryComputation = SalaryComputation::create($input);
        return $this->sendResponse(
            new BaseResource($salaryComputation),
            'Salary computation created successfully.'
        );
    }

    public function update(SalaryComputationRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $input = $request->validated();
        $salaryComputation = $employee->salaryComputation;
        if (!$salaryComputation) {
            return $this->sendError('Salary computation not found');
        }
        $salaryComputation->update($input);
        return $this->sendResponse(
            new BaseResource($salaryComputation),
            'Salary computation created successfully.'
        );
    }

    public function delete(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $salaryComputation = $employee->salaryComputation;
        if (!$salaryComputation) {
            return $this->sendError('Salary computation not found');
        }
        $salaryComputation->delete();
        return $this->sendResponse(
            new BaseResource($employee->salaryComputation),
            'Salary computation deleted successfully.'
        );
    }
}
