<?php

namespace App\Factories;

use App\Strategies\Qr\CompanyQrStrategy;
use App\Strategies\Qr\EmployeeQrStrategy;

class QrStrategyFactory
{
    public static function createStrategy(string $type)
    {
        if ($type == 'employee') {
            $strategy = new EmployeeQrStrategy;
        } elseif ($type == 'company') {
            $strategy = new CompanyQrStrategy;
        }

        return $strategy;
    }
}
