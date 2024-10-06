<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class AgeValidationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Parse the input date using Carbon
        $birth_date = Carbon::parse($value);
        $currentDate = Carbon::now();

        // Calculate the age difference
        $age = $birth_date->diffInYears($currentDate);

        // Validate if age is greater than or equal to 18
        if ($age < 18) {
            $fail('The :attribute must make the user at least 18 years old.');
        }
    }

    public function message(): string
    {
        return 'The :attribute must make the user at least 18 years old.';
    }
}
