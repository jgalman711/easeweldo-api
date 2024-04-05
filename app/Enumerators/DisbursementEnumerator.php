<?php

namespace App\Enumerators;

class DisbursementEnumerator
{
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
        self::STATUS_CANCELLED,
    ];

    public const TYPES = [
        self::TYPE_REGULAR,
        self::TYPE_SPECIAL,
        self::TYPE_NTH_MONTH_PAY,
        self::TYPE_FINAL,
    ];

    public const SPECIAL_TYPES = [
        self::TYPE_SPECIAL,
        self::TYPE_NTH_MONTH_PAY,
        self::TYPE_FINAL,
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
}
