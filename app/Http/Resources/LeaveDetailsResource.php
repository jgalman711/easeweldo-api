<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LeaveDetailsResource extends BaseResource
{
    private $hoursPerDay;
    public function toArray(Request $request): array
    {
        $salaryData = $this->salaryComputation;
        $this->hoursPerDay = $salaryData->working_hours_per_day;
        return [
            'total_leaves' => $this->hoursToDays($salaryData->total_sick_leave_hours + $salaryData->total_vacation_leave_hours),
            'remaining_leaves' => $this->hoursToDays($salaryData->available_sick_leave_hours + $salaryData->available_vacation_leave_hours),
            'total_sick_leaves' => $this->hoursToDays($salaryData->total_sick_leave_hours),
            'total_vacation_leaves' => $this->hoursToDays($salaryData->total_vacation_leave_hours),
            'remaining_sick_leaves' => $this->hoursToDays($salaryData->available_sick_leave_hours),
            'remaining_vacation_leaves' => $this->hoursToDays($salaryData->available_vacation_leave_hours),
        ];
    }

    private function hoursToDays(int $hours): float
    {
        return round($hours / $this->hoursPerDay, 2);
    }
}
