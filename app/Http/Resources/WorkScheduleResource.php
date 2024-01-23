<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class WorkScheduleResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "monday_clock_in_time" => $this->monday_clock_in_time,
            "monday_clock_out_time" => $this->monday_clock_out_time,
            "tuesday_clock_in_time" => $this->tuesday_clock_in_time,
            "tuesday_clock_out_time" => $this->tuesday_clock_out_time,
            "wednesday_clock_in_time" => $this->wednesday_clock_in_time,
            "wednesday_clock_out_time" => $this->wednesday_clock_out_time,
            "thursday_clock_in_time" => $this->thursday_clock_in_time,
            "thursday_clock_out_time" => $this->thursday_clock_out_time,
            "friday_clock_in_time" => $this->friday_clock_in_time,
            "friday_clock_out_time" => $this->friday_clock_out_time,
            "saturday_clock_in_time" => $this->saturday_clock_in_time,
            "saturday_clock_out_time" => $this->saturday_clock_out_time,
            "sunday_clock_in_time" => $this->sunday_clock_in_time,
            "sunday_clock_out_time" => $this->sunday_clock_out_time
        ];
    }
}
