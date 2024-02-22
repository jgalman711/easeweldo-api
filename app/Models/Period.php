<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This will act as the disbursements.
 *
 */
class Period extends Model
{
    use SoftDeletes;

    public const STATUS_UNINITIALIZED = 'uninitialized';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_UNINITIALIZED,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED
    ];

    public const TYPES = [
        self::TYPE_REGULAR,
        self::TYPE_SPECIAL,
        self::TYPE_NTH_MONTH_PAY,
        self::TYPE_FINAL
    ];

    public const SUBTYPES = [
        self::SUBTYPE_SEMI_MONTHLY,
        self::SUBTYPE_MONTHLY,
        self::SUBTYPE_WEEKLY,
    ];

    public const SUBTYPE_MONTHLY = 'monthly';
    public const SUBTYPE_SEMI_MONTHLY = 'semi-monthly';
    public const SUBTYPE_WEEKLY = 'weekly';

    public const TYPE_REGULAR = 'regular';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_NTH_MONTH_PAY = 'nth_month_pay';
    public const TYPE_FINAL = 'final';

    public const ALLOWED_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    protected $fillable = [
        'company_id',
        'company_period_id',
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
        return optional($next)->id;
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
