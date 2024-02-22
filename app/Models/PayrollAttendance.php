<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'period_id',
        'type',
        'date',
        'hours',
        'amount'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
