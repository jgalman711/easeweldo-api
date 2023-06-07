<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    public const ABSOLUTE_STORAGE_PATH = 'public/employees/images';

    public const STORAGE_PATH = 'employees/images/';

    public const EMPLOYMENT_TYPE = [
        self::FULL_TIME,
        self::PART_TIME,
        self::CONTRACT
    ];

    public const FULL_TIME = 'full-time';
    public const PART_TIME = 'part-time';
    public const CONTRACT = 'contract';

    public const EMPLOYMENT_STATUS = [
        self::ACTIVE,
        self::INACTIVE,
        self::PROBATIONARY,
        self::REGULAR,
        self::TERMINATED
    ];

    public const FULL_TIME_WORKING_DAYS_PER_WEEK = [
        self::FIVE_DAYS_PER_WEEK,
        self::SIX_DAYS_PER_WEEK
    ];

    public const FIVE_DAYS_PER_WEEK = 5;
    public const SIX_DAYS_PER_WEEK = 6;
    public const AS_NEEDED = 0;

    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const PROBATIONARY = 'probationary';
    public const REGULAR = 'full-time';
    public const TERMINATED = 'part-time';

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'department',
        'job_title',
        'employment_status',
        'employment_type',
        'working_days_per_week',
        'working_hours_per_day',
        'mobile_number',
        'address_line',
        'barangay_town_city_province',
        'date_of_hire',
        'date_of_termination',
        'date_of_birth',
        'sss_number',
        'pagibig_number',
        'philhealth_number',
        'tax_identification_number',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'profile_picture'
    ];

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function salaryComputation(): HasOne
    {
        return $this->hasOne(SalaryComputation::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(WorkSchedule::class, 'employee_schedules')->withTimestamps();
    }

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function getSickLeaveAttribute(): float
    {
        return $this->salaryComputation->available_sick_leaves;
    }

    public function getVacationLeaveAttribute(): float
    {
        return $this->salaryComputation->available_vacation_leaves;
    }

    public function getEmergencyLeaveAttribute(): float
    {
        return $this->vacation_leaves;
    }

    public function getFullNameAttribute(): string
    {
        return ucfirst($this->first_name) . " " . ucfirst($this->last_name);
    }

    public function getLeaveById(int $leaveId): Leave
    {
        $leave = $this->leaves->where('id', $leaveId)->first();
        if (!$leave) {
            throw new \Exception('Leave not found');
        }
        return $leave;
    }
}
