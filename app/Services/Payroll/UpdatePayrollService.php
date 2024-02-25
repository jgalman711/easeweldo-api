<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\Payroll\UpdatePayrollRequest as PayrollRequest;
use App\Models\Holiday;
use App\Models\Payroll;
use Illuminate\Validation\ValidationException;

class UpdatePayrollService extends RegeneratePayrollService
{
    public function update(Payroll $payroll, PayrollRequest $request): Payroll
    {
        $this->init($payroll);
        $errors = [];
        $input = $request->validated();
        $this->attendanceValidation($request, $input, $errors);
        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $formattedHolidayType = "{$type}_holidays";
            if ($request->has($formattedHolidayType)) {
                $this->validateHolidays($request->{$formattedHolidayType}, $input, $errors, $formattedHolidayType);
            }
            $formattedHolidayType = "{$type}_holidays_worked";
            if ($request->has($formattedHolidayType)) {
                $this->validateHolidays($request->{$formattedHolidayType}, $input, $errors, $formattedHolidayType);
            }
        }
        if (empty($errors)) {
            $payroll->update($input);
            return $payroll;
        }
        throw ValidationException::withMessages($errors);
    }

    protected function attendanceValidation(PayrollRequest $request, array &$input, array &$errors): void
    {
        $dates = [];
        foreach (PayrollEnumerator::ATTENDANCE_EARNINGS_TYPES as $payrollType) {
            $typePlural = $payrollType . "s";
            if ($request->has($typePlural)) {
                foreach ($request->{$typePlural} as $entry) {
                    $date = $entry['date'];
                    if (in_array($date, $dates[$payrollType] ?? [])) {
                        $errors['errors'][] = "The $typePlural date should not overlap with $date.";
                    }

                    $amount = $entry['hours'] * $entry['rate'] * $this->salaryComputation->hourly_rate;
                    $attendanceEarnings[] = [
                        'date' => $entry['date'],
                        'type' => $payrollType,
                        'hours' => $entry['hours'],
                        'rate' => $entry['rate'],
                        'amount' => $payrollType == PayrollEnumerator::OVERTIME ? $amount : $amount * -1
                    ];
                    $dates[$payrollType][] = $date;
                }
            }
        }
        $input['attendance_earnings'] = $attendanceEarnings;
    }

    protected function validateHolidays(array $holidayRequest, array &$input, array &$errors, string $holidayType): void
    {
        $dates = [];
        foreach ($holidayRequest as $entry) {
            $date = $entry['date'];
            $errorMessage = str_replace("_", " ", "The $holidayType date should not overlap with $date.");
            if (in_array($date, $dates ?? []) && !in_array($errorMessage, $errors)) {
                $errors['errors'][] = $errorMessage;
            }
            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }
            $amount = $entry['hours'] * $entry['rate'] * $this->salaryComputation->hourly_rate;
            $holidays[] = [
                'type' => $holidayType,
                'date' => $entry['date'],
                'hours' => $entry['hours'],
                'rate' => $entry['rate'],
                'amount' => $amount
            ];
        }
        $input[$holidayType] = $holidays;
    }
}
