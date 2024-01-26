<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TimerecordResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "company_id" => $this->company_id,
            "employee_id" => $this->employee_id,
            "clock_in" => $this->clock_in,
            "clock_out" => $this->clock_out,
            "expected_clock_in" => $this->expected_clock_in,
            "expected_clock_out" => $this->expected_clock_out,
            "original_clock_in" => $this->original_clock_in,
            "original_clock_out" => $this->original_clock_out,
            "source" => $this->source,
            "remarks" => $this->remarks,
            "attendance_status" => $this->attendance_status,
            "next_action" => $this->next_action
        ];
    }
}
