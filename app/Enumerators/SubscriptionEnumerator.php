<?php

namespace App\Enumerators;

class SubscriptionEnumerator
{
    public const NAMES = [
        self::CORE,
        self::CORE_TIME,
        self::CORE_TIME_DISBURSE
    ];

    public const CORE = 'core';
    public const CORE_TIME = 'core-plus-time-and-attendance';
    public const CORE_TIME_DISBURSE = 'core-plus-time-and-attendance-plus-auto-disburse';

    public const TYPES = [
        self::TYPE_CORE,
        self::TYPE_BUNDLE
    ];

    public const TYPE_CORE = 'core';
    public const TYPE_BUNDLE = 'bundle';

    public const STATUSES = [
        self::PAID_STATUS,
        self::UNPAID_STATUS
    ];

    public const PAID_STATUS = 'paid';
    public const UNPAID_STATUS = 'unpaid';

    public const MONTHS = [
        self::MONTHS_1,
        self::MONTHS_12,
        self::MONTHS_24,
        self::MONTHS_36
    ];

    public const MONTHS_36 = 36;
    public const MONTHS_24 = 24;
    public const MONTHS_12 = 12;
    public const MONTHS_1 = 1;
}