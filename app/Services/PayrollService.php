<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Models\TimeRecord;
use App\Services\Contributions\PagIbig;
use App\Services\Contributions\PhilHealth;
use App\Services\Contributions\SSS;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PayrollService
{
    protected $pagibig;
    protected $philhealth;
    protected $sss;
    protected $tax;

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
        $timeRecords = $this->getTimeRecords($employee, $period->start_date, $period->end_date);

        $totalMinutesLate = 0;
        $totalUnderTime = 0;
        $totalOvertime = 0;
        $absences = 0;
        
        foreach ($timeRecords as $timeRecord) {
            if ($timeRecord->expected_clock_in && $timeRecord->clock_in == null) {
                $absences++;
                continue;
            }
            
            $actualClockIn = $this->timeOnlyFormat($timeRecord->clock_in);
            $expectedClockIn = $this->timeOnlyFormat($timeRecord->expected_clock_in);

            $actualClockOut = $this->timeOnlyFormat($timeRecord->clock_out);
            $expectedClockOut = $this->timeOnlyFormat($timeRecord->expected_clock_out);
            
            $minutesLate = $actualClockIn->greaterThan($expectedClockIn)
                ? $actualClockIn->diffInMinutes($expectedClockIn) : 0;
            $underTimeInMinutes = $expectedClockOut->greaterThan($actualClockOut)
                ? $expectedClockOut->diffInMinutes($actualClockOut) : 0;
            $overtimeInMinutes = $actualClockOut->greaterThan($expectedClockOut)
                && ($actualClockOut->diffInMinutes($expectedClockOut) > 60)
                ? $actualClockOut->diffInMinutes($expectedClockOut) : 0;

            $totalMinutesLate += $minutesLate;
            $totalUnderTime += $underTimeInMinutes;
            $totalOvertime += $overtimeInMinutes;
        }

        $hourlyRate = $employee->salaryComputation->getHourlySalary();
        $totalAbsentDeductions = $absences * $employee->salaryComputation->getDailySalary();
        $totalLateDeductions = round($hourlyRate * ($totalMinutesLate / 60), 2);
        $totalUnderTimeDeductions = round($hourlyRate * ($totalUnderTime / 60), 2);
        $totalOverTimePay = round($totalOvertime / 60 * $hourlyRate * $employee->salaryComputation->overtime_rate, 2);
        
        //$totalNightDiffPay = TBD

        if ($period->type == Period::TYPE_MONTHLY) {
            $basicSalary = $employee->salaryComputation->basic_salary;
        } elseif ($period->type == Period::TYPE_SEMI_MONTHLY) {
            $basicSalary = $employee->salaryComputation->basic_salary / 2;
        } elseif ($period->type == Period::TYPE_WEEKLY) {
            $basicSalary = $employee->salaryComputation->basic_salary / 4.33;
        } else {
            throw new Exception("Invalid period type: " . $period->type);
        }

        $grossPay = $basicSalary + $totalOverTimePay - $totalLateDeductions - $totalUnderTimeDeductions;

        $pagIbigContribution = $this->pagibig->compute($grossPay);
        $philHealthContribution = $this->philhealth->compute($grossPay);
        $sssContribution = $this->sss->compute($grossPay);

        $totalContributions = $pagIbigContribution + $philHealthContribution + $sssContribution;

        $taxableIncome = $grossPay - $totalContributions;

        $incomeTax = $this->tax->compute($taxableIncome);
        $netPay = $grossPay - $totalContributions - $incomeTax;

        return Payroll::create([
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
            'net_salary' => $netPay
        ]);
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

    private function timeOnlyFormat($time): Carbon
    {
        return Carbon::parse(Carbon::parse($time)->format('H:i:s'));
    }
}

