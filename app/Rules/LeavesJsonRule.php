<?php

namespace App\Rules;

use App\Enumerators\LeaveEnumerator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LeavesJsonRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value) && ! empty($value)) {
            foreach ($value as $item) {
                if (! isset($item['type']) || ! $item['type']) {
                    $fail('The :attribute type is required');
                }
                if (! isset($item['type']) || ! $item['type'] || ! in_array($item['type'], LeaveEnumerator::TYPES)) {
                    $fail('The :attribute type must be in '.implode(', ', LeaveEnumerator::TYPES).'.');
                }
                if (! isset($item['hours']) || ! $item['hours']) {
                    $fail('The :attribute hours is required.');
                }
            }
        }
    }
}
