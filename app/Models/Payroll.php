<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;
    
    public const TYPE_REGULAR = 'regular';

    public const TYPE_THIRTEENTH_MONTH_PAY = 'thirteenth_month_pay';

    public const TYPE_FINAL_PAY = 'final_pay';

    protected $casts = [
        'holidays' => 'json',
        'leaves' => 'json',
        'taxable_earnings' => 'json',
        'non_taxable_earnings' => 'json'
    ];

    protected $hidden = [
        'holidays'
    ];

    protected $fillable = [
        'employee_id',
        'period_id',
        'description',
        'overtime_minutes',
        'late_minutes',
        'absent_minutes',
        'undertime_minutes',
        'regular_holiday_hours_worked',
        'special_holiday_hours_worked',
        'leaves',
        'taxable_earnings',
        'non_taxable_earnings',
        'holidays',
        'remarks'
    ];

    protected $appends = [
        'regular_holiday_hours_worked',
        'regular_holiday_hours_worked_pay',
        'regular_holiday_hours',
        'regular_holiday_hours_pay',
        'special_holiday_hours_worked',
        'special_holiday_hours_worked_pay',
        'special_holiday_hours',
        'special_holiday_hours_pay',
        'total_non_taxable_earnings',
        'total_taxable_earnings',
        'total_contributions',
        'total_deductions',
        'gross_income',
        'taxable_income',
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

    public function getLeavesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->sss_contributions + $this->philhealth_contributions + $this->pagibig_contributions;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->absent_deductions - $this->undertime_deductions - $this->late_deductions;
    }

    public function getGrossIncomeAttribute(): float
    {
        return $this->basic_salary - $this->total_deductions + $this->overtime_pay;
    }

    public function getTaxableIncomeAttribute(): float
    {
        return $this->gross_income - $this->total_contributions + $this->total_taxable_earnings;
    }

    public function getNetIncomeAttribute(): float
    {
        return $this->taxable_income - $this->withheld_tax + $this->total_non_taxable_earnings;
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

    public function getRegularHolidayHoursAttribute(): float
    {
        return optional(optional(optional($this->holidays))[Holiday::REGULAR_HOLIDAY])['hours'];
    }

    public function getRegularHolidayHoursPayAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::REGULAR_HOLIDAY])['hours_pay'];
    }

    public function getRegularHolidayHoursWorkedAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::REGULAR_HOLIDAY])['hours_worked'];
    }

    public function getRegularHolidayHoursWorkedPayAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::REGULAR_HOLIDAY])['hours_worked_pay'];
    }

    public function getSpecialHolidayHoursAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::SPECIAL_HOLIDAY])['hours'];
    }

    public function getSpecialHolidayHoursPayAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::SPECIAL_HOLIDAY])['hours_pay'];
    }

    public function getSpecialHolidayHoursWorkedAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::SPECIAL_HOLIDAY])['hours_worked'];
    }

    public function getSpecialHolidayHoursWorkedPayAttribute(): float
    {
        return optional(optional($this->holidays)[Holiday::SPECIAL_HOLIDAY])['hours_worked_pay'];
    }
}
