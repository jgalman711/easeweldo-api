<?php

namespace App\Services\Payroll;

use App\Enumerators\DisbursementEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class GenerateExtraPayrollService extends GeneratePayrollService
{
    protected $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function generate(Company $company, Period $period, Employee $employee): Payroll
    {
        parent::init($company, $employee, $period);
        try {
            DB::beginTransaction();
            $this->calculateBasicSalary();
            $this->calculateEarnings();
            $this->payroll->status = PayrollEnumerator::STATUS_TO_PAY;
            $this->payroll->save();
            DB::commit();
            return $this->payroll;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
    }

    protected function calculateBasicSalary(): void
    {
        if ($this->period->type == DisbursementEnumerator::TYPE_NTH_MONTH_PAY) {
            $payrollCollection = $this->payrollRepository->getEmployeePayrollsByDateRange($this->employee->id, [
                'start_date' => $this->period->start_date,
                'end_date' => $this->period->end_date
            ]);
            $startDate = Carbon::parse($this->period->start_date);
            $endDate = Carbon::parse($this->period->end_date);
            $months = $startDate->diffInMonths($endDate);
            $divisor  = $payrollCollection->count() / $months;
            $totalPaid = $payrollCollection->sum('total_basic_salary');
            $this->payroll->basic_salary = $divisor ? $totalPaid / $divisor : 0;
        }
    }
}
