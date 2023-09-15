<?php

namespace App\Factories;

use App\Enumerators\PayrollEnumerator;
use App\Strategies\Payroll\FinalPayrollStrategy;
use App\Strategies\Payroll\NthMonthPayrollStrategy;
use App\Strategies\Payroll\RegularPayrollStrategy;
use App\Strategies\Payroll\SpecialPayrollStrategy;
use Exception;

class PayrollStrategyFactory
{

    public static function createStrategy(string $type)
    {
        if ($type == PayrollEnumerator::TYPE_NTH_MONTH_PAY) {
            $strategy = new NthMonthPayrollStrategy();
        } elseif ($type == PayrollEnumerator::TYPE_SPECIAL) {
            $strategy = new SpecialPayrollStrategy();
        } elseif ($type == PayrollEnumerator::TYPE_REGULAR) {
            $strategy = new RegularPayrollStrategy();
        } elseif ($type == PayrollEnumerator::TYPE_FINAL) {
            $strategy = new FinalPayrollStrategy();
        }else {
            throw new Exception("Invalid payroll type");
        }
        return $strategy;
    }
}
