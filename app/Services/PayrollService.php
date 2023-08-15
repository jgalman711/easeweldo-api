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

    protected $timeRecordService;

    protected $pagIbigService;

    protected $philHealthService;

    protected $sssService;

    protected $taxService;

    protected $leaveService;

    protected $payroll;

    protected $salaryData;

    protected $data;

    protected $company;

    protected $settings;

    protected $timesheet;

    protected $holidays;

    public function __construct(
        TimeRecordService $timeRecordService,
        PagIbigService $pagIbigService,
        PhilHealthService $philHealthService,
        SSSService $sssService,
        TaxService $taxService,
        LeaveService $leaveService
    ) {
        $this->timeRecordService = $timeRecordService;
        $this->pagIbigService = $pagIbigService;
        $this->philHealthService = $philHealthService;
        $this->sssService = $sssService;
        $this->taxService = $taxService;
        $this->leaveService = $leaveService;
    }

    public function generate(Period $period, Employee $employee, array $data = null): Payroll
    {
        $this->data = $data;
        $this->company = $employee->company;
        $this->settings = $employee->company->setting;
        $this->salaryData = $employee->salaryComputation;

        throw_unless(
            $this->salaryData,
            new Exception("Salary computation data of employee {$employee->fullName} (ID:$employee->id) not found.")
        );

        $data['employee_id'] = $employee->id;
        $data['leaves'] = $data['leaves'] ?? [];

        $this->payroll = Payroll::updateOrCreate(
            ['period_id' => $period->id, 'employee_id' => $employee->id],
            $data
        );

        $this->payroll->basic_salary
            = $this->salaryData->basic_salary
            / self::CYCLE_DIVISOR[$this->settings->period_cycle];

        if ($this->company->hasTimeAndAttendanceSubscription()) {
            $this->getTimesheet($employee, $period);
        }

        $this->holidays = Holiday::whereBetween('date', [
            $this->payroll->period->start_date,
            $this->payroll->period->end_date
        ])->get();

        self::calculateAttendanceRecords();
        self::setPayrollContributions();

        $totalEarnings = $this->getOtherEarnings();

        $this->payroll->gross_income = $this->payroll->basic_salary
            - $this->payroll->absent_deductions
            - $this->payroll->undertime_deductions
            - $this->payroll->late_deductions
            + $this->payroll->overtime_pay;

        $this->payroll->taxable_income = $this->payroll->gross_income
            - $this->payroll->total_contributions
            + $this->payroll->leaves_pay
            + $totalEarnings[Earning::NON_TAXABLE];

        $this->payroll->withheld_tax = $this->taxService->compute(
                $this->payroll->taxable_income,
                $this->settings->period_cycle
            );

        $this->payroll->net_income = $this->payroll->taxable_income
            - $this->payroll->withheld_tax
            + $totalEarnings[Earning::TAXABLE];

        $this->payroll->save();
        return $this->payroll;
    }

    public function calculateAttendanceRecords(): void
    {
        if (!$this->company->hasTimeAndAttendanceSubscription()) {
            return;
        }

        $absentMinutes = 0;
        $lateMinutes = 0;
        $underMinutes = 0;
        $overtimeMinutes = 0;
        $minutesWorked = 0;
        $leavePay = 0;
        $leaves = [];

        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidaysHours[$type] = 0;
            $holidaysHoursWorked[$type] = 0;
        }

        foreach ($this->timesheet as $record) {
            $expectedClockIn = $record->expected_clock_in ? Carbon::parse($record->expected_clock_in) : null;
            $expectedClockOut = $record->expected_clock_out ? Carbon::parse($record->expected_clock_out) : null;
            $clockIn = Carbon::parse($record->clock_in);
            $clockOut = Carbon::parse($record->clock_out);
            $minutesWorked += $clockIn->diffInMinutes($clockOut);

            $formattedExpectedClockIn = Carbon::parse($record->expected_clock_in)->format('Y-m-d');

            $holiday = $this->holidays->where('date', $formattedExpectedClockIn)->first();
            if ($expectedClockIn) {
                if ($holiday) {
                    $holidaysHours[$holiday->simplifiedType] += $expectedClockIn->diffInHours($expectedClockOut);
                }
                $expectedClockIn->addMinutes($this->settings->grace_period);
            }

            if (!$record->clock_in && !$record->clock_out) {
                $absentMinutes += $this->salaryData->working_hours_per_day * self::SIXTY_MINUTES;
            } else {
                if ($holiday) {
                    $holidaysHoursWorked[$holiday->simplifiedType] += $clockIn->diffInHours($clockOut);
                }

                if ($expectedClockIn && $expectedClockOut) {
                    $lateMinutes += $clockIn->gt($expectedClockIn)
                        ? $clockIn->diffInMinutes($expectedClockIn)
                        : 0;

                    $underMinutes += $clockOut->lt($expectedClockOut)
                        ? $clockOut->diffInMinutes($expectedClockOut)
                        : 0;

                    if ($expectedClockOut) {
                        $expectedClockOut->addMinutes($this->settings->minimum_overtime);
                    }

                    $overtimeMinutes += $clockOut->gt($expectedClockOut)
                        ? $clockOut->diffInMinutes($expectedClockOut)
                        : 0;
                }
            }

            if (isset($this->data['leaves']) && $this->data['leaves']) {
                foreach ($this->data['leaves'] as $leave) {
                    if ($record->expected_clock_in && $leave['date'] == $formattedExpectedClockIn) {
                        $leave[Leave::PAY] = $leave[Leave::HOURS] * $this->salaryData->hourly_rate;
                        $leavePay += $leave[Leave::PAY];
                        $leaves[] = $leave;
                    }
                }
            }
        }

        $this->payroll->leaves = $leaves;
        $this->payroll->leaves_pay = $leavePay;

        $this->calculateRegularHolidayPay($holidaysHours, $holidaysHoursWorked);

        $this->payroll->absent_minutes = $absentMinutes;
        $this->payroll->absent_deductions = $absentMinutes / self::SIXTY_MINUTES * $this->salaryData->hourly_rate;

        $this->payroll->late_minutes = $lateMinutes;
        $this->payroll->late_deductions = $lateMinutes / self::SIXTY_MINUTES * $this->salaryData->hourly_rate;

        $this->payroll->undertime_minutes = $underMinutes;
        $this->payroll->undertime_deductions = $underMinutes / self::SIXTY_MINUTES * $this->salaryData->hourly_rate;

        $this->payroll->overtime_minutes = $overtimeMinutes;
        $this->payroll->overtime_pay = $overtimeMinutes / self::SIXTY_MINUTES * $this->salaryData->hourly_rate;

        $this->payroll->expected_hours_worked = $this->timesheet->count() * $this->salaryData->working_hours_per_day;
        $this->payroll->hours_worked = $minutesWorked / self::SIXTY_MINUTES;
    }

    private function getTimesheet(Employee $employee, Period $period)
    {
        $this->timesheet = $employee->timeRecords()->byRange([
            'dateTo' => $period->start_date,
            'dateFrom' => $period->end_date
        ])->get();
        throw_if($this->timesheet->isEmpty(), new Exception('Time records not found.'));
    }

    private function calculateRegularHolidayPay(array $holidaysHours, array $holidaysHoursWorked): void
    {
        foreach (Holiday::HOLIDAY_TYPES as $holidayType) {
            $this->payroll->{$holidayType . "_holiday_hours"} = $holidaysHours[$holidayType];
            $this->payroll->{$holidayType . "_holiday_hours_pay"}
                = $this->payroll->{$holidayType . "_holiday_hours"}
                * $this->salaryData->hourly_rate;

            $this->payroll->{$holidayType . "_holiday_hours_worked"} = $holidaysHoursWorked[$holidayType];
            $this->payroll->{$holidayType . "_holiday_hours_worked_pay"}
                = $this->payroll->{$holidayType . "_holiday_hours_worked"}
                * $this->salaryData->hourly_rate;
        }
    }

    private function getOtherEarnings(): array
    {
        $totalEarnings = [
            Earning::NON_TAXABLE => 0,
            Earning::TAXABLE => 0
        ];
        foreach (Earning::CATEGORIES as $category) {
            $categoryEarnings = $category . Earning::CATEGORY_SUFFIX_EARNINGS;
            if (isset($this->data[$categoryEarnings]) && $this->data[$categoryEarnings]) {
                $this->payroll->$categoryEarnings = $this->data[$categoryEarnings];
                foreach ($this->data[$categoryEarnings] as $earning) {
                    $totalEarnings[$category] += $earning['pay'];
                }
            }
        }
        return $totalEarnings;
    }

    private function setPayrollContributions(): void
    {
        $this->payroll->sss_contributions = $this->sssService->compute($this->payroll->basic_salary);
        $this->payroll->pagibig_contributions = $this->pagIbigService->compute($this->payroll->basic_salary);
        $this->payroll->philhealth_contributions = $this->philHealthService->compute($this->payroll->basic_salary);
        $this->payroll->total_contributions = $this->payroll->sss_contributions
            + $this->payroll->pagibig_contributions
            + $this->payroll->philhealth_contributions;
    }
}
