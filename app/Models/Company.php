<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    protected $fillable = [
        'name',
        'status'
    ];

    public function employees(): HasMany
    {
        return $this->HasMany(Employee::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
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
        $period = $this->periods->where('id', $periodId)->first();
        if (!$period) {
            throw new \Exception('Period not found');
        }
        return $period;
    }

    public function getWorkScheduleById(int $workScheduleId): ?WorkSchedule
    {
        return $this->workSchedules->where('id', $workScheduleId)->first();
    }
}
