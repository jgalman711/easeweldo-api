<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollTaxesContributions;
use App\Models\Period;
use App\Models\TimeRecord;
use App\Services\Contributions\PagIbig;
use App\Services\Contributions\PhilHealth;
use App\Services\Contributions\SSS;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    protected $pagibig;
    protected $philhealth;
    protected $sss;
    protected $tax;

    public const FREQUENCY_SEMI_MONTHLY = 2;
    public const FREQUENCY_WEEKLY = 4.33;

    public function __construct(PagIbig $pagIbig, PhilHealth $philHealth, SSS $sss, TaxService $tax)
    {
        $this->pagibig = $pagIbig;
        $this->philhealth = $philHealth;
        $this->sss = $sss;
        $this->tax = $tax;
    }

    public function compute(Employee $employee, Period $period): Payroll
    {
        $this->validate($period, $employee);
        $timeRecords = $this->getTimeRecords($employee, $period->start_date, $period->end_date)
            ->groupBy(function ($record) {
                return $record->created_at->format('Y-m-d');
            });
        $totalMinutesLate = 0;
        $totalUnderTime = 0;
        $totalOvertime = 0;
        $absences = 0;

        foreach ($timeRecords as $timeRecordsByDate) {
            $actualClockIn = strtotime($timeRecordsByDate->min('clock_in'));
            $expectedClockIn = strtotime($timeRecordsByDate->min('expected_clock_in'));

            $actualClockOut = strtotime($timeRecordsByDate->max('clock_out'));
            $expectedClockOut = strtotime($timeRecordsByDate->max('expected_clock_out'));

            if ($expectedClockIn && $actualClockIn == null) {
                $absences++;
                continue;
            }

            $minutesLate = $actualClockIn > $expectedClockIn
                ? ($actualClockIn - $expectedClockIn) / 60 : 0;
            $underTimeInMinutes = $expectedClockOut > $actualClockOut
                ? ($expectedClockOut - $actualClockOut) / 60 : 0;
            $overtimeInMinutes = $actualClockOut > $expectedClockOut
                && ($actualClockOut - $expectedClockOut > 60)
                ? $actualClockOut - $expectedClockOut : 0;

            $totalMinutesLate += $minutesLate;
            $totalUnderTime += $underTimeInMinutes;
            $totalOvertime += $overtimeInMinutes;
        }

        $hourlyRate = $employee->salaryComputation->getHourlySalary();
        $totalAbsentDeductions = $absences * $employee->salaryComputation->getDailySalary();
        $totalLateDeductions = round($hourlyRate * ($totalMinutesLate / 60), 2);
        $totalUnderTimeDeductions = round($hourlyRate * ($totalUnderTime / 60), 2);
        $totalOverTimePay = round($totalOvertime / 60 * $hourlyRate * $employee->salaryComputation->overtime_rate, 2);
        
        $basicSalary = $employee->salaryComputation->basic_salary;
        $grossPay = $basicSalary + $totalOverTimePay - $totalLateDeductions - $totalUnderTimeDeductions;

        //$totalNightDiffPay = TBD

        $sssContribution = $this->sss->compute($basicSalary);
        $pagIbigContribution = $this->pagibig->compute($basicSalary);
        $philHealthContribution = $this->philhealth->compute($basicSalary);

        if ($period->type == Period::TYPE_SEMI_MONTHLY) {
            $basicSalary = $basicSalary / self::FREQUENCY_SEMI_MONTHLY;
            $sssContribution = $sssContribution / self::FREQUENCY_SEMI_MONTHLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_SEMI_MONTHLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_SEMI_MONTHLY;
        } elseif ($period->type == Period::TYPE_WEEKLY) {
            $basicSalary = $basicSalary / self::FREQUENCY_WEEKLY;
            $sssContribution = $sssContribution / self::FREQUENCY_WEEKLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_WEEKLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_WEEKLY;
        }

        $totalContributions = $pagIbigContribution + $philHealthContribution + $sssContribution;

        $taxableIncome = $grossPay - $totalContributions;

        $incomeTax = $this->tax->compute($taxableIncome, $period->type);
        $netPay = $taxableIncome - $incomeTax;

        try {
            DB::beginTransaction();
            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'period_id' => $period->id,
                'basic_salary' => $basicSalary,
                'total_late_minutes' => $totalMinutesLate,
                'total_late_deductions' => $totalLateDeductions,
                'total_absent_days' => $absences,
                'total_absent_deductions' => $totalAbsentDeductions,
                'total_overtime_minutes' => $totalOvertime,
                'total_overtime_pay' => $totalOverTimePay,
                'total_undertime_minutes' => $totalUnderTime,
                'total_undertime_deductions' => $totalUnderTimeDeductions,
                'sss_contribution' => $sssContribution,
                'philhealth_contribution' => $philHealthContribution,
                'pagibig_contribution' => $pagIbigContribution,
                'total_contributions' => $totalContributions,
                'taxable_income' => $taxableIncome,
                'base_tax' => $this->tax->getBaseTax(),
                'compensation_level' => $this->tax->getCompensationLevel(),
                'tax_rate' => $this->tax->getTaxRate(),
                'income_tax' => $incomeTax,
                'net_salary' => $netPay
            ]);
            PayrollTaxesContributions::create([
                'payroll_id' => $payroll->id,
                'company_id' => $employee->company->id,
                'withholding_tax' => $this->tax->getBaseTax(),
                'sss_contribution' => $this->sss->getEmployerShare(),
                'pagibig_contribution' => $this->pagibig->getEmployerShare()
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $payroll;
    }

    private function validate(Period $period, Employee $employee): void
    {
        throw_if($period->status != Period::STATUS_PENDING, new Exception('Period is already ' . $period->status));
        $payroll = $employee->payrolls->firstWhere('period_id', $period->id);

        throw_if($payroll, new Exception('Payroll already exists.'));
        $workSchedule = $employee->schedules()
            ->where('start_date', '<=', $period->start_date)
            ->first();

        throw_unless($workSchedule, new Exception('No available work schedule for this period'));
    }

    private function getTimeRecords(Employee $employee, Carbon $startDate, Carbon $endDate): Collection
    {
        return $employee->timeRecords()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull(['expected_clock_in', 'expected_clock_out'])
            ->get();
    }
}

