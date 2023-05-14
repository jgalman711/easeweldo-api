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
        $timeRecords = $this->getTimeRecords($employee, $period->start_date, $period->end_date);

        $attendanceSummary = $this->calculateAttendanceSummary($timeRecords);

        $hourlyRate = $employee->salaryComputation->getHourlySalary();
        $totalAbsentDeductions = $attendanceSummary['absences'] * $employee->salaryComputation->getDailySalary();
        $totalLateDeductions = round($hourlyRate * ($attendanceSummary['totalMinutesLate'] / 60), 2);
        $totalUnderTimeDeductions = round($hourlyRate * ($attendanceSummary['totalUnderTime'] / 60), 2);
        $totalHourOverTime = $attendanceSummary['totalOvertime'] / 60;
        $totalOverTimePay = round($totalHourOverTime * $hourlyRate * $employee->salaryComputation->overtime_rate, 2);
        
        $basicSalary = $employee->salaryComputation->basic_salary;
        $grossPay = $basicSalary + $totalOverTimePay - $totalLateDeductions - $totalUnderTimeDeductions;

        //$totalNightDiffPay = TBD

        $contributionSummary = $this->calculateContributionSummary($basicSalary, $period->type);

        $taxableIncome = $grossPay - $contributionSummary['totalContributions'];

        $incomeTax = $this->tax->compute($taxableIncome, $period->type);
        $netPay = $taxableIncome - $incomeTax;

        try {
            DB::beginTransaction();
            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'period_id' => $period->id,
                'basic_salary' => $basicSalary,
                'total_late_minutes' => $attendanceSummary['totalMinutesLate'],
                'total_late_deductions' => $totalLateDeductions,
                'total_absent_days' => $attendanceSummary['absences'],
                'total_absent_deductions' => $totalAbsentDeductions,
                'total_overtime_minutes' => $attendanceSummary['totalOvertime'],
                'total_overtime_pay' => $totalOverTimePay,
                'total_undertime_minutes' => $attendanceSummary['totalUnderTime'],
                'total_undertime_deductions' => $totalUnderTimeDeductions,
                'sss_contribution' => $contributionSummary['sssContribution'],
                'philhealth_contribution' => $contributionSummary['philHealthContribution'],
                'pagibig_contribution' => $contributionSummary['pagIbigContribution'],
                'total_contributions' => $contributionSummary['totalContributions'],
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

    public function calculateAttendanceSummary(Collection $timeRecords): array
    {
        $totalMinutesLate = 0;
        $totalUnderTime = 0;
        $totalOvertime = 0;
        $absences = 0;
        $summary = [];
        foreach ($timeRecords as $date => $group) {
            $lateMinutes = 0;
            $undertimeMinutes = 0;
            $absent = true;
            $overtimeMinutes = 0;

            $expectedClockIn = null;
            $expectedClockOut = null;
            foreach ($group as $record) {
                $clockIn = $this->timeOnlyFormat($record->clock_in);
                $clockOut = $this->timeOnlyFormat($record->clock_out);
                list($expectedClockIn, $expectedClockOut) = $this->getExpectedClocks($record);
                if ($clockOut) {
                    $absent = false;
                    $lateMinutes += $this->computeLateMinutes($clockIn, $expectedClockIn);
                    $undertimeMinutes += $this->computeUndertimeMinutes($clockOut, $expectedClockOut);
                } else {
                    $absent = true;
                    $lateMinutes += $this->computeLateMinutes($clockIn, $expectedClockIn);
                }
            }
            if (!$absent) {
                $overtimeMinutes = $this->computeOvertimeMinutes($clockOut, $expectedClockOut);
            } else {
                $absences++;
            }
            $totalMinutesLate += $lateMinutes;
            $totalUnderTime += $undertimeMinutes;
            $totalOvertime += $overtimeMinutes;
            $summary[$date] = [
                'lateMinutes' => $lateMinutes,
                'undertimeMinutes' => $undertimeMinutes,
                'overtimeMinutes' => $overtimeMinutes
            ];
        }
        $summary['totalMinutesLate'] = $totalMinutesLate;
        $summary['totalUnderTime'] = $totalUnderTime;
        $summary['totalOvertime'] = $totalOvertime;
        $summary['absences'] = $absences;
        return $summary;
    }

    public function calculateContributionSummary(float $basicSalary, string $periodType): array
    {
        $sssContribution = $this->sss->compute($basicSalary);
        $pagIbigContribution = $this->pagibig->compute($basicSalary);
        $philHealthContribution = $this->philhealth->compute($basicSalary);

        if ($periodType == Period::TYPE_SEMI_MONTHLY) {
            $basicSalary = $basicSalary / self::FREQUENCY_SEMI_MONTHLY;
            $sssContribution = $sssContribution / self::FREQUENCY_SEMI_MONTHLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_SEMI_MONTHLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_SEMI_MONTHLY;
        } elseif ($periodType == Period::TYPE_WEEKLY) {
            $basicSalary = $basicSalary / self::FREQUENCY_WEEKLY;
            $sssContribution = $sssContribution / self::FREQUENCY_WEEKLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_WEEKLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_WEEKLY;
        }

        $totalContributions = $pagIbigContribution + $philHealthContribution + $sssContribution;

        return [
            'pagIbigContribution' => $pagIbigContribution,
            'philHealthContribution' => $philHealthContribution,
            'sssContribution' => $sssContribution,
            'totalContributions' => $totalContributions
        ];
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
            ->orderBy('clock_in')
            ->get()
            ->groupBy(function ($record) {
                return Carbon::parse($record->clock_in)->format('Y-m-d');
            });
    }

    private function getExpectedClocks(TimeRecord $record): array
    {
        if ($record->attendance_status == 'leave') {
            $expectedClockIn = $this->timeOnlyFormat($record->clock_in);
            $expectedClockOut = $this->timeOnlyFormat($record->clock_out);
        } else {
            $expectedClockIn = $this->timeOnlyFormat($record->expected_clock_in);
            $expectedClockOut = $this->timeOnlyFormat($record->expected_clock_out);
        }
        return [$expectedClockIn, $expectedClockOut];
    }

    private function timeOnlyFormat($time): Carbon
    {
        return Carbon::parse(Carbon::parse($time)->format('H:i:s'));
    }

    private function computeLateMinutes(Carbon $clockIn, Carbon $expectedClockIn): string
    {
        return $clockIn->gt($expectedClockIn) ? $clockIn->diffInMinutes($expectedClockIn) : 0;
    }

    private function computeUndertimeMinutes(Carbon $clockOut, Carbon $expectedClockOut): string
    {
        return $clockOut->lt($expectedClockOut) ? $expectedClockOut->diffInMinutes($clockOut) : 0;
    }

    private function computeOvertimeMinutes(Carbon $actualClockOut, Carbon $expectedClockOut): float
    {
        return $actualClockOut->greaterThan($expectedClockOut)
                && ($actualClockOut->diffInMinutes($expectedClockOut) > 60)
                ? $actualClockOut->diffInMinutes($expectedClockOut) : 0;
    }
}

