<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class EditPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employeeId' => $this->employee_id,
            'periodId' => $this->period_id,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'payDate' => $this->pay_date,
            'basicSalary' => $this->basic_salary,
            'regularEarnings' => self::getRegularEarnings(),
            'otherEarnings' => self::getOtherEarnings(),
            'taxesAndContributions' => self::getTaxesAndContributions(),
            'deductions' => self::getDeductions()
        ];
    }

    public function getRegularEarnings(): array
    {
        $regularEarnings = [];
        $regularEarnings['overtime'] = $this->attendance_earnings['overtime'] ?? null;
        $regularEarnings['regularHolidayWorked'] = $this->holidays['regularWorked'] ?? null;
        $regularEarnings['specialHoliday'] = $this->holidays['special'] ?? null;
        $regularEarnings['specialHolidayWorked'] = $this->holidays['specialWorked'] ?? null;
        $regularEarnings['sickLeave'] = $this->leaves['sick'] ?? null;
        $regularEarnings['vacationLeave'] = $this->leaves['vacation'] ?? null;
        return $regularEarnings;
    }

    public function getOtherEarnings(): array
    {
        $otherEarnings = [];
        $otherEarnings['taxableEarnings'] = $this->taxable_earnings ?? null;
        $otherEarnings['nonTaxableEarnings'] = $this->non_taxable_earnings ?? null;
        return $otherEarnings;
    }

    public function getTaxesAndContributions(): array
    {
        $contributions = [];
        $contributions['sssContributions'] = $this->sss_contributions ?? null;
        $contributions['philhealthContributions'] = $this->philhealth_contributions ?? null;
        $contributions['pagibigContributions'] = $this->pagibig_contributions ?? null;
        $contributions['withheldTax'] = $this->withheld_tax ?? null;
        return $contributions;
    }

    public function getDeductions(): array
    {
        $deductions = [];
        $deductions['late'] = $this->attendance_earnings['late'] ?? null;
        $deductions['absent'] = $this->attendance_earnings['absent'] ?? null;
        $deductions['undertime'] = $this->attendance_earnings['undertime'] ?? null;
        return $deductions;
    }
}
