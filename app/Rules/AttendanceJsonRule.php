<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AttendanceJsonRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attendanceType = ['absent', 'late', 'undertime', 'overtime'];
        if (isset($value) && ! empty($value)) {
            foreach ($value as $item) {
                if (! isset($item['type']) || ! $item['type']) {
                    $fail('The :attribute type is required');
                }
                if (! isset($item['type']) || ! $item['type'] || ! in_array($item['type'], $attendanceType)) {
                    $fail('The :attribute type must be in '.implode(', ', $attendanceType).'.');
                }
                if (! isset($item['hours']) || ! $item['hours']) {
                    $fail('The :attribute hours is required.');
                }
            }
        }
    }
}
