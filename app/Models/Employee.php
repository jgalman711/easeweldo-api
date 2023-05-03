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

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'department',
        'job_title',
        'date_of_hire',
        'date_of_birth',
        'contact_number',
        'address',
        'social_security_number',
        'bank_account_number',
        'pay_rate'
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
}
