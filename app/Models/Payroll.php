<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_id',
        'total_earnings',
        'overtime',
        'total_hours_worked',
        'night_diff',
        'total_deductions',
        'pag_ibig',
        'philhealth',
        'sss',
        'net_pay',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
