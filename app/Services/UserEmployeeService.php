<?php

namespace App\Services;

use App\Http\Requests\EmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;

class UserEmployeeService
{
    protected $employeeService;

    protected $userService;

    public function __construct(EmployeeService $employeeService, UserService $userService)
    {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
    }

    public function create(EmployeeRequest $request, Company $company): array
    {
        $input = $request->validated();
        if ($request->has('user_id')) {
            $user = $this->userService->getExistingUser($input);
        } else {
            $companies = new Collection([$company]);
            $user = $this->userService->create($companies, $input);
        }
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
            $input['profile_picture'] = Employee::STORAGE_PATH . $filename;
        }
        $input['user_id'] = $user->id;
        $employee = $this->employeeService->create($company, $input);
        return [$employee, $user];
    }
}
