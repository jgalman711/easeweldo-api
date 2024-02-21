<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Contributions\ContributionsService;
use Exception;

class GeneratePayrollService
{
    public $payroll;

    protected const CYCLE_DIVISOR = [
        Period::TYPE_WEEKLY => 4,
        Period::TYPE_SEMI_MONTHLY => 2,
        Period::TYPE_MONTHLY => 1,
    ];

    protected $contributionsService;

    public function __construct(ContributionsService $contributionsService)
    {
        $this->contributionsService = $contributionsService;
    }

    public function generate(Company $company, Period $period, Employee $employee): Payroll
    {
        $salaryComputation = $employee->salaryComputation;
        throw_unless(
            $salaryComputation,
            new Exception("Salary computation data of employee {$employee->fullName} (ID:$employee->id) not found.")
        );

        $companySettings = $company->setting;
        throw_unless(
            $companySettings,
            new Exception("Company settings not available.")
        );

        $data = [
            'payroll_number' => $this->generatePayrollNumber($company->name, $employee->id, $period->id),
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'type' => PayrollEnumerator::TYPE_REGULAR,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'description' => "Payroll for {$period->salary_date}",
            'basic_salary' => $salaryComputation->basic_salary / self::CYCLE_DIVISOR[$companySettings->period_cycle],
            'pay_date' => $period->salary_date,
            'period_cycle' => $companySettings->period_cycle,
            'taxable_earnings' => $salaryComputation->taxable_earnings,
            'non_taxable_earnings' => $salaryComputation->non_taxable_earnings,
        ];
        $this->payroll = new Payroll($data);
        $this->calculateLeaves($salaryComputation->hourly_rate);
        $this->calculateContributions($companySettings->period_cycle);
        $this->payroll->save();
        return $this->payroll;
    }

    protected function calculateLeaves(float $hourlyRate): void
    {
        $leaves = $this->payroll->employee->leaves()->where([
            ['date', '>=', $this->payroll->period->start_date],
            ['date', '<=', $this->payroll->period->end_date]
        ])->get();

        $leavePay = 0;
        if ($leaves->isNotEmpty()) {
            foreach ($leaves as $leave) {
                $pay = $leave->hours * $hourlyRate;
                $transformedLeaves[] = [
                    'type' => $leave->type,
                    'date' => $leave->date,
                    'hours' => $leave->hours,
                    'pay' => $pay
                ];
                $leavePay += $pay;
            }
            $this->payroll->leaves = $transformedLeaves;
            $this->payroll->leaves_pay = $leavePay;
        }
    }

    protected function calculateContributions(string $periodCycle): void
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
            ->compute($this->payroll->gross_income, $periodCycle);
    }

    protected function generatePayrollNumber(string $companyName, int $employeeId, int $periodId)
    {
        $companyInitials = substr(str_replace(' ', '', strtoupper($companyName)), 0, 3);
        $employeeId = str_pad($employeeId, 5, '0', STR_PAD_LEFT);
        $periodId = str_pad($periodId, 5, '0', STR_PAD_LEFT);
        $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $companyInitials . date('Ymd') . '-' . $periodId . $employeeId . '-' . $randomNumber;
    }
}
