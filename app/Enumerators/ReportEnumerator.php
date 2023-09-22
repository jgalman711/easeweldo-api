<?php

namespace App\Enumerators;

class ReportEnumerator
{
    public const TYPE = [
        self::ANNUAL_EXPENSES,
        self::MONTHLY_EXPENSES,
        self::EMPLOYEE_PAYROLL,
        self::ATTENDANCE
    ];

    public const ANNUAL_EXPENSES = 'annual-expenses';
    public const MONTHLY_EXPENSES = 'monthly-expenses';
    public const EMPLOYEE_PAYROLL = 'employee-payroll';
    public const ATTENDANCE = 'attendance';
}