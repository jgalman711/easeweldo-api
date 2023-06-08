<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;
    
    public const TYPE_REGULAR = "regular";

    public const TYPE_THIRTEENTH_MONTH_PAY = "thirteenth_month_pay";

    protected $fillable = [
        'employee_id',
        'period_id',
        'description',
        'basic_salary',
        'total_late_minutes',
        'total_late_deductions',
        'total_absent_days',
        'total_absent_deductions',
        'total_overtime_minutes',
        'total_overtime_pay',
        'total_undertime_minutes',
        'total_undertime_deductions',
        'total_leave_hours',
        'total_leave_compensation',
        'sss_contribution',
        'philhealth_contribution',
        'pagibig_contribution',
        'total_contributions',
        'taxable_income',
        'base_tax',
        'compensation_level',
        'tax_rate',
        'income_tax',
        'net_salary',
        'status',
        'remarks'
    ];

    protected $attributes = [
        'total_late_minutes' => 0,
        'total_late_deductions' => 0,
        'total_absent_days' => 0,
        'total_absent_deductions' => 0,
        'total_overtime_minutes' => 0,
        'total_overtime_pay' => 0,
        'total_undertime_minutes' => 0,
        'total_undertime_deductions' => 0,
        'total_leave_hours' => 0,
        'total_leave_compensation' => 0,
        'sss_contribution' => 0,
        'philhealth_contribution' => 0,
        'pagibig_contribution' => 0,
        'total_contributions' => 0,
        'taxable_income' => 0,
        'base_tax' => 0,
        'compensation_level' => 0,
        'tax_rate' => 0,
        'income_tax' => 0,
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
