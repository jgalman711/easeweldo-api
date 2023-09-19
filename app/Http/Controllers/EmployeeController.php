<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserEmployeeService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $employeeService;

    protected $userService;

    protected $userEmployeeService;

    public function __construct(
        EmployeeService $employeeService,
        UserService $userService,
        UserEmployeeService $userEmployeeService
    ) {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
        $this->userEmployeeService = $userEmployeeService;
        $this->setCacheIdentifier('employees');
    }
    
    public function index(Request $request, Company $company): JsonResponse
    {
        $this->forget($company);
        $employees = $this->applyFilters($request, $company->employees()->with('user'), [
            'user.first_name',
            'user.last_name',
            'job_title',
            'employment_status',
            'department'
        ]);
        return $this->sendResponse(BaseResource::collection($employees), 'Employees retrieved successfully.');
    }

    public function store(EmployeeRequest $request, Company $company): JsonResponse
    {
        try {
            DB::beginTransaction();
            list($employee) = $this->userEmployeeService->create($request, $company);
            $this->forget($company);
            DB::commit();
            return $this->sendResponse(new BaseResource($employee), "Employee created successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $this->remember($company, function () use ($company, $employeeId) {
            return $company->getEmployeeById($employeeId);
        }, $employeeId);
        return $this->sendResponse(new BaseResource($employee), 'Employee retrieved successfully.');
    }

    public function update(EmployeeRequest $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $employee = $company->getEmployeeById($employeeId);
            $employee = $this->employeeService->update($request, $company, $employee);
            $this->forget($company, $employee->id);
            return $this->sendResponse(new BaseResource($employee), 'Employee updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $employee->delete();
        $this->forget($company, $employee->id);
        return $this->sendResponse(new BaseResource($employee), 'Employee deleted successfully.');
    }

    public function all(Request $request): JsonResponse
    {
        $employees = $this->applyFilters($request, Employee::with(['company:id,name,slug,status']), [
            'first_name',
            'last_name',
            'employment_status',
            'company.name'
        ]);
        return $this->sendResponse($employees, 'Employees retrieved successfully.');
    }
}
