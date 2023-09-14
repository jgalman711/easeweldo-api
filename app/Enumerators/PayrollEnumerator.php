<?php

namespace App\Enumerators;

class PayrollEnumerator
{
    public const STATUSES = [
        self::STATUS_TO_PAY,
        self::STATUS_PAID
    ];
    public const STATUS_TO_PAY = 'to_pay';
    public const STATUS_PAID = 'paid';

    public const TYPE = [
        self::TYPE_REGULAR,
        self::TYPE_SPECIAL
    ];
    public const TYPE_REGULAR = 'regular';
    public const TYPE_SPECIAL = 'special';
}
