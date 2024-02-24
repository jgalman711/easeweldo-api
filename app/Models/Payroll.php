<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;

    protected $casts = [
        'attendance_earnings' => 'json',
        'holidays' => 'json',
        'leaves' => 'json',
        'taxable_earnings' => 'json',
        'non_taxable_earnings' => 'json'
    ];

    protected $hidden = [
        'holidays'
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
        'leaves',
        'leaves_pay',
        'taxable_earnings',
        'non_taxable_earnings',
        'holidays',
        'sss_contributions',
        'philhealth_contributions',
        'pagibig_contributions',
        'withheld_tax',
        'remarks'
    ];

    protected $appends = [
        'total_non_taxable_earnings',
        'total_taxable_earnings',
        'total_contributions',
        'gross_income',
        'taxable_income',
        'net_taxable_income',
        'net_income'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->sss_contributions + $this->philhealth_contributions + $this->pagibig_contributions;
    }

    public function getGrossIncomeAttribute(): float
    {
        $grossIncome = $this->basic_salary +
            $this->leaves_pay +
            $this->regular_holiday_hours_worked_pay +
            $this->regular_holiday_hours_pay +
            $this->special_holiday_hours_worked_pay +
            $this->special_holiday_hours_pay +
            $this->total_non_taxable_earnings +
            $this->total_taxable_earnings;
        return max(0, $grossIncome);
    }

    public function getTaxableIncomeAttribute(): float
    {
        $taxableIncome = $this->gross_income - $this->total_contributions - $this->total_non_taxable_earnings;
        return $taxableIncome >= 0 ? $taxableIncome : 0;
    }

    public function getNetTaxableIncomeAttribute(): float
    {
        return $this->taxable_income - $this->withheld_tax;
    }

    public function getNetIncomeAttribute(): float
    {
        return $this->net_taxable_income + $this->total_non_taxable_earnings;
    }

    public function getTotalTaxableEarningsAttribute(): float
    {
        $taxableEarnings = isset($this->attributes['taxable_earnings'])
            ? json_decode($this->attributes['taxable_earnings'], true)
            : [];

        $totalTaxableEarnings = 0;

        foreach ($taxableEarnings as $item) {
            if (isset($item['pay']) && is_numeric($item['pay'])) {
                $totalTaxableEarnings += $item['pay'];
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
            if (isset($item['pay']) && is_numeric($item['pay'])) {
                $totalNonTaxableEarnings += $item['pay'];
            }
        }

        return $totalNonTaxableEarnings;
    }
}
