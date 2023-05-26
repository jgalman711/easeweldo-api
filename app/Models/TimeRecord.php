<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_id',
        'clock_in',
        'clock_out',
        'expected_clock_in',
        'expected_clock_out',
        'attendance_status',
        'remarks'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
