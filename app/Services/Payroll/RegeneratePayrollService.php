<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\HolidayRepository;
use App\Services\Contributions\ContributionsService;
use Exception;
use Illuminate\Support\Facades\DB;

class RegeneratePayrollService extends GeneratePayrollService
{
    public function __construct(ContributionsService $contributionsService, HolidayRepository $holidayRepository)
    {
        $this->contributionsService = $contributionsService;
        $this->holidayRepository = $holidayRepository;
    }

    public function regenerate(Payroll $payroll): Payroll
    {
        self::init($payroll);
        try {
            DB::beginTransaction();
            $this->calculateEarnings();
            $this->calculateHoliday();
            $this->calculateLeaves();
            $this->calculateContributions();
            $this->payroll->status = PayrollEnumerator::STATUS_TO_PAY;
            $this->payroll->save();
            DB::commit();
            return $this->payroll;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
    }

    protected function init(Payroll $payroll): void
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
            'dateTo' => $this->period->end_date
        ])->get();

        if (!$this->salaryComputation || !$this->companySettings) {
            $this->payroll->status = PayrollEnumerator::STATUS_FAILED;
            $this->payroll->save();
            throw new Exception("Payroll {$this->payroll->id} generation encountered an error.");
        }
    }
}
