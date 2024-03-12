<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EarningsValidationRule implements ValidationRule
{
    private const ERROR_MESSAGE = 'This field is required.';

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) && !empty($value)) {
            foreach ($value as $item) {
                self::validateEarningName($fail, $item);
                self::validateEarningPay($fail, $item);
            }
        }
    }

    public function validateEarningName(Closure $fail, array $item): void
    {
        if ((isset($item['amount']) && $item['amount'])
            && (!isset($item['name']) || !$item['name'])
        ) {
            $fail(self::ERROR_MESSAGE);
        }
    }

    public function validateEarningPay(Closure $fail, array $item): void
    {
        if (isset($item['name']) && $item['name']) {
            if (!isset($item['amount']) || !$item['amount']) {
                $fail(self::ERROR_MESSAGE);
            } elseif ($item['amount'] < 0) {
                $fail('Pay must be greater than zero.');
            }
        }
    }
}
