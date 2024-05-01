<?php

namespace App\Traits;

use App\Enumerators\AttendanceEarningsEnumerator;

trait PayrollCalculator
{
    use PayrollJsonParser;

    protected function getGrossIncomeAttribute(): float
    {
        $grossIncome = $this->basic_salary
            + $this->total_taxable_earnings
            + $this->total_non_taxable_earnings
            + $this->total_leaves_pay
            + $this->total_holidays_pay
            + $this->total_holidays_worked_pay
            + $this->total_attendance_earnings;
        return max(0, $grossIncome);
    }

    public function getTotalTaxableEarningsAttribute(): float
    {
        $taxableEarnings = isset($this->attributes['taxable_earnings'])
            ? json_decode($this->attributes['taxable_earnings'], true)
            : [];

        $totalTaxableEarnings = 0;

        foreach ($taxableEarnings as $item) {
            if (isset($item['amount']) && is_numeric($item['amount'])) {
                $totalTaxableEarnings += $item['amount'];
            }
        }

        return $totalTaxableEarnings;
    }

    public function getTotalNonTaxableEarningsAttribute(): float
    {
        $nonTaxableEarnings = isset($this->attributes['non_taxable_earnings'])
            ? json_decode($this->attributes['non_taxable_earnings'], true)
            : [];

        $totalNonTaxableEarnings = 0;

        foreach ($nonTaxableEarnings as $item) {
            if (isset($item['amount']) && is_numeric($item['amount'])) {
                $totalNonTaxableEarnings += $item['amount'];
            }
        }
        return $totalNonTaxableEarnings;
    }

    public function getTotalLeavesPayAttribute(): float
    {
        return $this->totalAmountParser($this->leaves);
    }

    public function getTotalHolidaysPayAttribute(): float
    {
        return $this->totalAmountParser($this->holidays);
    }

    public function getTotalHolidaysWorkedPayAttribute(): float
    {
        return $this->totalAmountParser($this->holidays_worked);
    }

    public function getTotalAttendanceEarningsAttribute(): float
    {
        if (isset($this->attendance_earnings['overtime']) && $this->attendance_earnings['overtime']) {
            return $this->totalAmountParser($this->attendance_earnings['overtime'] ?? []);
        }
        return 0;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return +$this->total_contributions
            + $this->withheld_tax
            + $this->total_other_deductions
            + $this->total_attendance_deductions;
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->sss_contributions
            + $this->philhealth_contributions
            + $this->pagibig_contributions;
    }

    public function getTotalOtherDeductionsAttribute(): float
    {
        return $this->totalAmountParser($this->other_deductions);
    }

    public function getTotalAttendanceDeductionsAttribute(): ?float
    {
        foreach (AttendanceEarningsEnumerator::DEDUCTION_TYPES as $type) {
            return $this->totalAmountParser($this->attendance_earnings[$type] ?? []);
        }

        return null;
    }

    public function getNetIncomeAttribute(): float
    {
        return round($this->gross_income - $this->total_deductions, 2);
    }

    public function getTaxableIncomeAttribute(): float
    {
        $taxableIncome = $this->gross_income - $this->total_attendance_deductions;

        return $taxableIncome >= 0 ? round($taxableIncome, 2) : 0;
    }
}
