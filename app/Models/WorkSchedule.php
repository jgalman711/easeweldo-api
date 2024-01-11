<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkSchedule extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'company_id',
        'monday_clock_in_time',
        'monday_clock_out_time',
        'tuesday_clock_in_time',
        'tuesday_clock_out_time',
        'wednesday_clock_in_time',
        'wednesday_clock_out_time',
        'thursday_clock_in_time',
        'thursday_clock_out_time',
        'friday_clock_in_time',
        'friday_clock_out_time',
        'saturday_clock_in_time',
        'saturday_clock_out_time',
        'sunday_clock_in_time',
        'sunday_clock_out_time'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_schedules');
    }
}
