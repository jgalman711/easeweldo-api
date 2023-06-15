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
        $data = parent::toArray($request);
        $data['monday_clock_in_time'] = self::format($this->monday_clock_in_time);
        $data['monday_clock_out_time'] = self::format($this->monday_clock_out_time);
        $data['tuesday_clock_in_time'] = self::format($this->tuesday_clock_in_time);
        $data['tuesday_clock_out_time'] = self::format($this->tuesday_clock_out_time);
        $data['wednesday_clock_in_time'] = self::format($this->wednesday_clock_in_time);
        $data['wednesday_clock_out_time'] = self::format($this->wednesday_clock_out_time);
        $data['thursday_clock_in_time'] = self::format($this->thursday_clock_in_time);
        $data['thursday_clock_out_time'] = self::format($this->thursday_clock_out_time);
        $data['friday_clock_in_time'] = self::format($this->friday_clock_in_time);
        $data['friday_clock_out_time'] = self::format($this->friday_clock_out_time);
        $data['saturday_clock_in_time'] = self::format($this->saturday_clock_in_time);
        $data['saturday_clock_out_time'] = self::format($this->saturday_clock_out_time);
        $data['sunday_clock_in_time'] = self::format($this->sunday_clock_in_time);
        $data['sunday_clock_out_time'] = self::format($this->sunday_clock_out_time);
        return $data;
    }

    private static function format(string $time = null): ?string
    {
        return $time ? substr($time, 0, 5) : $time;
    }
}
