<?php

namespace App\Models;

use App\Enumerators\AttendanceEarningsEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\StateMachines\Contracts\PayrollStateContract;
use App\StateMachines\Payroll\BaseState;
use App\StateMachines\Payroll\ToPayState;
use App\Traits\PayrollJsonParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use PayrollJsonParser, SoftDeletes;

    protected $casts = [
        'attendance_earnings' => 'json',
        'holidays' => 'json',
        'holidays_worked' => 'json',
        'leaves' => 'json',
        'taxable_earnings' => 'json',
        'non_taxable_earnings' => 'json',
        'other_deductions' => 'json',
    ];

    protected $fillable = [
        'payroll_number',
        'employee_id',
        'period_id',
        'type',
        'status',
        'description',
        'pay_date',
        'basic_salary',
        'attendance_earnings',
        'leaves',
        'taxable_earnings',
        'non_taxable_earnings',
        'other_deductions',
        'holidays',
        'holidays_worked',
        'sss_contributions',
        'philhealth_contributions',
        'pagibig_contributions',
        'withheld_tax',
        'remarks',
    ];

    protected $appends = [
        'total_attendance_earnings',
        'total_attendance_deductions',
        'total_holidays_pay',
        'total_holidays_worked_pay',
        'total_leaves_pay',
        'total_other_deductions',
        'total_non_taxable_earnings',
        'total_taxable_earnings',
        'total_contributions',
        'total_deductions',
        'gross_income',
        'taxable_income',
        'net_taxable_income',
        'net_income',
    ];

    public function state(): PayrollStateContract
    {
        return match ($this->status) {
            PayrollEnumerator::STATUS_TO_PAY => new ToPayState($this),
            default => new BaseState($this)
        };
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function getTotalAttendanceEarningsAttribute(): ?float
    {
        if (isset($this->attendance_earnings['overtime'])) {
            return $this->totalAmountParser($this->attendance_earnings['overtime'] ?? []);
        }

        return null;
    }

    public function getTotalAttendanceDeductionsAttribute(): ?float
    {
        foreach (AttendanceEarningsEnumerator::DEDUCTION_TYPES as $type) {
            return $this->totalAmountParser($this->attendance_earnings[$type] ?? []);
        }

        return null;
    }

    public function getTotalHolidaysPayAttribute(): ?float
    {
        return $this->totalAmountParser($this->holidays);
    }

    public function getTotalHolidaysWorkedPayAttribute(): ?float
    {
        return $this->totalAmountParser($this->holidays_worked);
    }

    public function getTotalLeavesPayAttribute(): ?float
    {
        return $this->totalAmountParser($this->leaves);
    }

    public function getTotalOtherDeductionsAttribute(): float
    {
        return $this->totalAmountParser($this->other_deductions);
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->sss_contributions + $this->philhealth_contributions + $this->pagibig_contributions;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->total_other_deductions + $this->total_attendance_deductions + $this->withheld_tax;
    }

    public function getGrossIncomeAttribute(): float
    {
        $grossIncome = $this->basic_salary +
            $this->total_leaves_pay +
            $this->total_holidays_pay +
            $this->total_holidays_worked_pay +
            $this->total_attendance_earnings +
            $this->total_non_taxable_earnings +
            $this->total_taxable_earnings -
            $this->total_attendance_deductions;

        return max(0, $grossIncome);
    }

    public function getTaxableIncomeAttribute(): float
    {
        $taxableIncome = $this->gross_income - $this->total_contributions;

        return $taxableIncome >= 0 ? round($taxableIncome, 2) : 0;
    }

    public function getNetTaxableIncomeAttribute(): float
    {
        return round($this->taxable_income - $this->withheld_tax, 2);
    }

    public function getNetIncomeAttribute(): float
    {
        return round($this->net_taxable_income + $this->total_non_taxable_earnings, 2);
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
}
