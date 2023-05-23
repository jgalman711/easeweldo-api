<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollTaxesContributions;
use App\Models\Period;
use App\Services\Contributions\PagIbigService;
use App\Services\Contributions\PhilHealthService;
use App\Services\Contributions\SSSService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public const FREQUENCY_SEMI_MONTHLY = 2;

    public const FREQUENCY_WEEKLY = 4.33;

    private const MINUTES_60 = 60;

    protected $timeRecordService;

    protected $pagIbigService;

    protected $philHealthService;

    protected $sssService;

    protected $taxService;

    public function __construct(
        TimeRecordService $timeRecordService,
        PagIbigService $pagIbigService,
        PhilHealthService $philHealthService,
        SSSService $sssService,
        TaxService $taxService
    ) {
        $this->timeRecordService = $timeRecordService;
        $this->pagIbigService = $pagIbigService;
        $this->philHealthService = $philHealthService;
        $this->sssService = $sssService;
        $this->taxService = $taxService;
    }

    public function generate(Period $period, Employee $employee): Payroll
    {
        $this->validate($period, $employee);
        $timeRecords = $this->timeRecordService->getTimeRecordsByDateRange(
            $employee,
            $period->start_date,
            $period->end_date
        );

        list(
            $absences,
            $absentHours,
            $lateMinutes,
            $overtimeMinutes,
            $undertimeMinutes,
            $hoursWorkedMinutes,
            $totalExpectedWorkedHours
        ) = $this->calculateAttendanceRecords($timeRecords);
        
        $salaryComputation = $employee->salaryComputation;

        if (!$salaryComputation->basic_salary && $salaryComputation->hourly_rate) {
            $basicPay = $totalExpectedWorkedHours * $salaryComputation->hourly_rate;
        } elseif ($period->type == Period::TYPE_MONTHLY) {
            $basicPay = $salaryComputation->basic_salary;
        } elseif ($period->type == Period::TYPE_SEMI_MONTHLY) {
            $basicPay = $salaryComputation->basic_salary / self::FREQUENCY_SEMI_MONTHLY;
        } elseif ($period->type == Period::TYPE_WEEKLY) {
            $basicPay = $salaryComputation->basic_salary / self::FREQUENCY_WEEKLY;
        } else {
            throw new Exception("Invalid period type");
        }

        $absencesDeductions = $absentHours * $salaryComputation->hourly_rate;
        $latesDeductions = $lateMinutes / self::MINUTES_60 * $salaryComputation->hourly_rate;
        $undertimeDeductions = $undertimeMinutes / self::MINUTES_60 * $salaryComputation->hourly_rate;
        $overtimeCompensation = $overtimeMinutes / self::MINUTES_60 * $salaryComputation->hourly_rate;

        $grossPay = $basicPay - $absencesDeductions - $latesDeductions - $undertimeDeductions + $overtimeCompensation;

        $calculateContributions = $this->calculateContributions($grossPay, $period->type);

        $taxableIncome = $grossPay - $calculateContributions['total'];

        $incomeTax = $this->taxService->compute($taxableIncome, $period->type);
        $netPay = $taxableIncome - $incomeTax;

        try {
            DB::beginTransaction();
            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'period_id' => $period->id,
                'basic_salary' => $basicPay,
                'total_late_minutes' => $lateMinutes,
                'total_late_deductions' => $latesDeductions,
                'total_absent_days' => $absences,
                'total_absent_deductions' => $absencesDeductions,
                'total_overtime_minutes' =>  $overtimeMinutes,
                'total_overtime_pay' => $overtimeCompensation,
                'total_undertime_minutes' => $undertimeMinutes,
                'total_undertime_deductions' => $undertimeDeductions,
                'total_hours_worked' => $hoursWorkedMinutes / self::MINUTES_60,
                'sss_contribution' => $calculateContributions['sss'],
                'philhealth_contribution' => $calculateContributions['philHealth'],
                'pagibig_contribution' => $calculateContributions['pagIbig'],
                'total_contributions' => $calculateContributions['total'],
                'taxable_income' => $taxableIncome,
                'base_tax' => $this->taxService->getBaseTax(),
                'compensation_level' => $this->taxService->getCompensationLevel(),
                'tax_rate' => $this->taxService->getTaxRate(),
                'income_tax' => $incomeTax,
                'net_salary' => $netPay
            ]);
            PayrollTaxesContributions::create([
                'payroll_id' => $payroll->id,
                'company_id' => $employee->company->id,
                'withholding_tax' => $this->taxService->getBaseTax(),
                'sss_contribution' => $this->sssService->getEmployerShare(),
                'pagibig_contribution' => $this->pagIbigService->getEmployerShare()
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $payroll;
    }

    private function validate(Period $period, Employee $employee): void
    {
        throw_if($period->status == Period::STATUS_COMPLETED, new Exception('Period is already ' . $period->status));

        throw_unless(
            $employee->salaryComputation,
            new Exception('No available salary details for ' . $employee->fullName)
        );

        $payroll= $employee->payrolls->firstWhere('period_id', $period->id);
        throw_if($payroll, new Exception('Payroll already exists.'));
    }

    private function calculateAttendanceRecords(Collection $timeRecords): array
    {
        $absences = 0;
        $absentHours = 0;
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $undertimeMinutes = 0;
        $hoursWorkedMinutes = 0;
        $totalExpectedWorkedHours = 0;
        $expectedWorkedHours = 0;

        foreach ($timeRecords as $timeRecord) {
            $expectedClockIn = Carbon::parse($timeRecord->expected_clock_in);
            $expectedClockOut = Carbon::parse($timeRecord->expected_clock_out);
            $clockIn = Carbon::parse($timeRecord->clock_in);
            $clockOut = Carbon::parse($timeRecord->clock_out);
            $expectedWorkedHours = $expectedClockIn->diffInHours($expectedClockOut);
            $totalExpectedWorkedHours += $expectedWorkedHours;
            if (!$timeRecord->clock_in && !$timeRecord->clock_out) {
                $absences++;
                $absentHours += $expectedWorkedHours;
            } else {
                $lateMinutes += $clockIn->gt($expectedClockIn)
                    ? $clockIn->diffInMinutes($expectedClockIn): 0;
                $undertimeMinutes += $clockOut->lt($expectedClockOut)
                    ? $clockOut->diffInMinutes($expectedClockOut) : 0;
                $overtimeMinutes += $clockOut->gt($expectedClockOut)
                    ? $clockOut->diffInMinutes($expectedClockOut) : 0;
                $hoursWorkedMinutes += $clockIn->diffInMinutes($clockOut);
            }
        }

        return [
            $absences,
            $absentHours,
            $lateMinutes,
            $overtimeMinutes,
            $undertimeMinutes,
            $hoursWorkedMinutes,
            $totalExpectedWorkedHours,
            $expectedWorkedHours
        ];
    }

    private function calculateContributions(float $grossPay, string $periodType): array
    {

        $sssContribution = $this->sssService->compute($grossPay);
        $pagIbigContribution = $this->pagIbigService->compute($grossPay);
        $philHealthContribution = $this->philHealthService->compute($grossPay);

        if ($periodType == Period::TYPE_SEMI_MONTHLY) {
            $sssContribution = $sssContribution / self::FREQUENCY_SEMI_MONTHLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_SEMI_MONTHLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_SEMI_MONTHLY;
        } elseif ($periodType == Period::TYPE_WEEKLY) {
            $sssContribution = $sssContribution / self::FREQUENCY_WEEKLY;
            $pagIbigContribution = $pagIbigContribution / self::FREQUENCY_WEEKLY;
            $philHealthContribution = $philHealthContribution / self::FREQUENCY_WEEKLY;
        }

        $totalContributions = $pagIbigContribution + $philHealthContribution + $sssContribution;

        return [
            'pagIbig' => $pagIbigContribution,
            'philHealth' => $philHealthContribution,
            'sss' => $sssContribution,
            'total' => $totalContributions
        ];
    }
}
