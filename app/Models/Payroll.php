<?php

namespace App\Models;

use App\Enumerators\PayrollEnumerator;
use App\StateMachines\Contracts\PayrollStateContract;
use App\StateMachines\Payroll\BaseState;
use App\StateMachines\Payroll\ToPayState;
use App\Traits\PayrollCalculator;
use App\Traits\PayslipDownloadable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use PayrollCalculator, PayslipDownloadable, SoftDeletes;

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
}
