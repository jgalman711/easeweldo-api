<?php

namespace App\Models;

use App\Enumerators\SubscriptionEnumerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_PENDING
    ];

    protected $appends = [
        'has_time_and_attendance_subscription',
        'has_core_subscription',
        'subscription_amount',
        'subscription_amount_paid',
        'subscription_balance',
        'subscription_status'
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_PENDING = 'pending';

    public const STATUS_TRIAL = 'trial';

    public const ABSOLUTE_STORAGE_PATH = 'public/companies/images';

    public const STORAGE_PATH = 'companies/images/';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'details',
        'logo',
        'legal_name',
        'address_line',
        'barangay_town_city_province',
        'contact_name',
        'email_address',
        'mobile_number',
        'landline_number',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'tin',
        'sss_number',
        'philhealth_number',
        'pagibig_number'
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function biometrics(): HasMany
    {
        return $this->hasMany(Biometrics::class);
    }

    public function employees(): HasMany
    {
        return $this->HasMany(Employee::class);
    }

    public function earnings(): HasOne
    {
        return $this->hasOne(Earning::class);
    }

    public function payrolls(): HasManyThrough
    {
        return $this->hasManyThrough(Payroll::class, Employee::class);
    }

    public function period(int $periodId): Period
    {
        return $this->periods()->where('company_period_id', $periodId)->first();
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users', 'company_id', 'user_id');
    }

    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function companySubscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function subscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Subscription::class,
            CompanySubscription::class,
            'company_id',
            'id',
            'id',
            'subscription_id'
        );
    }

    public function getEmployeeById(int $employeeId): ?Employee
    {
        $employee = $this->employees()->with('user')->where('id', $employeeId)->first();
        if (!$employee) {
            throw new \Exception('Employee not found');
        }
        return $employee;
    }

    public function getPeriodById(int $periodId): ?Period
    {
        $period =  $this->periods->where('id', $periodId)->first();
        if (!$period) {
            throw new \Exception('Period not found');
        }
        return $period;
    }

    public function currentPeriod(): ?Period
    {
        $now = Carbon::now();
        return $this->periods()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->first();
    }

    public function isInSettlementPeriod(): bool
    {
        $now = Carbon::now();
        return $this->periods()
            ->where('end_date', '<=', $now)
            ->where('salary_date', '>=', $now)
            ->exists();
    }

    public function getWorkScheduleById(int $workScheduleId): ?WorkSchedule
    {
        $workSchedule = $this->workSchedules->where('id', $workScheduleId)->first();
        if (!$workSchedule) {
            throw new \Exception('Work schedule not found');
        }
        return $workSchedule;
    }

    public function getHasTimeAndAttendanceSubscriptionAttribute()
    {
        $subscription = Subscription::where('name', SubscriptionEnumerator::CORE_TIME)
            ->orWhere('name', SubscriptionEnumerator::CORE_TIME_DISBURSE)
            ->first();

        foreach ($this->companySubscriptions as $companySubscription) {
            if ($companySubscription->subscription_id == $subscription->id
                && $companySubscription->status == SubscriptionEnumerator::PAID_STATUS
                && Carbon::now()->lte($subscription->end_date)
            ) {
                return true;
            }
        }
        return false;
    }

    public function getHasCoreSubscriptionAttribute()
    {
        $subscriptions = Subscription::whereIn('name', SubscriptionEnumerator::NAMES)->pluck('id');
        foreach ($this->companySubscriptions as $companySubscription) {
            if (in_array($companySubscription->subscription_id, $subscriptions->toArray())
                && $companySubscription->status == SubscriptionEnumerator::PAID_STATUS
                && Carbon::now()->lte($companySubscription->end_date)
            ) {
                return true;
            }
        }
        return false;
    }

    public function getSubscriptionBalanceAttribute(): float
    {
        return $this->companySubscriptions()->sum('balance');
    }

    public function getSubscriptionAmountPaidAttribute(): float
    {
        return $this->companySubscriptions()->sum('amount_paid');
    }

    public function getSubscriptionAmountAttribute(): float
    {
        return $this->companySubscriptions()->sum('amount');
    }

    public function getSubscriptionStatusAttribute(): string
    {
        $unpaidSubscriptionsCount = $this->companySubscriptions()
            ->where('status', SubscriptionEnumerator::UNPAID_STATUS)
            ->count();
        return $unpaidSubscriptionsCount > 0
            ? SubscriptionEnumerator::UNPAID_STATUS
            : SubscriptionEnumerator::PAID_STATUS;
    }
}
