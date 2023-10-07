<?php

namespace App\Enumerators;

class PayrollEnumerator
{
    public const STATUSES = [
        self::STATUS_TO_PAY,
        self::STATUS_PAID,
        self::STATUS_CANCELED
    ];

    public const STATUS_TO_PAY = 'to-pay';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';

    public const TYPE = [
        self::TYPE_REGULAR,
        self::TYPE_SPECIAL,
        self::TYPE_NTH_MONTH_PAY,
        self::TYPE_FINAL
    ];
    public const TYPE_REGULAR = 'regular';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_NTH_MONTH_PAY = 'nth_month_pay';
    public const TYPE_FINAL = 'final';
}
