<?php

namespace App\Traits;

trait PayrollJsonParser
{
    public function totalAmountParser(array $elements): float
    {
        $totalAmount = 0;
        foreach ($elements as $element) {
            $totalAmount += $element['amount'];
        }
        return round($totalAmount, 2);
    }
}
