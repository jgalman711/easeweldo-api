<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'allowances' => 'json',
        'other_compensations' => 'json'
    ];

    protected $fillable = [
        'employee_id',
        'period_id',
        'description',
        'basic_salary',
        'hours_worked',
        'expected_hours_worked',
        'overtime_minutes',
        'overtime_pay',
        'overtime_pay_ytd',
        'late_minutes',
        'late_deductions',
        'late_deductions_ytd',
        'absent_minutes',
        'absent_deductions',
        'absent_deductions_ytd',
        'undertime_minutes',
        'undertime_deductions',
        'undertime_deductions_ytd',
        'leaves',
        'allowances',
        'commissions',
        'other_compensations',
        'regular_holiday_hours',
        'regular_holiday_hours_worked',
        'regular_holiday_hours_pay',
        'regular_holiday_hours_pay_ytd',
        'special_holiday_hours',
        'special_holiday_hours_worked',
        'special_holiday_hours_pay',
        'special_holiday_hours_pay_ytd',
        'sss_contributions',
        'sss_contributions_ytd',
        'philhealth_contributions',
        'philhealth_contributions_ytd',
        'pagibig_contributions',
        'pagibig_contributions_ytd',
        'total_contributions',
        'total_contributions_ytd',
        'gross_income',
        'gross_income_ytd',
        'taxable_income',
        'taxable_income_ytd',
        'income_tax',
        'income_tax_ytd',
        'net_salary',
        'net_income_ytd'
    ];

    public function getLeavesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setLeavesAttribute($value)
    {
        $this->attributes['leaves'] = json_encode($value);
    }

    public function getAllowancesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setAllowancesAttribute($value)
    {
        $this->attributes['allowances'] = json_encode($value);
    }

    public function getOtherCompensationsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setOtherCompensationsAttribute($value)
    {
        $this->attributes['other_compensations'] = json_encode($value);
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
