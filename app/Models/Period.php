<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Period extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ATTENTION_REQUIRED = 'attention_required';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';

    public const STATUSES = [
        self::STATUS_ATTENTION_REQUIRED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_PENDING,
        self::STATUS_PROCESSING
    ];

    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_SEMI_MONTHLY = 'semi-monthly';
    public const TYPE_WEEKLY = 'weekly';

    public const TYPES = [
        self::TYPE_MONTHLY,
        self::TYPE_SEMI_MONTHLY,
        self::TYPE_WEEKLY
    ];

    protected $fillable = [
        'name',
        'company_id',
        'company_period_number',
        'type',
        'start_date',
        'end_date',
        'salary_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'salary_date' => 'datetime',
        'status' => 'string'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}