<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserService;
use App\Traits\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EmployeeController extends Controller
{
    use Filter;

    protected $employeeService;

    protected $userService;

    public function __construct(EmployeeService $employeeService, UserService $userService)
    {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
    }
    
    public function index(Request $request, Company $company): JsonResponse
    {
        $employees = $this->applyFilters($request, $company->employees(), [
            'first_name',
            'last_name',
            'job_title',
            'employment_status',
            'department'
        ]);
        return $this->sendResponse($employees, 'Employees retrieved successfully.');
    }

    public function store(EmployeeRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
            $input['profile_picture'] = Employee::STORAGE_PATH . $filename;
        }
        $employee = $this->employeeService->create($input);
        $message = $this->employeeService->getEmployeeTemporaryCredentials();
        return $this->sendResponse(new BaseResource($employee), $message);
    }

    public function show(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(new BaseResource($employee), 'Employee retrieved successfully.');
    }

    public function update(EmployeeRequest $request, Company $company, Employee $employee): JsonResponse
    {
        $company->getEmployeeById($employee->id);
        $input = $request->validated();
        if ($request->has('reset_password') && $request->reset_password) {
            $temporaryPassword = $this->userService->employeeResetPassword($employee->user);
            PersonalAccessToken::where('tokenable_id', $employee->user->id)
                ->where('tokenable_type', get_class($employee->user))
                ->delete();
            return $this->sendResponse(
                new BaseResource($employee),
                'Employee password reset successfully. Temporary password: ' . $temporaryPassword
            );
        }
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
            $input['profile_picture'] = Employee::STORAGE_PATH . $filename;
        } else {
            unset($input['profile_picture']);
        }
        $employee->update($input);
        if ($request->has('email_address')) {
            $employee->user->update([
                'email_address' => $request->email_address
            ]);
        }
        return $this->sendResponse(new BaseResource($employee), 'Employee updated successfully.');
    }

    public function destroy(Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $employee->delete();
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
