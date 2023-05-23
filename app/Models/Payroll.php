<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'employee_id',
        'period_id',
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
        'status'
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
