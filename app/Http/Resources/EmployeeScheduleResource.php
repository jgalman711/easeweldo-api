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
            ...parent::toArray($request),
            'monday_clock_in_time' => self::format($this->monday_clock_in_time),
            'monday_clock_out_time' => self::format($this->monday_clock_out_time),
            'tuesday_clock_in_time' => self::format($this->tuesday_clock_in_time),
            'tuesday_clock_out_time' => self::format($this->tuesday_clock_out_time),
            'wednesday_clock_in_time' => self::format($this->wednesday_clock_in_time),
            'wednesday_clock_out_time' => self::format($this->wednesday_clock_out_time),
            'thursday_clock_in_time' => self::format($this->thursday_clock_in_time),
            'thursday_clock_out_time' => self::format($this->thursday_clock_out_time),
            'friday_clock_in_time' => self::format($this->friday_clock_in_time),
            'friday_clock_out_time' => self::format($this->friday_clock_out_time),
            'saturday_clock_in_time' => self::format($this->saturday_clock_in_time),
            'saturday_clock_out_time' => self::format($this->saturday_clock_out_time),
            'sunday_clock_in_time' => self::format($this->sunday_clock_in_time),
            'sunday_clock_out_time' => self::format($this->sunday_clock_out_time)
        ];
    }

    private static function format(string $time = null): ?string
    {
        return $time ? substr($time, 0, 5) : $time;
    }
}
