<?php

namespace App\Services;

use App\Http\Requests\EmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserEmployeeService
{
    protected $employeeService;

    protected $userService;

    public function __construct(EmployeeService $employeeService, UserService $userService)
    {
        $this->employeeService = $employeeService;
        $this->userService = $userService;
    }

    public function create(Company $company, array $employeesData): array
    {
        try {
            if (isset($employeesData['user_id'])) {
                $user = $this->userService->getExistingUser($employeesData['user_id']);
            } else {
                $companies = new Collection([$company]);
                $user = $this->userService->create($companies, $employeesData);
            }
            if (isset($employeesData['profile_picture']) && $employeesData['profile_picture']) {
                $filename = time() . '.' . $employeesData['profile_picture']->extension();
                $employeesData['profile_picture']->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
                $employeesData['profile_picture'] = Employee::STORAGE_PATH . $filename;
            }
            $employeesData['user_id'] = $user->id;
            $employee = $this->employeeService->create($company, $employeesData);
            return [$employee, $user];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function bulk(Company $company, array $employeesData): array
    {
        $employees = [];
        $errors = [];
        foreach ($employeesData as $key => $employeeRow) {
            if ($key == 0) { continue; }
            try {
                $employeeData = [
                    'first_name' => $employeeRow[0],
                    'last_name' => $employeeRow[1],
                    'email_address' => $employeeRow[2],
                    'department' => $employeeRow[3],
                    'job_title' => $employeeRow[4],
                    'employment_status' => $employeeRow[5],
                    'employment_type' => $employeeRow[6],
                    'mobile_number' => $employeeRow[7],
                    'address_line' => $employeeRow[8],
                    'barangay_town_city_province' => $employeeRow[9],
                    'date_of_hire' => $employeeRow[10],
                    'date_of_birth' => $employeeRow[11],
                    'sss_number' => $employeeRow[12],
                    'pagibig_number' => $employeeRow[13],
                    'philhealth_number' => $employeeRow[14],
                    'tax_identification_number' => $employeeRow[15],
                    'bank_name' => $employeeRow[16],
                    'bank_account_name' => $employeeRow[17],
                    'bank_account_number' => $employeeRow[18],
                ];
                $employeeRequest = new EmployeeRequest;
                $validator = Validator::make($employeeData, $employeeRequest->rules());
                if ($validator->fails()) {
                    $errors[] = $validator->messages()->all();
                    continue;
                }
                $employees[] = self::create($company, $employeeData);
            } catch (Exception $e) {
                $errors[] = [
                    'error' => $e->getMessage(),
                    'data' => $employeeData
                ];
            }
        }
        return [$employees, $errors];
    }
}
