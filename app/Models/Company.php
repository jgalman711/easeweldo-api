<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const COMPANY_STORAGE_PATH = 'companies/images/';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'details',
        'logo',
        'legal_name',
        'address_line',
        'barangay_town_city_province',
        'contact_name',
        'email_address',
        'mobile_number',
        'landline_number',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'tin',
        'sss_number',
        'philhealth_number',
        'pagibig_number'
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function employees(): HasMany
    {
        return $this->HasMany(Employee::class);
    }

    public function payrolls(): HasManyThrough
    {
        return $this->hasManyThrough(Payroll::class, Employee::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function companySubscription(): HasOne
    {
        return $this->hasOne(CompanySubscription::class);
    }

    public function subscription(): HasOneThrough
    {
        return $this->hasOneThrough(
            Subscription::class,
            CompanySubscription::class,
            'company_id',
            'id',
            'id',
            'subscription_id'
        );
    }

    public function getEmployeeById(int $employeeId): ?Employee
    {
        $employee = $this->employees->where('id', $employeeId)->first();
        if (!$employee) {
            throw new \Exception('Employee not found');
        }
        return $employee;
    }

    public function getPeriodById(int $periodId): ?Period
    {
        $period =  $this->periods->where('id', $periodId)->first();
        if (!$period) {
            throw new \Exception('Period not found');
        }
        return $period;
    }

    public function getWorkScheduleById(int $workScheduleId): ?WorkSchedule
    {
        $workSchedule = $this->workSchedules->where('id', $workScheduleId)->first();
        if (!$workSchedule) {
            throw new \Exception('Work schedule not found');
        }
        return $workSchedule;
    }
}
