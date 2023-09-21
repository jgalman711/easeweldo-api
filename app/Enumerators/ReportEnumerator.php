<?php

namespace App\Enumerators;

class ReportEnumerator
{
    public const TYPE = [
        self::ANNUAL_EXPENSES,
        self::MONTHLY_EXPENSES
    ];

    public const ANNUAL_EXPENSES = 'annual-expenses';
    public const MONTHLY_EXPENSES = 'monthly-expenses';
}