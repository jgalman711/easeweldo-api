<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;
    
    public const TYPE_REGULAR = 'regular';

    public const TYPE_THIRTEENTH_MONTH_PAY = 'thirteenth_month_pay';

    public const TYPE_FINAL_PAY = 'final_pay';

    protected $casts = [
        'leaves' => 'json',
        'allowances' => 'json',
        'commissions' => 'json',
        'other_compensations' => 'json',
        'non_taxable_earnings' => 'json'
    ];

    protected $fillable = [
        'employee_id',
        'period_id'
    ];

    public function getLeavesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setLeavesAttribute($value)
    {
        $this->attributes['leaves'] = json_encode($value);
    }

    public function getAllowancesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setAllowancesAttribute($value)
    {
        $this->attributes['allowances'] = json_encode($value);
    }

    public function getOtherCompensationsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setOtherCompensationsAttribute($value)
    {
        $this->attributes['other_compensations'] = json_encode($value);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
