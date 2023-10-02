<?php

namespace App\Http\Resources;

use App\Models\TimeRecord;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardResource extends BaseResource
{
    protected $expectedClockIn;

    protected $expectedClockOut;

    public function toArray(Request $request): array
    {
        $timeRecord = $this['timeRecord'];
        $workSchedule = $this['workSchedule'];
        return [
            'schedule' => $this->parseWorkSchedule($workSchedule),
            'work_today' =>$this->parseWorkToday($timeRecord),
        ];
    }

    private function parseWorkToday(?TimeRecord $timeRecord): array
    {
        $formattedExpectedIn = $this->expectedClockIn
            ? Carbon::parse($this->expectedClockIn)->format('h:i A')
            : null;
        $formattedExpectedOut = $this->expectedClockOut
            ? Carbon::parse($this->expectedClockOut)->format('h:i A')
            : null;

        $formattedClockIn = optional($timeRecord)->clock_in
            ? Carbon::parse($timeRecord->clock_in)->format('h:i A')
            : null;

        $formattedClockOut = optional($timeRecord)->clock_out
            ? Carbon::parse($timeRecord->clock_out)->format('h:i A')
            : null;

        return [
            'day' => Carbon::now()->format('l'),
            'date' => Carbon::now()->format('d'),
            'clock_in' => $formattedClockIn,
            'clock_out' => $formattedClockOut,
            'expected_clock_in' => $formattedExpectedIn,
            'expected_clock_out' => $formattedExpectedOut,
            'attendance_status' => optional($timeRecord)->attendance_status,
            'next_action' => optional($timeRecord)->next_action ?? "Clock In"
        ];
    }

    private function parseWorkSchedule(WorkSchedule $workSchedule): array
    {
        $result = [];
        $daysOfWeek = [
            'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
        ];
        $currentDay = date('l');
        foreach ($daysOfWeek as $day) {
            $isToday = false;
            $clockInKey = "{$day}_clock_in_time";
            $clockOutKey = "{$day}_clock_out_time";
            $clockIn = $workSchedule->$clockInKey;
            $clockOut = $workSchedule->$clockOutKey;

            if (strtolower($day) === strtolower($currentDay)) {
                $isToday = true;
                $this->expectedClockIn = $clockIn;
                $this->expectedClockOut = $clockOut;
            }
            $result[] = [
                'day' => strtoupper(substr($day, 0, 3)),
                'clock_in' => $clockIn ? Carbon::createFromFormat('H:i:s', $clockIn)->format('H:i') : null,
                'clock_out' => $clockOut ? Carbon::createFromFormat('H:i:s', $clockOut)->format('H:i') : null,
                'is_today' => $isToday,
            ];
        }
        return $result;
    }
}
