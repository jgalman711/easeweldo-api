<?php

namespace App\Rules;

use App\Models\Leave;
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
                if (! isset($item['type']) || ! $item['type'] || ! in_array($item['type'], Leave::TYPES)) {
                    $fail('The :attribute type must be in '.implode(', ', Leave::TYPES).'.');
                }
                if (! isset($item['hours']) || ! $item['hours']) {
                    $fail('The :attribute hours is required.');
                }
            }
        }
    }
}
