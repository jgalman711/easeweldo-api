<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComputation extends Model
{
    use HasFactory;

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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function timeRecords()
    {
        return $this->hasMany(TimeRecord::class);
    }
}
