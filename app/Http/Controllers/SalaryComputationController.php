<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryComputationRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\SalaryComputationService;
use Illuminate\Http\JsonResponse;

class SalaryComputationController extends Controller
{
    protected $salaryComputationService;

    public function __construct(SalaryComputationService $salaryComputationService)
    {
        $this->salaryComputationService = $salaryComputationService;
    }

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
        $employee = $company->getEmployeeById($employeeId);
        if ($employee->salaryComputation) {
            return $this->sendError("Salary computation for employee already exists.");
        }
        $input = $request->validated();
        $input['employee_id'] = $employeeId;
        $salaryComputation = $this->salaryComputationService->initialize($employee, $input);
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
            $salaryComputation = $this->salaryComputationService->initialize($employee, $input);
        } else {
            $salaryComputation->update($input);
        }
        return $this->sendResponse(
            new BaseResource($salaryComputation),
            'Salary computation updated successfully.'
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
