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
                if (!isset($item['type']) || !$item['type'] || !in_array($item['type'], Earning::TYPES)) {
                    $fail('The :attribute type must be in ' . implode(', ', Earning::TYPES) . '.');
                }
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
