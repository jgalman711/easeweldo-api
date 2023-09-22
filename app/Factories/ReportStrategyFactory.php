<?php

namespace App\Factories;

use App\Enumerators\ReportEnumerator;
use App\Strategies\Report\AnnualExpenseReportStrategy;
use App\Strategies\Report\AttendanceReportStrategy;
use App\Strategies\Report\EmployeePayrollReportStrategy;
use App\Strategies\Report\MonthlyExpenseReportStrategy;
use Exception;

class ReportStrategyFactory
{
    public static function createStrategy(string $type)
    {
        if ($type == ReportEnumerator::ANNUAL_EXPENSES) {
            $strategy = new AnnualExpenseReportStrategy;
        } elseif ($type == ReportEnumerator::MONTHLY_EXPENSES) {
            $strategy = new MonthlyExpenseReportStrategy;
        } elseif ($type == ReportEnumerator::EMPLOYEE_PAYROLL) {
            $strategy = new EmployeePayrollReportStrategy;
        } elseif ($type == ReportEnumerator::ATTENDANCE) {
            $strategy = new AttendanceReportStrategy;
        } else {
            throw new Exception("Invalid report type");
        }
        return $strategy;
    }
}
