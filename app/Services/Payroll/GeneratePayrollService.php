<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\HolidayRepository;
use App\Services\Contributions\ContributionsService;
use Exception;
use Illuminate\Support\Facades\DB;

class GeneratePayrollService
{
    protected const CYCLE_DIVISOR = [
        Period::SUBTYPE_WEEKLY => 4,
        Period::SUBTYPE_SEMI_MONTHLY => 2,
        Period::SUBTYPE_MONTHLY => 1,
    ];

    protected $contributionsService;
    protected $holidayRepository;

    protected $payroll;
    protected $company;
    protected $employee;
    protected $period;
    protected $schedules;
    protected $timesheet;
    protected $salaryComputation;
    protected $companySettings;

    public function __construct(ContributionsService $contributionsService, HolidayRepository $holidayRepository)
    {
        $this->contributionsService = $contributionsService;
        $this->holidayRepository = $holidayRepository;
    }

    public function generate(Company $company, Period $period, Employee $employee): Payroll
    {
        self::init($company, $employee, $period);
        try {
            DB::beginTransaction();
            $this->calculateBasicSalary();
            $this->calculateEarnings();
            $this->calculateHoliday();
            $this->calculateLeaves();
            $this->calculateContributions();
            $this->payroll->save();
            DB::commit();
            return $this->payroll;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }
    }

    protected function init(Company $company, Employee $employee, Period $period): void
    {
        $this->company = $company;
        $this->employee = $employee;
        $this->period = $period;

        throw_if($this->period->type == Period::TYPE_SPECIAL,
            new Exception("Unable to auto-generate disbursement type {$this->period->type}")
        );
        $this->salaryComputation = $employee->salaryComputation;
        $this->schedules = $employee->schedules;
        $this->companySettings = $company->setting;

        $this->payroll = Payroll::create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'type' => PayrollEnumerator::TYPE_REGULAR,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'description' => $period->description ?? "Payroll for {$period->salary_date}",
            'pay_date' => $period->salary_date,
            'period_cycle' => $period->type
        ]);

        $this->timesheet = $employee->timeRecords()->byRange([
            'dateFrom' => $period->start_date,
            'dateTo' => $period->end_date
        ])->get();
        $this->payroll->payroll_number = $this->generatePayrollNumber();

        if (!$this->salaryComputation) {
            $this->payroll->status = PayrollEnumerator::STATUS_FAILED;
            $this->payroll->save();
            throw new Exception(
                "Payroll {$this->payroll->id} generation encountered an error. No salary data found."
            );
        } elseif (!$this->companySettings) {
            $this->payroll->status = PayrollEnumerator::STATUS_FAILED;
            $this->payroll->save();
            throw new Exception(
                "Payroll {$this->payroll->id} generation encountered an error. No company settings found."
            );
        }
    }

    protected function calculateEarnings(): void
    {
        $this->payroll->taxable_earnings = $this->salaryComputation->taxable_earnings;
        $this->payroll->non_taxable_earnings = $this->salaryComputation->non_taxable_earnings;
    }

    protected function calculateBasicSalary(): void
    {
        if ($this->period->type == PayrollEnumerator::TYPE_REGULAR) {
            $this->payroll->basic_salary = $this->salaryComputation->basic_salary
                / self::CYCLE_DIVISOR[$this->period->subtype];
        }
    }

    protected function calculateHoliday(): void
    {
        if (!$this->schedules || !$this->timesheet) { return; }
        $holidays = $this->holidayRepository->getHolidaysForPeriod($this->period->start_date, $this->period->end_date);
        if (!$holidays) { return; }
        $payrollHolidays = [];
        $payrollHolidaysWorked = [];
        $absents = [];
        $payrollHolidays = [];
        foreach ($holidays as $holiday) {
            $holidayTimesheet = $this->timesheet->where(function ($item) use ($holiday) {
                $expectedClockInDate = substr($item['expected_clock_in'], 0, 10);
                $clockInDate = substr($item['clock_in'], 0, 10);
                return $expectedClockInDate == $holiday->date || $clockInDate == $holiday->date;
            });

            if ($holidayTimesheet->isEmpty()) { continue; }
            $hours = $this->salaryComputation->working_hours_per_day;
            $hoursAmount = $this->salaryComputation->working_hours_per_day * $this->salaryComputation->hourly_rate;
            $payrollHolidays[$holiday->simplified_type][] = [
                'date' => $holiday->date,
                'hours' =>  $hours,
                'amount' => $hoursAmount,
            ];

            $daySchedule = $holidayTimesheet->first();

            if (!$daySchedule->clock_in) {
                $absents[PayrollEnumerator::ABSENT][] = [
                    'date' => $holiday->date,
                    'hours' => $hours,
                    'amount' => $hoursAmount * -1,
                ];
            }
        }
        $this->payroll->attendance_earnings = empty($absents) ? null : $absents;
        $this->payroll->holidays = empty($payrollHolidays) ? null : $payrollHolidays;
        $this->payroll->holidays_worked = empty($payrollHolidaysWorked) ? null : $payrollHolidaysWorked;
    }

    protected function calculateLeaves(): void
    {
        $leaves = $this->payroll->employee->leaves()->where([
            ['date', '>=', $this->payroll->period->start_date],
            ['date', '<=', $this->payroll->period->end_date],
            ['status', '=' , Leave::APPROVED]
        ])->get();

        $leavePay = 0;
        if ($leaves->isNotEmpty()) {
            foreach ($leaves as $leave) {
                $pay = $leave->hours * $this->salaryComputation->hourly_rate;
                $transformedLeaves[$leave->type][] = [
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
        $lastNumber = str_pad($this->payroll->id, 7, '0', STR_PAD_LEFT);
        return $companyInitials . date('Ymd') . '-' . $periodId . $employeeId . '-' . $lastNumber;
    }
}
