<?php

namespace App\Services;

use App\Http\Requests\EmployeeRequest;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EmployeeService
{
    protected $employeeUploadPath;
    protected $userService;

    public function __construct()
    {
        $this->employeeUploadPath = config('app.uploads.employee_path');
        $this->userService = app()->make(UserService::class);
    }

    public function create(EmployeeRequest $request, Company $company): Employee
    {
        try {
            DB::beginTransaction();
            $input = $request->validated();
            $user = $this->userService->create($company, $input);
            $input['company_id'] = $company->id;
            $input['company_employee_id'] = $this->generateCompanyEmployeeId($company);
            $input['status'] = Employee::ACTIVE;
            if (isset($input['profile_picture']) && $input['profile_picture']) {
                $filename = time() . '.' . $request->profile_picture->extension();
                $request->profile_picture->storeAs($this->employeeUploadPath, $filename);
                $input['profile_picture'] = $filename;
            }
            $input['user_id'] = $user->id;
            $employee = Employee::create($input);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    public function quickCreate(Company $company, array $data): Employee
    {
        $data = [
            ...$data,
            'company_id' => $company->id,
            'company_employee_id' => $this->generateCompanyEmployeeId($company),
            'status' => $company->isInSettlementPeriod() ? Employee::PENDING : Employee::ACTIVE
        ];
        return Employee::create($data);
    }

    public function update(Request $request, Company $company, Employee $employee): Employee
    {
        $input = $request->all();
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            if ($employee->profile_picture) {
                $filePath = public_path("$this->employeeUploadPath/$employee->profile_picture");
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs($this->employeeUploadPath, $filename);
            $input['profile_picture'] = $filename;
        } else {
            unset($input['profile_picture']);
        }

        if ($company->isInSettlementPeriod()) {
            $input['status'] = $employee->status;
        }

        $employee->update($input);
        if ($employee->user) {
            $employee->user->update($input);
        }
        return $employee;
    }

    public function generateDashboardDetails(Company $company): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $employees = $company->employees->filter(function ($employee) use ($currentMonthStart) {
            return is_null($employee->date_of_termination)
                || $employee->date_of_termination > $currentMonthStart;
        });
        $typeCount = [];
        $typePercentage = [];
        foreach(Employee::EMPLOYMENT_TYPE as $type) {
            $count = $employees->where('employment_type', $type)->count();
            $typeCount[$type] = $count;
            $typePercentage[$type] = round(($count / $employees->count()) * 100, 2);
        }

        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEmployees = $company->employees->filter(function ($employee) use ($previousMonthStart) {
            return is_null($employee->date_of_termination)
                || $employee->date_of_termination > $previousMonthStart;
        });

        $retention = $this->generateRetentionRateByMonth($previousEmployees, $previousMonthStart);
        return [
            'active_employees' => $employees->count(),
            'employment_type_count' => $typeCount,
            'employment_type_percentatge' => $typePercentage,
            'retention' => $retention
        ];
    }

    public function generateRetentionRateByMonth(Collection $employees, string $month): array
    {
        $activeEmployeesBeforeMonthStart = $employees->filter(function ($employee) use ($month) {
            return ($employee->date_of_hire < $month) &&
                (is_null($employee->date_of_termination) || $employee->date_of_termination > $month);
        })->count();

        $terminatedEmployees = $employees->filter(function ($employee) use ($month) {
            return !is_null($employee->date_of_termination)
                && ($employee->date_of_termination >= $month)
                && ($employee->date_of_termination <= Carbon::now()->endOfMonth());
        })->count();

        $newlyHiredEmployees = $employees->count() - $activeEmployeesBeforeMonthStart;

        return [
            'active_employees_start_of_month' => $activeEmployeesBeforeMonthStart,
            'newly_hired_employees' => $newlyHiredEmployees,
            'terminated_employees' => $terminatedEmployees,
            'retention_rate' =>  $this->calculateRetentionRate(
                $activeEmployeesBeforeMonthStart,
                $terminatedEmployees
            ),
        ];
    }

    private function generateCompanyEmployeeId(Company $company): int
    {
        $latestEmployee = $company->employees()->orderByDesc('id')->first();
        return $latestEmployee ? $latestEmployee->company_employee_id + 1 : 1;
    }

    private function calculateRetentionRate(int $active, int $terminated)
    {
        return $active ? round((($active - $terminated) / $active) * 100, 2) : null;
    }
}
