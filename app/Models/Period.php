<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';

    public const STATUSES = [
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_PENDING
    ];

    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_SEMI_MONTHLY = 'semi-monthly';
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_FINAL = 'final';
    public const TYPE_NTH_MONTH_PAY = 'nth_month_pay';

    public const TYPES = [
        self::TYPE_MONTHLY,
        self::TYPE_SEMI_MONTHLY,
        self::TYPE_WEEKLY,
        self::TYPE_FINAL,
        self::TYPE_NTH_MONTH_PAY
    ];

    public const PERIODIC_TYPES = [
        self::TYPE_MONTHLY,
        self::TYPE_SEMI_MONTHLY,
        self::TYPE_WEEKLY
    ];

    public const ALLOWED_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    protected $fillable = [
        'name',
        'company_id',
        'company_period_number',
        'payroll_cost',
        'employees_count',
        'employees_net_pay',
        'withheld_taxes',
        'total_contributions',
        'description',
        'type',
        'start_date',
        'end_date',
        'salary_date',
        'status'
    ];

    protected $appends = [
        'next_period',
        'previous_period',
        'employees_count',
        'employees_net_pay',
        'withheld_taxes',
        'total_contributions',
        'payroll_cost'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function getEmployeesCountAttribute(): int
    {
        return $this->payrolls->count();
    }

    public function getEmployeesNetPayAttribute(): float
    {
        return $this->payrolls->sum('net_income');
    }

    public function getWithheldTaxesAttribute(): float
    {
        return $this->payrolls->sum('withheld_tax');
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->payrolls->sum('total_contributions');
    }

    public function getPayrollCostAttribute(): float
    {
        return $this->employees_net_pay +  $this->withheld_taxes +  $this->total_contributions;
    }

    public function getNextPeriodAttribute()
    {
        $next = Period::where('start_date', '>', $this->end_date)
            ->orderBy('start_date', 'asc')
            ->first();
        return  optional($next)->id;
    }

    public function getPreviousPeriodAttribute()
    {
        $previous = Period::where('end_date', '<', $this->start_date)
            ->orderBy('start_date', 'desc')
            ->first();
        return optional($previous)->id;
    }

    public function scopeByRange(Builder $periodsQuery, array $range): Builder
    {
        return $periodsQuery->when(isset($range['dateTo']) && $range['dateTo'], function ($query) use ($range) {
            $query->whereDate('start_date', '>=', $range['dateTo']);
        })->when(isset($range['dateFrom']) && $range['dateFrom'], function ($query) use ($range) {
            $query->whereDate('end_date', '<=', $range['dateFrom']);
        });
    }
}
