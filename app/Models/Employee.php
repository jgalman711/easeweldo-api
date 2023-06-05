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

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'department',
        'job_title',
        'employment_status',
        'mobile_number',
        'address_line',
        'barangay_town_city_province',
        'date_of_hire',
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
