<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryComputationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'salary computation',
            'id' => $this->id,
            'attributes' => [
                'basicSalary' => $this->basic_salary,
                'hourlyRate' => $this->hourly_rate,
                'dailyRate' => $this->daily_rate,
                'nonTaxableEarnings' => $this->non_taxable_earnings,
                'taxableEarnings' => $this->taxable_earnings,
                'otherDeductions' => $this->other_deductions,
                'workingHoursPerDay' => $this->working_hours_per_day,
                'breakHoursPerDay' => $this->break_hours_per_day,
                'workingDaysPerWeek' => $this->working_days_per_week,
                'overtimeRate' => $this->overtime_rate,
                'nightDiffRate' => $this->night_diff_rate,
                'regularHolidayRate' => $this->regular_holiday_rate,
                'specialHolidayRate' => $this->special_holiday_rate,
                'totalSickLeaveHours' => $this->total_sick_leave_hours,
                'totalVacationLeaveHours' => $this->total_vacation_leave_hours,
                'availableSickLeaveHours' => $this->available_sick_leave_hours,
                'availableVacationLeaveHours' => $this->available_vacation_leave_hours
            ],
            'relationships' => [
                'employee' => [
                    'data' => [
                        'type' => 'employee',
                        'id' => $this->employee_id,
                    ],
                    'links' => [
                        ['self' => route('employees.show', [$this->employee->company->slug, $this->employee_id])]
                    ]
                ]
            ],
            'links' => [
                ['self' => route('employees.salary-computations.index', [$this->employee->company->slug, $this->employee_id])]
            ]
        ];
    }
}
