<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;
    
    public const TYPE_REGULAR = 'regular';

    public const TYPE_THIRTEENTH_MONTH_PAY = 'thirteenth_month_pay';

    public const TYPE_FINAL_PAY = 'final_pay';

    protected $casts = [
        'leaves' => 'json',
        'taxable_earnings' => 'json',
        'non_taxable_earnings' => 'json'
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
        'remarks'
    ];

    protected $appends = ['non_taxable_total_earnings', 'taxable_total_earnings'];

    public function getLeavesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setLeavesAttribute($value)
    {
        $this->attributes['leaves'] = json_encode($value);
    }

    public function getTaxableTotalEarningsAttribute()
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

    public function getNonTaxableTotalEarningsAttribute()
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
