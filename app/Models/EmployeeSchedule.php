<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'work_schedule_id',
        'start_date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
