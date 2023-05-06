<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComputation extends Model
{
    use HasFactory;

    public const TYPICAL_WORK_DAYS_PER_MONTH = 22;
    public const EIGHT_HOURS = 8;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'overtime_rate',
        'night_diff_rate',
        'regular_holiday_rate',
        'special_holiday_rate',
        'tax_rate',
        'sss_contribution',
        'pagibig_contribution',
        'philhealth_contribution'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function getDailySalary()
    {
        return $this->basic_salary / self::TYPICAL_WORK_DAYS_PER_MONTH;
    }

    public function getHourlySalary()
    {
        return $this->getDailySalary() / self::EIGHT_HOURS;
    }
}
