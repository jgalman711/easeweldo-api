<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;

class TimerecordResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "company_id" => $this->company_id,
            "employee_id" => $this->employee_id,
            "date" => $this->date(),
            "clock_in" => $this->timeFormat($this->clock_in),
            "clock_out" => $this->timeFormat($this->clock_out),
            "expected_clock_in" => $this->timeFormat($this->expected_clock_in),
            "expected_clock_out" => $this->timeFormat($this->expected_clock_out),
            "original_clock_in" => $this->timeFormat($this->original_clock_in),
            "original_clock_out" => $this->timeFormat($this->original_clock_out),
            "source" => $this->source,
            "remarks" => $this->remarks,
            "attendance_status" => $this->attendance_status,
            "next_action" => $this->next_action
        ];
    }

    private function timeFormat(?string $dateTime = null): ?string
    {
        if ($dateTime) {
            $datetime = new DateTime($dateTime);
            return $datetime->format('H:i:s');
        }
        return $dateTime;
    }

    private function date(): ?string
    {
        if ($this->clock_in) {
            $datetimeString = $this->clock_in;
        } elseif ($this->expected_clock_in) {
            $datetimeString = $this->expected_clock_in;
        } else {
            return null;
        }
        $datetime = new DateTime($datetimeString);
        return $datetime->format('Y-m-d');
    }
}
