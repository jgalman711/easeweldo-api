<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EmployeeScheduleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'work_schedule_id' => optional($this->workSchedule)->id,
            'name' => optional($this->workSchedule)->name,
            'status' => $this->status,
            'is_clock_required' => $this->is_clock_required,
            'monday_clock_in_time' => self::format(optional($this->workSchedule)->monday_clock_in_time),
            'monday_clock_out_time' => self::format(optional($this->workSchedule)->monday_clock_out_time),
            'tuesday_clock_in_time' => self::format(optional($this->workSchedule)->tuesday_clock_in_time),
            'tuesday_clock_out_time' => self::format(optional($this->workSchedule)->tuesday_clock_out_time),
            'wednesday_clock_in_time' => self::format(optional($this->workSchedule)->wednesday_clock_in_time),
            'wednesday_clock_out_time' => self::format(optional($this->workSchedule)->wednesday_clock_out_time),
            'thursday_clock_in_time' => self::format(optional($this->workSchedule)->thursday_clock_in_time),
            'thursday_clock_out_time' => self::format(optional($this->workSchedule)->thursday_clock_out_time),
            'friday_clock_in_time' => self::format(optional($this->workSchedule)->friday_clock_in_time),
            'friday_clock_out_time' => self::format(optional($this->workSchedule)->friday_clock_out_time),
            'saturday_clock_in_time' => self::format(optional($this->workSchedule)->saturday_clock_in_time),
            'saturday_clock_out_time' => self::format(optional($this->workSchedule)->saturday_clock_out_time),
            'sunday_clock_in_time' => self::format(optional($this->workSchedule)->sunday_clock_in_time),
            'sunday_clock_out_time' => self::format(optional($this->workSchedule)->sunday_clock_out_time),
            'remarks' => $this->remarks,
        ];
    }

    private static function format(string $time = null): ?string
    {
        return $time ? substr($time, 0, 5) : $time;
    }
}
