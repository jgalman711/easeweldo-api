<?php

namespace App\Services\Payroll;

use App\Enumerators\LeaveEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\Exceptions\InvalidPayrollGenerationException;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\HolidayRepository;
use App\Services\Contributions\ContributionsService;
use Carbon\Carbon;
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
        try {
            $this->init($company, $employee, $period);
            DB::beginTransaction();
            $this->calculateBasicSalary();
            $this->calculateEarnings();
            $this->calculateDeductions();
            $this->calculateHoliday();
            $this->calculateAttendanceEarnings();
            $this->calculateLeaves();
            $this->calculateContributions();
            $this->payroll->save();
            DB::commit();

            return $this->payroll;
        } catch (Exception $e) {
            DB::rollBack();
            $this->payroll->status = PayrollEnumerator::STATUS_FAILED;
            $this->payroll->error = $e->getMessage();
            $this->payroll->save();
            throw new InvalidPayrollGenerationException($e);
        }
    }

    protected function init(Company $company, Employee $employee, Period $period): void
    {
        $this->company = $company;
        $this->employee = $employee;
        $this->period = $period;

        if (! $this->period->start_date) {
            $this->period->start_date = $employee->date_of_hire
                ? Carbon::parse($employee->date_of_hire)
                : $employee->created_at;
        }

        throw_if($this->period->type == Period::TYPE_SPECIAL,
            new InvalidPayrollGenerationException("Unable to auto-generate disbursement type {$this->period->type}")
        );
        $this->salaryComputation = $employee->salaryComputation;
        $this->schedules = $employee->schedules;
        $this->companySettings = $company->setting;

        $this->payroll = Payroll::create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'type' => $period->type,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'description' => $period->description ?? "Payroll for {$period->salary_date}",
            'pay_date' => $period->salary_date,
            'period_cycle' => $period->type,
        ]);

        $this->payroll->payroll_number = $this->generatePayrollNumber();

        if (! $this->salaryComputation) {
            throw new InvalidPayrollGenerationException(PayrollEnumerator::ERROR_NO_SALARY_DATA);
        } elseif (! $this->companySettings) {
            throw new InvalidPayrollGenerationException(PayrollEnumerator::ERROR_NO_COMPANY_SETTINGS);
        }

        $this->salaryComputation->is_clock_required = true;
        if ($this->salaryComputation->is_clock_required) {
            $this->timesheet = $employee->timeRecords()->byRange([
                'dateFrom' => $period->start_date,
                'dateTo' => $period->end_date,
            ])->get();
        }
    }

    protected function calculateEarnings(): void
    {
        $this->payroll->taxable_earnings = $this->salaryComputation->taxable_earnings;
        $this->payroll->non_taxable_earnings = $this->salaryComputation->non_taxable_earnings;
    }

    protected function calculateDeductions(): void
    {
        $this->payroll->other_deductions = $this->salaryComputation->other_deductions;
    }

    protected function calculateAttendanceEarnings(): void
    {
        $earnings = [];
        foreach ($this->timesheet as $record) {
            $expectedClockIn = Carbon::parse($record->expected_clock_in);
            $expectedClockOut = Carbon::parse($record->expected_clock_out);
            $date = $expectedClockIn->format('Y-m-d');
            if ($record->clock_in && $record->clock_out) {
                $clockIn = Carbon::parse($record->clock_in);
                $clockOut = Carbon::parse($record->clock_out);
                if ($this->isLate($clockIn, $expectedClockIn)) {
                    $lateHours = round($clockIn->diffInMinutes($expectedClockIn) / 60, 2);
                    $earnings[PayrollEnumerator::LATE][] = [
                        'date' => $date,
                        'hours' => $lateHours,
                        'rate' => 1,
                        'amount' => round($lateHours * $this->salaryComputation->hourly_rate, 2),
                    ];
                }
                if ($clockOut->lt($expectedClockOut)) {
                    $undertimeHours = round($clockOut->diffInMinutes($expectedClockOut) / 60, 2);
                    $earnings[PayrollEnumerator::UNDERTIME][] = [
                        'date' => $date,
                        'hours' => $undertimeHours,
                        'rate' => 1,
                        'amount' => round($undertimeHours * $this->salaryComputation->hourly_rate, 2),
                    ];
                }
                if ($this->isOvertime($clockOut, $expectedClockOut)) {
                    $rate = $this->salaryComputation->overtime_rate;
                    $overtimeHours = round($clockOut->diffInMinutes($expectedClockOut) / 60, 2);
                    $earnings[PayrollEnumerator::OVERTIME][] = [
                        'date' => $date,
                        'hours' => $overtimeHours,
                        'rate' => $rate,
                        'amount' => round($overtimeHours * $rate * $this->salaryComputation->hourly_rate, 2),
                    ];
                }
            } else {
                $absentHours = $expectedClockIn->diffInHours($expectedClockOut);
                $earnings[PayrollEnumerator::ABSENT][] = [
                    'date' => $date,
                    'rate' => 1,
                    'hours' => $absentHours,
                    'amount' => round($absentHours * $this->salaryComputation->hourly_rate, 2),
                ];
            }
        }
        $this->payroll->attendance_earnings = empty($earnings) ? null : $earnings;
    }

    protected function calculateBasicSalary(): void
    {
        if ($this->salaryComputation->is_clock_required &&
            $this->period->type == PayrollEnumerator::TYPE_FINAL
        ) {
            $daysCount = $this->timesheet->count();
            $this->payroll->basic_salary = $daysCount * $this->salaryComputation->daily_rate;
        } elseif ($this->period->type == PayrollEnumerator::TYPE_REGULAR ||
            $this->period->type == PayrollEnumerator::TYPE_FINAL
        ) {
            $this->payroll->basic_salary = $this->salaryComputation->basic_salary
                / self::CYCLE_DIVISOR[$this->period->subtype];
        } else {
            throw new InvalidPayrollGenerationException('Invalid payroll type.');
        }
    }

    protected function calculateHoliday(): void
    {
        if (! $this->schedules || ! $this->timesheet) {
            return;
        }
        $holidays = $this->holidayRepository->getHolidaysForPeriod($this->period->start_date, $this->period->end_date);
        if (! $holidays) {
            return;
        }
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

            if ($holidayTimesheet->isEmpty()) {
                continue;
            }
            $hours = $this->salaryComputation->working_hours_per_day;
            $hoursAmount = $this->salaryComputation->working_hours_per_day * $this->salaryComputation->hourly_rate;
            $payrollHolidays[$holiday->simplified_type][] = [
                'date' => $holiday->date,
                'hours' => $hours,
                'amount' => $hoursAmount,
                'rate' => $this->salaryComputation->{$holiday->simplified_type}.'_holiday_rate',
            ];

            $daySchedule = $holidayTimesheet->first();

            if (! $daySchedule->clock_in) {
                $absents[PayrollEnumerator::ABSENT][] = [
                    'date' => $holiday->date,
                    'hours' => $hours,
                    'amount' => $hoursAmount,
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
            ['date', '>=', $this->period->start_date],
            ['date', '<=', $this->period->end_date],
            ['status', '=', LeaveEnumerator::APPROVED],
        ])->get();

        if ($leaves->isNotEmpty()) {
            foreach ($leaves as $leave) {
                $pay = $leave->hours * $this->salaryComputation->hourly_rate;
                $transformedLeaves[$leave->type][] = [
                    'date' => $leave->date,
                    'hours' => $leave->hours,
                    'amount' => $pay,
                ];
            }
            $this->payroll->leaves = $transformedLeaves;
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
            ->compute($this->payroll->taxable_income, $this->period->subtype);
    }

    private function generatePayrollNumber(): string
    {
        $companyInitials = substr(str_replace(' ', '', strtoupper($this->company->name)), 0, 3);
        $employeeId = str_pad($this->employee->id, 5, '0', STR_PAD_LEFT);
        $periodId = str_pad($this->period->id, 5, '0', STR_PAD_LEFT);
        $lastNumber = str_pad($this->payroll->id, 7, '0', STR_PAD_LEFT);

        return $companyInitials.date('Ymd').'-'.$periodId.$employeeId.'-'.$lastNumber;
    }

    private function isLate(Carbon $clockIn, Carbon $expectedClockIn): bool
    {
        return $clockIn->gt($expectedClockIn)
            && $clockIn->diffInMinutes($expectedClockIn) > $this->companySettings->grace_period;
    }

    private function isOvertime(Carbon $clockOut, Carbon $expectedClockOut): bool
    {
        return $this->companySettings->is_ot_auto_approve
            && $clockOut->gt($expectedClockOut)
            && $clockOut->diffInMinutes($expectedClockOut) > $this->companySettings->minimum_overtime;
    }
}
