<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    protected const PUBLIC_PATH = 'public/';

    public function create(Company $company, array $data): Employee
    {
        try {
            DB::beginTransaction();
            $data['company_id'] = $company->id;
            $data['company_employee_id'] = $this->generateCompanyEmployeeId($company);
            $data['status'] = $company->isInSettlementPeriod() ? Employee::PENDING : Employee::ACTIVE;
            $employee = Employee::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    public function update(Request $request, Company $company, Employee $employee): Employee
    {
        $input = $request->all();
        if (isset($input['profile_picture']) && $input['profile_picture']) {
            if ($employee->profile_picture) {
                Storage::delete(self::PUBLIC_PATH . $employee->profile_picture);
            }
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs(Employee::ABSOLUTE_STORAGE_PATH, $filename);
            $input['profile_picture'] = Employee::STORAGE_PATH . $filename;
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
            $typePercentage[$type] = number_format($count / $employees->count() * 100, 2);
        }

        $retention = $this->generateRetentionRateByMonth($employees, $currentMonthStart);
        return [
            'active' => $employees->count(),
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
        });
        $terminatedEmployees = $employees->filter(function ($employee) use ($month) {
            return !is_null($employee->date_of_termination)
                && ($employee->date_of_termination >= $month)
                && ($employee->date_of_termination <= Carbon::now()->endOfMonth());
        });

        return [
            'active_employees_start_of_month' => $activeEmployeesBeforeMonthStart->count(),
            'terminated_employees' => $terminatedEmployees->count(),
            'retention_rate' =>  $this->calculateRetentionRate(
                $activeEmployeesBeforeMonthStart->count(),
                $terminatedEmployees->count()
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
        return number_format((($active - $terminated) / $active) * 100, 2);
    }
}
