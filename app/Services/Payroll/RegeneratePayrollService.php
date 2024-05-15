<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Exceptions\InvalidPayrollGenerationException;
use App\Models\Payroll;
use App\Models\Period;
use Exception;
use Illuminate\Support\Facades\DB;

class RegeneratePayrollService extends GeneratePayrollService
{
    public function regenerate(Payroll $payroll): Payroll
    {
        try {
            $this->initByPayroll($payroll);
            DB::beginTransaction();
            $this->calculateEarnings();
            $this->calculateDeductions();
            $this->calculateHoliday();
            $this->calculateLeaves();
            $this->calculateContributions();
            $this->payroll->status = PayrollEnumerator::STATUS_TO_PAY;
            $this->payroll->save();
            DB::commit();

            return $this->payroll;
        } catch (Exception $e) {
            DB::rollBack();
            $this->payroll->status = PayrollEnumerator::STATUS_FAILED;
            $this->payroll->error = $e->getMessage();
            $this->payroll->save();
            throw new InvalidPayrollGenerationException($e->getMessage());
        }
    }

    protected function initByPayroll(Payroll $payroll): void
    {
        $this->payroll = $payroll;
        $this->period = $payroll->period;
        $this->employee = $payroll->employee;
        $this->company = $this->employee->company;

        throw_unless($this->period->type == Period::TYPE_REGULAR,
            new Exception("Unable to auto-generate disbursement type {$this->period->type}")
        );
        $this->salaryComputation = $this->employee->salaryComputation;
        $this->schedules = $this->employee->schedules;
        $this->companySettings = $this->company->setting;

        $this->timesheet = $this->employee->timeRecords()->byRange([
            'dateFrom' => $this->period->start_date,
            'dateTo' => $this->period->end_date,
        ])->get();

        if (! $this->salaryComputation) {
            throw new InvalidPayrollGenerationException(PayrollEnumerator::ERROR_NO_SALARY_DATA);
        } elseif (! $this->companySettings) {
            throw new InvalidPayrollGenerationException(PayrollEnumerator::ERROR_NO_COMPANY_SETTINGS);
        }
    }
}
