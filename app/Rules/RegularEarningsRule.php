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
        if (! is_array($value)) {
            $fail('The :attribute must be an array.');
        }

        foreach ($value as $type => $earnings) {
            if (! is_array($earnings) && ! is_null($earnings)) {
                $fail('The :attribute must be an array.');
            }

            if (is_array($earnings)) {
                foreach ($earnings as $earning) {
                    if (! isset($item['date']) || ! $item['date']) {
                        $fail('The :attribute date is required.');
                    }
                    if (! isset($item['rate']) || ! $item['rate']) {
                        $fail('The :attribute rate is required.');
                    }
                    if (! isset($item['hours']) || ! $item['hours']) {
                        $fail('The :attribute hours is required.');
                    }
                }
            }
        }
    }
}
