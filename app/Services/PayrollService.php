<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Contributions\PagIbigService;
use App\Services\Contributions\PhilHealthService;
use App\Services\Contributions\SSSService;
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

    protected const COMPENSATION_TYPES = ['allowances', 'commissions', 'other_compensations'];

    protected const TOTAL_PREFIX = "total_";

    protected const YTD_SUFFIX = "_ytd";

    protected $timeRecordService;

    protected $pagIbigService;

    protected $philHealthService;

    protected $sssService;

    protected $taxService;

    protected $leaveService;

    protected $payroll;

    protected $employeeYTD;

    protected $salaryData;

    protected $timesheet;

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
        $payrollCycle = $employee->company->setting->period_cycle;
        $this->employeeYTD = $employee->yearToDate()->firstOrNew();
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

        $this->timesheet = $employee->timeRecords()->byRange([
            'dateTo' => $period->start_date,
            'dateFrom' => $period->end_date
        ])->get();

        if ($this->timesheet->isNotEmpty()) {
            // Time records are optional. It can be csv or from the attendance record.
            $this->calculateAttendanceRecords();
        } else {
            $this->payroll->basic_salary = $this->salaryData->basic_salary / self::CYCLE_DIVISOR[$payrollCycle];
        }

        self::setPayrollHolidays();
        self::setPayrollContributions();
        self::setPayrollCompensations($data, $payrollCycle);

        $this->payroll->gross_income = $this->payroll->basic_salary;
        $this->payroll->gross_income_ytd = $this->employeeYTD->gross_income + $this->payroll->gross_income;
        $this->payroll->taxable_income = $this->payroll->gross_income
            + $this->payroll->total_allowances
            + $this->payroll->total_commissions
            - $this->payroll->total_contributions;

        $this->payroll->withheld_tax = $this->taxService->compute($this->payroll->taxable_income, $payrollCycle);
        $this->payroll->withheld_tax = $this->employeeYTD->withheld_tax + $this->payroll->withheld_tax;
        $this->payroll->net_income = $this->payroll->taxable_income
            - $this->payroll->withheld_tax
            + $this->payroll->total_other_compensations;
        $this->payroll->net_income_ytd = $this->employeeYTD->net_income_ytd + $this->payroll->net_income;

        $this->payroll->save();
        return $this->payroll;
    }

    private function calculateAttendanceRecords(): void
    {
        foreach ($this->timesheet as $record) {
            dd($record->expected_clock_in);
            // $expectedClockIn = Carbon::parse($timeRecord->expected_clock_in);
            // $expectedClockOut = Carbon::parse($timeRecord->expected_clock_out);
            // $clockIn = Carbon::parse($timeRecord->clock_in);
            // $clockOut = Carbon::parse($timeRecord->clock_out);
            // $expectedWorkedHours = $expectedClockIn->diffInHours($expectedClockOut);
            // $totalExpectedWorkedHours += $expectedWorkedHours;
            // if (!$timeRecord->clock_in && !$timeRecord->clock_out) {
            //     $absences ++;
            //     $absentHours += $expectedWorkedHours;
            //     $leaveHours += $this->calculateLeaveHours($leaves, $expectedWorkedHours, $expectedClockIn);
            // } else {
            //     $lateMinutes += $clockIn->gt($expectedClockIn)
            //         && $clockIn->diffInMinutes($expectedClockIn) > $settings->grace_period
            //         ? $clockIn->diffInMinutes($expectedClockIn) : 0;
            //     $undertimeMinutes += $clockOut->lt($expectedClockOut)
            //         ? $clockOut->diffInMinutes($expectedClockOut) : 0;
            //     $overtimeMinutes += $clockOut->gt($expectedClockOut)
            //         && $clockOut->diffInMinutes($expectedClockOut) > $settings->minimum_overtime
            //         ? $clockOut->diffInMinutes($expectedClockOut) : 0;
            //     $hoursWorkedMinutes += $clockIn->diffInMinutes($clockOut);
            // }
        }
    }

    private function setPayrollCompensations(array $data, string $payrollCycle): void
    {
        foreach (self::COMPENSATION_TYPES as $compensationType) {
            if (empty($data[$compensationType])) {
                $compensations = $this->salaryData->$compensationType;
                foreach($compensations as &$compensation) {
                    $compensation['pay'] /= self::CYCLE_DIVISOR[$payrollCycle];
                }
                $this->payroll->$compensationType = $compensations;
            } else {
                $this->payroll->compensationType = $data[$compensationType];
            }
            $this->payroll->{self::TOTAL_PREFIX . $compensationType}
                = collect($this->payroll->$compensationType)->sum('pay');
            $this->payroll->{self::TOTAL_PREFIX . $compensationType . self::YTD_SUFFIX}
                = $this->payroll->{self::TOTAL_PREFIX . $compensationType}
                + $this->employeeYTD->{self::TOTAL_PREFIX . $compensationType};
        }
    }

    private function setPayrollHolidays(): void
    {
        $regularHolidays = Holiday::whereBetween('date', [
                $this->payroll->period->start_date,
                $this->payroll->period->end_date
            ])
            ->where('type', Holiday::REGULAR_HOLIDAY)
            ->count();

        $specialHolidays = Holiday::whereBetween('date', [
                $this->payroll->period->start_date,
                $this->payroll->period->end_date
            ])
            ->where('type', Holiday::SPECIAL_HOLIDAY)
            ->count();

        $regularHolidayWorkedPay = $this->payroll->regular_holiday_hours_worked
            * $this->salaryData->hourly_rate
            * $this->salaryData->regular_holiday_rate;
        $this->payroll->regular_holiday_hours = $regularHolidays * $this->salaryData->working_hours_per_day;
        $this->payroll->regular_holiday_hours_pay = $regularHolidayWorkedPay;
        $this->payroll->regular_holiday_hours_pay_ytd = $this->payroll->regular_holiday_hours_pay
            + $this->employeeYTD->regular_holiday_hours_pay;

        $specialHolidayWorkedPay = $this->payroll->special_holiday_hours_worked
            * $this->salaryData->hourly_rate
            * $this->salaryData->special_holiday_rate;
        $this->payroll->special_holiday_hours = $specialHolidays * $this->salaryData->working_hours_per_day;
        $this->payroll->special_holiday_hours_pay = $specialHolidayWorkedPay;
        $this->payroll->special_holiday_hours_pay_ytd = $this->payroll->special_holiday_hours_pay
            + $this->employeeYTD->special_holiday_hours_pay;
    }

    private function setPayrollContributions(): void
    {
        $this->payroll->sss_contributions = $this->sssService->compute($this->payroll->basic_salary);
        $this->payroll->sss_contributions_ytd = $this->payroll->sss_contributions
            + $this->employeeYTD->sss_contributions;

        $this->payroll->pagibig_contributions = $this->pagIbigService->compute($this->payroll->basic_salary);
        $this->payroll->pagibig_contributions_ytd = $this->payroll->pagibig_contributions
            + $this->employeeYTD->pagibig_contributions;

        $this->payroll->philhealth_contributions = $this->philHealthService->compute($this->payroll->basic_salary);
        $this->payroll->philhealth_contributions_ytd = $this->payroll->philhealth_contributions
            + $this->employeeYTD->philhealth_contributions;

        $this->payroll->total_contributions = $this->payroll->sss_contributions
            + $this->payroll->pagibig_contributions
            + $this->payroll->philhealth_contributions;
        $this->payroll->total_contributions_ytd = $this->employeeYTD->total_contributions
            + $this->payroll->total_contributions;
    }
}
