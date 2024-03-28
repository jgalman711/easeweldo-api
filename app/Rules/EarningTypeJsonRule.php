<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EarningTypeJsonRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) && ! empty($value)) {
            foreach ($value as $item) {
                if (! isset($item['name']) || ! $item['name']) {
                    $fail('The :attribute name is required.');
                }
                if (! isset($item['amount']) || ! $item['amount']) {
                    $fail('The :attribute amount is required.');
                }
                if (! isset($item['amount']) || $item['amount'] < 0) {
                    $fail('The :attribute amount must be greater than zero.');
                }
            }
        }
    }
}
