<?php

namespace App\Http\Resources\Payroll;

class EditPayrollResource
{
    public function toArray(): array
    {
        return [
            'regularEarnings' => self::getRegularEarnings()
        ];
    }

    public function getRegularEarnings(): array
    {
        return [
            [
                'name' => 'Overtime',
                'hours' => 'hours',
                'rate' => 'rate'
            ], [
                'name' => 'Regular Holiday',
                'hours' => 'hours',
                'rate' => 'rate'
            ]
        ];
    }
}
