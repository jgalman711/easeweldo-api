<?php

namespace App\Strategies\Payroll;

use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\Payroll\PayrollService;
use Exception;
use Illuminate\Support\Facades\DB;

class RegularPayrollStrategy implements PayrollStrategy
{
    protected $payrollService;

    public function __construct()
    {
        $this->payrollService = app()->make(PayrollService::class);
    }

    public function generate($employees, $period): array
    {
        $payrolls = [];
        $errors = [];
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                $payroll = $this->payrollService->generate($period, $employee);
                $payroll->makeHidden('employee');
                $payroll->makeHidden('period');
                $payrolls[] = $payroll;
                DB::commit();
            } catch (Exception $e) {
                $errors[] = [
                    'employee_id' => $employee->id,
                    'employee_full_name' => $employee->fullName,
                    'error' => $e->getMessage()
                ];
                DB::rollBack();
            }
        }
        return [$payrolls, $errors];
    }

    public function regenerate(Payroll $payroll, array $data): Payroll
    {
        $payroll = $this->payrollService->update($payroll, $data);
        $payroll->makeHidden('employee');
        $payroll->makeHidden('period');
        return $payroll;
    }

    public function getEmployees(Company $company, array $data = null)
    {
        return $company->employees()->where('status', Employee::ACTIVE)->get();
    }
}
