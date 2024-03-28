<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'period_cycle',
        'salary_day',
        'grace_period',
        'minimum_overtime',
        'is_ot_auto_approve',
        'overtime_rate',
        'auto_send_email_to_bank',
        'auto_pay_disbursement',
        'clock_action_required',
        'disbursement_method',
        'leaves_convertible',
    ];

    protected $casts = [
        'salary_day' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
