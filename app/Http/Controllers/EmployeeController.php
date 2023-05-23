<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }
    
    public function index(Company $company): JsonResponse
    {
        $employees = $company->employees()->paginate(10);
        return $this->sendResponse(BaseResource::collection($employees), 'Employees retrieved successfully.');
    }

    public function store(EmployeeRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $employee = $this->employeeService->create($input);
        $message = $this->employeeService->getEmployeeCreationMessage();
        return $this->sendResponse(new BaseResource($employee), $message);
    }

    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(new BaseResource($employee), 'Employee retrieved successfully.');
    }

    public function update(EmployeeRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $input = $request->validated();
        $employee->update($input);
        return $this->sendResponse(new BaseResource($employee), 'Employee updated successfully.');
    }

    public function destroy(Company $company, int $employeeId)
    {
        $employee = $company->getEmployeeById($employeeId);
        $employee->company_id = null;
        $employee->save();
        return $this->sendResponse(new BaseResource($employee), 'Employee deleted successfully.');
    }
}
