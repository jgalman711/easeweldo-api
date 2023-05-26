<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComputation extends Model
{
    use HasFactory;

    public const UNIT_DAY = 'day';
    public const TYPICAL_WORK_DAYS_PER_MONTH = 22;
    public const EIGHT_HOURS = 8;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'hourly_rate',
        'daily_rate',
        'overtime_rate',
        'night_diff_rate',
        'regular_holiday_rate',
        'special_holiday_rate',
        'tax_rate',
        'sss_contribution',
        'pagibig_contribution',
        'philhealth_contribution',
        'total_sick_leave_hours',
        'total_vacation_leave_hours',
        'available_sick_leave_hours',
        'available_vacation_leave_hours'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }
}