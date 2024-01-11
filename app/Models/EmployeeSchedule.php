<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'work_schedule_id',
        'start_date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
