<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyEmployeeController extends Controller
{
    protected $employeeService;

    protected $userService;

    public function __construct(
        EmployeeService $employeeService,
        UserService $userService,
    ) {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
        $this->setCacheIdentifier('employees');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        // Todo: must fix this circular relationship with the company
        $builder = $company->employees()->with([
            'company',
            'user.roles',
            'salaryComputation',
            'employeeSchedules' => function ($query) {
                $query->latest('start_date')->limit(1);
            }
        ]);
        $employees = $this->applyFilters($request, $builder, [
            'user.first_name',
            'user.last_name',
            'user.full_name',
            'user.role',
            'job_title',
            'employment_status',
            'department',
        ]);
        return $this->sendResponse(EmployeeResource::collection($employees), 'Employees retrieved successfully.');
    }

    public function store(EmployeeRequest $request, Company $company): JsonResponse
    {
        try {
            $request->validated();
            $employee = $this->employeeService->create($request, $company);
            return $this->sendResponse(new EmployeeResource($employee), 'Employee created successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);

        if ($employee) {
            $employee->load([
                'company',
                'supervisor',
                'user',
                'salaryComputation',
                'employeeSchedules' => function ($query) {
                    $query->latest('start_date')->limit(1);
                }
            ]);
            return $this->sendResponse(new EmployeeResource($employee), 'Employee retrieved successfully.');
        }
        return $this->sendError("Employee not found.");
    }

    public function update(Request $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $employee = $company->getEmployeeById($employeeId);
            $employee = $this->employeeService->update($request, $company, $employee);
            DB::commit();
            $this->forget($company, $employee->id);

            return $this->sendResponse(new EmployeeResource($employee), 'Employee updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, Employee $employee): JsonResponse
    {
        $company->getEmployeeById($employee->id);
        $employee->delete();
        $this->forget($company, $employee->id);

        return $this->sendResponse(new EmployeeResource($employee), 'Employee deleted successfully.');
    }

    public function all(Request $request): JsonResponse
    {
        $employees = $this->applyFilters($request, Employee::with(['company:id,name,slug,status']), [
            'first_name',
            'last_name',
            'employment_status',
            'company.name',
        ]);

        return $this->sendResponse(EmployeeResource::collection($employees), 'Employees retrieved successfully.');
    }
}
