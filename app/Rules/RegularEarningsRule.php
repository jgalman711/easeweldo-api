<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RegularEarningsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail('The :attribute must be an array.');
        }
        
        // Check if the array has valid keys and values
        foreach ($value as $type => $earnings) {
            // Each earnings type should be either an array or null
            if (!is_array($earnings) && !is_null($earnings)) {
                $fail('The :attribute must be an array.');
            }
            
            // If the earnings type is an array, validate its structure
            if (is_array($earnings)) {
                foreach ($earnings as $earning) {
                    if (!isset($item['date']) || !$item['date']) {
                        $fail('The :attribute date is required.');
                    }
                    if (!isset($item['rate']) || !$item['rate']) {
                        $fail('The :attribute rate is required.');
                    }
                    if (!isset($item['hours']) || !$item['hours']) {
                        $fail('The :attribute hours is required.');
                    }
                }
            }
        }
    }
}
