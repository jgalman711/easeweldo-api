<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryComputation extends Model
{
    use SoftDeletes;

    public const FIVE_DAYS_PER_WEEK_WORK_DAYS = 261;

    public const SIX_DAYS_PER_WEEK_WORK_DAYS = 313;

    protected $casts = [
        'taxable_earnings' => 'array',
        'non_taxable_earnings' => 'array'
    ];

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'hourly_rate',
        'daily_rate',
        'non_taxable_earnings',
        'taxable_earnings',
        'working_hours_per_day',
        'working_days_per_week',
        'overtime_rate',
        'night_diff_rate',
        'regular_holiday_rate',
        'special_holiday_rate',
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
