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
}
