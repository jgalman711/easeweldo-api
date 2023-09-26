<?php

namespace App\Rules;

use App\Models\Earning;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EarningTypeJsonRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) && !empty($value)) {
            foreach ($value as $item) {
                if (!isset($item['name']) || !$item['name']) {
                    $fail('The :attribute name is required.');
                }
                if (!isset($item['pay']) || !$item['pay']) {
                    $fail('The :attribute pay is required.');
                }
                if (!isset($item['pay']) || $item['pay'] < 0) {
                    $fail('The :attribute pay must be greater than zero.');
                }
            }
        }
    }
}
