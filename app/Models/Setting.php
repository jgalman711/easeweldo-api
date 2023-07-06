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
        'is_ot_auto_approve'
    ];

    protected $casts = [
        'salary_day' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
