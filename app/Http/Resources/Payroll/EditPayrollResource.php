<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class EditPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'period_id' => $this->period_id,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'pay_date' => $this->pay_date,
            'basic_salary' => $this->basic_salary,
            'id' => $this->id,
            'id' => $this->id,
            'regularEarnings' => self::getRegularEarnings()
        ];
    }

    public function getRegularEarnings(): array
    {
        $regularEarnings = [];
        $regularEarnings['overtime'] = $this->attendance_earnings['overtime'] ?? null;
        $regularEarnings['regularHoliday'] = $this->holidays['regular'] ?? null;
        $regularEarnings['regularHolidayWorked'] = $this->holidays['regularWorked'] ?? null;
        $regularEarnings['specialHoliday'] = $this->holidays['special'] ?? null;
        $regularEarnings['specialHolidayWorked'] = $this->holidays['specialWorked'] ?? null;
        $regularEarnings['sickLeave'] = $this->leaves['sick'] ?? null;
        $regularEarnings['vacationLeave'] = $this->leaves['vacation'] ?? null;
        return $regularEarnings;
    }
}
