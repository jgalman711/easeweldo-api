<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DeductionJsonRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) && !empty($value)) {
            foreach ($value as $item) {
                if (!isset($item['hours']) || !$item['hours']) {
                    $fail('The :attribute hours is required.');
                }
                if (!isset($item['rate']) || !$item['rate']) {
                    $fail('The :attribute rate is required.');
                }
                if (!isset($item['date']) || !$item['date']) {
                    $fail('The :attribute date is required.');
                }
            }
        } elseif (!is_null($value)) {
            $fail('The :attribute field is required.');
        }
    }
}
