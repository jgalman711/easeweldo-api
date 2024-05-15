<?php

namespace App\Helpers;

class NumberHelper
{
    public static function format($number)
    {
        return number_format(round($number, 2), 2);
    }
}
