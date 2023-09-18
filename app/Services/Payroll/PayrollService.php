<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\AttendanceService;
use App\Services\Contributions\PagIbigService;
use App\Services\Contributions\PhilHealthService;
use App\Services\Contributions\SSSService;
use App\Services\LeaveService;
use App\Services\TaxService;
use App\Services\TimeRecordService;
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

    public function initializePayroll(Employee $employee, Period $period, array $override = []): Payroll
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

        $data = [
            ...$override,
            'period_id' => $period->id,
            'employee_id' => $employee->id
        ];
        return Payroll::firstOrCreate($data);
    }

    public function generate(Period $period, Employee $employee, array $override = []): Payroll
    {
        $timesheet = null;
        $payroll = $this->initializePayroll($employee, $period, $override);
        $payroll = $this->getLeaves($payroll);
        $payroll->basic_salary = $this->salaryData->basic_salary / self::CYCLE_DIVISOR[$this->settings->period_cycle];
        if ($this->company->hasTimeAndAttendanceSubscription) {
            $this->timeRecordService->setExpectedScheduleByPeriod($employee, $period);
            $timesheet = $this->getTimesheet($employee, $period);
            $payroll = self::calculateAttendanceRecords($payroll, $timesheet);
            $payroll = self::calculateAttendancePay($payroll, $this->salaryData->hourly_rate);
        }
        $payroll = self::calculateHolidayHours($payroll, $timesheet);
        $payroll = self::calculateHolidayPay($payroll);
        $payroll = self::calculateLeaves($payroll);
        $payroll = self::calculateContributions($payroll);
        $payroll = self::calculateOtherEarnings($payroll);
        $payroll = self::calculateWithheldTax($payroll);
        $payroll->save();
        return $payroll;
    }

    public function update(Payroll $payroll, array $data): Payroll
    {
        $this->initializePayroll($payroll->employee, $payroll->period);
        $data = self::parseHoliday($payroll, $data);
        $data = self::parseAttendance($data);
        $payroll->update($data);
        $payroll = self::calculateAttendancePay($payroll, $this->salaryData->hourly_rate);
        $payroll = self::calculateHolidayPay($payroll);
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
        $working_hours_per_day = $this->salaryData->working_hours_per_day;

        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidaysHours[$type] = 0;
            $holidaysHoursWorked[$type] = 0;
        }

        foreach ($timesheet as $record) {
            $expectedClockIn = self::parseClockRecord($record->expected_clock_in);
            $expectedClockOut = self::parseClockRecord($record->expected_clock_out);
            $clockIn = self::parseClockRecord($record->clock_in);
            $clockOut = self::parseClockRecord($record->clock_out);
            $minutesWorked = self::getMinutesWorked($clockIn, $clockOut);

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
        $payroll->late_minutes = $lateMinutes;
        $payroll->undertime_minutes = $underMinutes;
        $payroll->overtime_minutes = $overtimeMinutes;
        $payroll->expected_hours_worked = $timesheet->count() * $working_hours_per_day;
        $payroll->hours_worked = $minutesWorked / self::SIXTY_MINUTES;
        return $payroll;
    }

    public function calculateAttendancePay(Payroll $payroll, float $hourlyRate): Payroll
    {
        $payroll->absent_deductions = $this->attendanceService->formatHourly($payroll->absent_minutes, $hourlyRate);
        $payroll->late_deductions = $this->attendanceService->formatHourly($payroll->late_minutes, $hourlyRate);
        $payroll->undertime_deductions = $this->attendanceService->formatHourly($payroll->undertime_minutes, $hourlyRate);
        $payroll->overtime_pay = $this->attendanceService->formatHourly($payroll->overtime_minutes, $hourlyRate);
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

    private function calculateHolidayHours(Payroll $payroll, Collection $timesheet = null): Payroll
    {
        $holidaysPay = [];
        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidays = $this->holidays->where('simplified_type', $type);
            if (!$holidays) {
                continue;
            }
            $hours = 0;
            $hoursWorked = 0;
            foreach ($holidays as $holiday) {
                $hours += $this->salaryData->working_hours_per_day;
                if ($timesheet) {
                    $hoursWorked += $this->calculateHolidayHoursWorked($timesheet, $holiday);
                }
            }
            $holidaysPay[$type] = [
                'hours' => $hours,
                'hours_worked' => $hoursWorked,
            ];
        }
        $payroll->holidays = $holidaysPay;
        return $payroll;
    }

    private function calculateHolidayPay(Payroll $payroll): Payroll
    {
        $holidays = $payroll->holidays;
        foreach ($holidays as $type => $holiday) {

            $rate = $this->salaryData->{"{$type}_holiday_rate"};

            $holidays[$type] = [
                ...$holidays[$type],
                'hours_pay' => $holiday['hours'] * $this->salaryData->hourly_rate,
                'hours_worked_pay' => $holiday['hours_worked'] * $this->salaryData->hourly_rate * $rate
            ];
        }
        $payroll->holidays = $holidays;
        return $payroll;
    }

    private function calculateLeaves(Payroll $payroll): Payroll
    {
        $leavesPay = 0;
        $period = $payroll->period;
        $leaves = collect($payroll->leaves)->unique('date')->values()->all();
        if (!empty($leaves) && $period) {
            foreach ($leaves as $key => $leave) {
                if ($leave['date'] >= $period->start_date && $leave['date'] <= $period->end_date) {
                    $leavesPay += $leave['hours'] * $this->salaryData->hourly_rate;
                } else {
                    unset($leaves[$key]);
                }
            }
            $payroll->leaves = $leaves;
            $payroll->leaves_pay = $leavesPay;
        }
        return $payroll;
    }

    private function getTimesheet(Employee $employee, Period $period)
    {
        return $employee->timeRecords()->byRange([
            'dateFrom' => $period->start_date,
            'dateTo' => $period->end_date
        ])->get();
    }

    private function getLeaves(Payroll $payroll): Payroll
    {
        $leaves = $payroll->employee->leaves()->where([
            ['start_date', '>=', $payroll->period->start_date],
            ['end_date', '<=', $payroll->period->end_date]
        ])->get();
        if ($leaves->isNotEmpty()) {
            $leaves = $leaves->map(function ($item) {
                $type = str_replace("_leave", "", $item["type"]);
                $startDateTime = new Carbon($item["start_date"]);
                $endDateTime = new Carbon($item["end_date"]);
                $interval = $startDateTime->diff($endDateTime);
                return [
                    "type" => $type,
                    "date" => date("Y-m-d", strtotime($item["start_date"])),
                    "hours" => $interval->h - $this->salaryData->break_hours_per_day,
                ];
            });
            $payroll->leaves = json_encode($leaves);
        }
        return $payroll;
    }

    private function parseClockRecord(?string $clock): ?Carbon
    {
        return $clock ? Carbon::parse($clock) : null;
    }

    private function getMinutesWorked(?Carbon $clockIn, ?Carbon $clockOut): float
    {
        if ($clockIn && $clockOut) {
            return $clockIn->diffInMinutes($clockOut);
        }
        return 0;
    }

    private function parseHoliday(Payroll $payroll, array $data): array
    {
        $holidays = $payroll->holidays;
        if (isset($data['regular_holiday_hours_worked'])) {
            $holidays[Holiday::REGULAR_HOLIDAY]['hours_worked'] = floatval($data['regular_holiday_hours_worked']);
            unset($data['regular_holiday_hours_worked']);
        }
        if (isset($data['special_holiday_hours_worked'])) {
            $holidays[Holiday::SPECIAL_HOLIDAY]['hours_worked'] = floatval($data['special_holiday_hours_worked']);
            unset($data['special_holiday_hours_worked']);
        }
        $data['holidays'] = $holidays;
        return $data;
    }

    private function parseAttendance(array $data): array
    {
        $types = [
            'overtime_hours' => 'overtime_minutes',
            'late_hours' => 'late_minutes',
            'absent_hours' => 'absent_minutes',
            'undertime_hours' => 'undertime_minutes'
        ];

        foreach ($types as $hours => $minutes) {
            if (isset($data[$hours])) {
                $data[$minutes] = $data[$hours] / self::SIXTY_MINUTES;
                unset($data[$hours]);
            }
        }
        return $data;
    }

    private function calculateHolidayHoursWorked(Collection $timesheet, Holiday $holiday): float
    {
        $hoursWorked = 0;
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
            }
        }
        return $hoursWorked;
    }

}
