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

    protected $fillable = [
        'employee_id',
        'period_id',
        'description',
        'basic_salary',
        'overtime_minutes',
        'overtime_pay',
        'late_minutes',
        'late_deductions',
        'absent_minutes',
        'absent_deductions',
        'undertime_minutes',
        'undertime_deductions',
        'leave_minutes',
        'leave_pay',
        'regular_holiday_minutes_pay',
        'regular_holiday_worked_minutes',
        'regular_holiday_worked_minutes_pay',
        'special_holiday_minutes_pay',
        'special_holiday_worked_minutes',
        'special_holiday_worked_minutes_pay',
        'sss_contributions',
        'philhealth_contributions',
        'pagibig_contributions',
        'total_contributions',
        'taxable_income',
        'income_tax',
        'net_salary'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
