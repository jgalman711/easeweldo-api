<?php

namespace App\Traits;

trait PayrollJsonParser
{
    public function totalAmountParser($elements): float
    {
        $totalAmount = 0;
        if ($elements) {
            foreach ($elements as $element) {
                if (is_array($element) && ! isset($element['amount'])) {
                    $totalAmount += $this->totalAmountParser($element);
                } else {
                    // added key pay just in case
                    $totalAmount += $element['amount'] ?? $element['pay'] ?? 0;
                }
            }
        }

        return round($totalAmount, 2);
    }
}
