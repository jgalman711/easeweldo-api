<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\PayrollTaxesContributions;
use App\Models\Period;
use App\Models\SalaryComputation;
use App\Models\Setting;
use App\Services\Contributions\PagIbigService;
use App\Services\Contributions\PhilHealthService;
use App\Services\Contributions\SSSService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public const FREQUENCY_SEMI_MONTHLY = 2;

    public const FREQUENCY_WEEKLY = 4.33;

    private const MINUTES_60 = 60;

    private const MONTHS_12 = 12;

    protected const CYCLE_DIVISOR = [
        Period::TYPE_WEEKLY => 4,
        Period::TYPE_SEMI_MONTHLY => 2,
        Period::TYPE_MONTHLY => 1,
    ];

    protected $timeRecordService;

    protected $pagIbigService;

    protected $philHealthService;

    protected $sssService;

    protected $taxService;

    protected $leaveService;

    protected $payroll;

    protected $salaryData;

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

    public function generate(Employee $employee, array $data, array $timeRecords = null): Payroll
    {
        $payrollCycle = $employee->company->setting->period_cycle;
        $this->salaryData = $employee->salaryComputation;

        throw_unless(
            $this->salaryData,
            new Exception("Salary computation data not found for the employee.")
        );

        $data['employee_id'] = $employee->id;
        $data['leaves'] = $data['leaves'] ?? [];

        $data['allowances'] = !empty($data['allowances']) ? $data['allowances'] : $this->salaryData->allowances;
        $data['commissions'] = !empty($data['commissions']) ? $data['commissions'] : $this->salaryData->commissions;
        $data['other_compensations'] = !empty($data['other_compensations'])
            ? $data['other_compensations']
            : $this->salaryData->other_compensations;

        $this->payroll = Payroll::updateOrCreate(
            ['period_id' => $data['period_id']],
            $data
        );

        if ($timeRecords) {
            // Time records are optional. It can be csv or from the attendance record.
        } else {
            $this->payroll->basic_salary = $this->salaryData->basic_salary;
        }

        self::setPayrollHolidays();
        self::setPayrollContributions($payrollCycle);

        $totalAllowances = collect($this->payroll->allowances)->sum('pay');
        $totalCommissions = collect($this->payroll->commissions)->sum('pay');
        $otherCompensations = collect($this->payroll->other_compensations)->sum('pay');

        $this->payroll->gross_income = $this->payroll->basic_salary / self::CYCLE_DIVISOR[$payrollCycle];
        $this->payroll->taxable_income = $this->payroll->gross_income
            + $totalAllowances
            + $totalCommissions
            - $this->payroll->total_contributions;

        $this->payroll->income_tax = $this->taxService->compute($this->payroll->taxable_income, $payrollCycle);
        $this->payroll->net_income = $this->payroll->taxable_income - $this->payroll->income_tax + $otherCompensations;
        $this->payroll->save();
        return $this->payroll;
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

        $specialHolidayWorkedPay = $this->payroll->special_holiday_hours_worked
            * $this->salaryData->hourly_rate
            * $this->salaryData->special_holiday_rate;
        $this->payroll->special_holiday_hours = $specialHolidays * $this->salaryData->working_hours_per_day;
        $this->payroll->special_holiday_hours_pay = $specialHolidayWorkedPay;
    }

    private function setPayrollContributions(string $payrollCycle): void
    {
        $this->payroll->sss_contributions = $this->sssService->compute($this->payroll->basic_salary);
        $this->payroll->pagibig_contributions = $this->pagIbigService->compute($this->payroll->basic_salary);
        $this->payroll->philhealth_contributions = $this->philHealthService->compute($this->payroll->basic_salary);
        $this->payroll->total_contributions = ($this->payroll->sss_contributions
            + $this->payroll->pagibig_contributions
            + $this->payroll->philhealth_contributions) / self::CYCLE_DIVISOR[$payrollCycle];
    }
}
