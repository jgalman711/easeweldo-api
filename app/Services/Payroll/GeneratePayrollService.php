<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Contributions\ContributionsService;
use Exception;

class GeneratePayrollService
{
    protected const CYCLE_DIVISOR = [
        Period::TYPE_WEEKLY => 4,
        Period::TYPE_SEMI_MONTHLY => 2,
        Period::TYPE_MONTHLY => 1,
    ];

    protected $contributionsService;

    private $payroll;
    private $company;
    private $employee;
    private $period;
    private $schedules;
    private $timesheet;
    private $salaryComputation;
    private $companySettings;

    public function __construct(ContributionsService $contributionsService)
    {
        $this->contributionsService = $contributionsService;
    }

    public function generate(Company $company, Period $period, Employee $employee): Payroll
    {
        self::init($company, $employee, $period);
        $data = [
            'payroll_number' => $this->generatePayrollNumber(),
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'type' => PayrollEnumerator::TYPE_REGULAR,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'description' => "Payroll for {$period->salary_date}",
            'basic_salary' => $this->salaryComputation->basic_salary / self::CYCLE_DIVISOR[$period->type],
            'pay_date' => $period->salary_date,
            'period_cycle' => $this->period->type,
            'taxable_earnings' => $this->salaryComputation->taxable_earnings,
            'non_taxable_earnings' => $this->salaryComputation->non_taxable_earnings,
        ];
        $this->payroll = new Payroll($data);
        $this->calculateHoliday($period);
        $this->calculateLeaves();
        $this->calculateContributions();
        $this->payroll->save();
        return $this->payroll;
    }

    protected function calculateHoliday(): void
    {
        if (!$this->schedules || !$this->timesheet) { return; }
        $holidays = Holiday::whereBetween('date', [
            $this->period->start_date,
            $this->period->end_date
        ])->get(); // can be refactored to store in cache
        if (!$holidays) { return; }

        $payrollHolidays = [];
        foreach ($holidays as $type => $holiday) {
            $filteredCollection = $this->timesheet->where(function ($item) use ($holiday) {
                $expectedClockInDate = substr($item['expected_clock_in'], 0, 10);
                return $expectedClockInDate == $holiday->date;
            });
            if ($filteredCollection->isEmpty()) { return; }
            $payrollHolidays[$type] = [
                'hours' => $this->salaryComputation->working_hours_per_day,
                'hours_pay' => $this->salaryComputation->working_hours_per_day * $this->salaryComputation->hourly_rate,
            ];
        }
        $this->payroll->holidays = $payrollHolidays;
    }

    protected function calculateLeaves(): void
    {
        $leaves = $this->payroll->employee->leaves()->where([
            ['date', '>=', $this->payroll->period->start_date],
            ['date', '<=', $this->payroll->period->end_date]
        ])->get();

        $leavePay = 0;
        if ($leaves->isNotEmpty()) {
            foreach ($leaves as $leave) {
                $pay = $leave->hours * $this->salaryComputation->hourly_rate;
                $transformedLeaves[] = [
                    'type' => $leave->type,
                    'date' => $leave->date,
                    'hours' => $leave->hours,
                    'pay' => $pay
                ];
                $leavePay += $pay;
            }
            $this->payroll->leaves = $transformedLeaves;
            $this->payroll->leaves_pay = $leavePay;
        }
    }

    protected function calculateContributions(): void
    {
        $this->payroll->sss_contributions = $this->contributionsService
            ->sssCalculatorService
            ->compute($this->payroll->gross_income);
        $this->payroll->pagibig_contributions = $this->contributionsService
            ->pagIbigCalculatorService
            ->compute($this->payroll->gross_income);
        $this->payroll->philhealth_contributions = $this->contributionsService
            ->philHealthCalculatorService
            ->compute($this->payroll->gross_income);
        $this->payroll->withheld_tax = $this->contributionsService
            ->taxCalculatorService
            ->compute($this->payroll->gross_income, $this->period->type);
    }

    private function generatePayrollNumber(): string
    {
        $companyInitials = substr(str_replace(' ', '', strtoupper($this->company->name)), 0, 3);
        $employeeId = str_pad($this->employee->id, 5, '0', STR_PAD_LEFT);
        $periodId = str_pad($this->period->id, 5, '0', STR_PAD_LEFT);
        $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $companyInitials . date('Ymd') . '-' . $periodId . $employeeId . '-' . $randomNumber;
    }

    private function init(Company $company, Employee $employee, Period $period): void
    {
        $this->company = $company;
        $this->employee = $employee;
        $this->period = $period;
        $this->salaryComputation = $employee->salaryComputation;
        $this->schedules = $employee->schedules;
        $this->companySettings = $company->setting;
        $this->timesheet = $employee->timeRecords()->byRange([
            'dateFrom' => $period->start_date,
            'dateTo' => $period->end_date
        ])->get();

        throw_unless(
            $this->salaryComputation,
            new Exception("Salary computation data of employee {$employee->fullName} (ID:$employee->id) not found.")
        );

        throw_unless(
            $this->companySettings,
            new Exception("Company settings not available.")
        );
    }
}
