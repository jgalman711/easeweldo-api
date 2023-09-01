<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Contributions\PagIbigService;
use App\Services\Contributions\PhilHealthService;
use App\Services\Contributions\SSSService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;

class PayrollService
{
    public const FREQUENCY_SEMI_MONTHLY = 2;

    public const FREQUENCY_WEEKLY = 4.33;

    protected const CYCLE_DIVISOR = [
        Period::TYPE_WEEKLY => 4,
        Period::TYPE_SEMI_MONTHLY => 2,
        Period::TYPE_MONTHLY => 1,
    ];

    protected const TOTAL_PREFIX = "total_";

    protected const SIXTY_MINUTES = 60;

    protected $attendanceService;

    protected $timeRecordService;

    protected $pagIbigService;

    protected $philHealthService;

    protected $sssService;

    protected $taxService;

    protected $leaveService;

    protected $salaryData;

    protected $data;

    protected $company;

    protected $settings;

    protected $timesheet;

    protected $holidays;

    public function __construct(
        AttendanceService $attendanceService,
        TimeRecordService $timeRecordService,
        PagIbigService $pagIbigService,
        PhilHealthService $philHealthService,
        SSSService $sssService,
        TaxService $taxService,
        LeaveService $leaveService
    ) {
        $this->attendanceService = $attendanceService;
        $this->timeRecordService = $timeRecordService;
        $this->pagIbigService = $pagIbigService;
        $this->philHealthService = $philHealthService;
        $this->sssService = $sssService;
        $this->taxService = $taxService;
        $this->leaveService = $leaveService;
    }

    public function initializePayroll(Employee $employee, Period $period): Payroll
    {
        $this->company = $employee->company;
        $this->settings = $employee->company->setting;
        $this->salaryData = $employee->salaryComputation;
        $this->holidays = Holiday::whereBetween('date', [
            $period->start_date,
            $period->end_date
        ])->get();

        throw_unless($this->company, new Exception("Company not found."));

        throw_unless(
            $this->salaryData,
            new Exception("Salary computation data of employee {$employee->fullName} (ID:$employee->id) not found.")
        );

        throw_unless(
            $this->company->hasCoreSubscription,
            new Exception("Company is not subscribed.")
        );

        return Payroll::firstOrCreate(['period_id' => $period->id, 'employee_id' => $employee->id]);
    }

    public function generate(Period $period, Employee $employee): Payroll
    {
        $payroll = $this->initializePayroll($employee, $period);
        $payroll->basic_salary = $this->salaryData->basic_salary / self::CYCLE_DIVISOR[$this->settings->period_cycle];
        if ($this->company->hasTimeAndAttendanceSubscription) {
            $this->timeRecordService->setExpectedScheduleByPeriod($employee, $period);
            $timesheet = $this->getTimesheet($employee, $period);
            $payroll = self::calculateAttendanceRecords($payroll, $timesheet);
        }
        $payroll = self::calculateHolidayPay($payroll, $timesheet);
        $payroll = self::calculateLeaves($payroll);
        $payroll = self::calculateContributions($payroll);
        $payroll = self::calculateOtherEarnings($payroll);
        $payroll = self::calculateWithheldTax($payroll);
        $payroll->save();
        return $payroll;
    }

    public function calculateAttendanceRecords(Payroll $payroll, Collection $timesheet): Payroll
    {
        if (!$this->company->hasTimeAndAttendanceSubscription) {
            return $payroll;
        }

        $absentMinutes = 0;
        $lateMinutes = 0;
        $underMinutes = 0;
        $overtimeMinutes = 0;
        $minutesWorked = 0;
        $hourlyRate = $this->salaryData->hourly_rate;
        $working_hours_per_day = $this->salaryData->working_hours_per_day;

        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidaysHours[$type] = 0;
            $holidaysHoursWorked[$type] = 0;
        }

        foreach ($timesheet as $record) {
            $expectedClockIn = $record->expected_clock_in ? Carbon::parse($record->expected_clock_in) : null;
            $expectedClockOut = $record->expected_clock_out ? Carbon::parse($record->expected_clock_out) : null;
            $clockIn = Carbon::parse($record->clock_in);
            $clockOut = Carbon::parse($record->clock_out);
            $minutesWorked += $clockIn->diffInMinutes($clockOut);

            if (!$record->clock_in && !$record->clock_out) {
                $absentMinutes += $this->attendanceService->calculateAbsences($working_hours_per_day);
            } else {
                if ($expectedClockIn && $expectedClockOut) {
                    $lateMinutes += $this->attendanceService->calculateLates($clockIn, $expectedClockIn);
                    $underMinutes += $this->attendanceService->calculateUndertimes($clockOut, $expectedClockOut);
                    if ($expectedClockOut) {
                        $expectedClockOut->addMinutes($this->settings->minimum_overtime);
                    }
                    $overtimeMinutes += $this->attendanceService->calculateOvertime($clockOut, $expectedClockOut);
                }
            }
        }

        $payroll->absent_minutes = $absentMinutes;
        $payroll->absent_deductions = $this->attendanceService->formatHourly($absentMinutes, $hourlyRate);

        $payroll->late_minutes = $lateMinutes;
        $payroll->late_deductions = $this->attendanceService->formatHourly($lateMinutes, $hourlyRate);

        $payroll->undertime_minutes = $underMinutes;
        $payroll->undertime_deductions = $this->attendanceService->formatHourly($underMinutes, $hourlyRate);

        $payroll->overtime_minutes = $overtimeMinutes;
        $payroll->overtime_pay = $this->attendanceService->formatHourly($overtimeMinutes, $hourlyRate);

        $payroll->expected_hours_worked = $timesheet->count() * $working_hours_per_day;
        $payroll->hours_worked = $minutesWorked / self::SIXTY_MINUTES;

        return $payroll;
    }

    private function calculateContributions(Payroll $payroll): Payroll
    {
        $payroll->sss_contributions = $this->sssService->compute($payroll->basic_salary);
        $payroll->pagibig_contributions = $this->pagIbigService->compute($payroll->basic_salary);
        $payroll->philhealth_contributions = $this->philHealthService->compute($payroll->basic_salary);
        return $payroll;
    }

    private function calculateWithheldTax(Payroll $payroll): Payroll
    {
        $payroll->withheld_tax = $this->taxService->compute(
            $payroll->taxable_income,
            $this->settings->period_cycle
        );
        return $payroll;
    }

    private function calculateOtherEarnings(Payroll $payroll): Payroll
    {
        $payroll->non_taxable_earnings = $this->salaryData->non_taxable_earnings;
        $payroll->taxable_earnings = $this->salaryData->taxable_earnings;
        return $payroll;
    }

    private function calculateHolidayPay(Payroll $payroll, Collection $timesheet = null): Payroll
    {
        $hourlyRate = $this->salaryData->hourly_rate;
        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidays = $this->holidays->where('simplified_type', $type);
            $hours = 0;
            $hoursPay = 0;
            $hoursWorked = 0;
            $hoursWorkedPay = 0;
            $rate = $this->salaryData->{"{$type}_holiday_rate"};
            foreach ($holidays as $holiday) {
                $hours += $this->salaryData->working_hours_per_day;
                $hoursPay += $this->salaryData->daily_rate;

                $filteredCollection = $timesheet->where(function ($item) use ($holiday) {
                    $clockInDate = substr($item['clock_in'], 0, 10);
                    return $clockInDate === $holiday->date;
                });

                if ($filteredCollection->isNotEmpty()) {
                    foreach ($filteredCollection as $entry) {
                        $clockIn = Carbon::parse($entry->clock_in);
                        $clockOut = Carbon::parse($entry->clock_out);
                        $minutesWorked = $clockIn->diffInMinutes($clockOut);
                        $hoursWorked += $minutesWorked / self::SIXTY_MINUTES;
                        $hoursWorkedPay += $this->attendanceService->formatHourly($minutesWorked, $hourlyRate) * $rate;
                    }
                }
            }
            $holidaysPay[$type] = [
                'hours' => $hours,
                'hours_pay' => $hoursPay,
                'hours_worked' => $hoursWorked,
                'hours_worked_pay' => $hoursWorkedPay
            ];
        }
        $payroll->holidays = $holidaysPay;
        return $payroll;
    }

    private function calculateLeaves(Payroll $payroll): Payroll
    {
        return $payroll;
    }

    private function getTimesheet(Employee $employee, Period $period)
    {
        return $employee->timeRecords()->byRange([
            'dateFrom' => $period->start_date,
            'dateTo' => $period->end_date
        ])->get();
    }

}
