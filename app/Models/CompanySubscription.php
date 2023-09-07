<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanySubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'subscription_id',
        'renewed_from_id',
        'status',
        'amount',
        'amount_per_employee',
        'employee_count',
        'amount_paid',
        'balance',
        'overpaid_balance',
        'months',
        'start_date',
        'end_date',
    ];

    protected $dates = ['start_date', 'end_date'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(CompanySubscription::class, 'renewed_from_id');
    }
}
