<?php

namespace Database\Seeders;

use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;

class WorkScheduleSeeder extends Seeder
{
    public function run(): void
    {
        WorkSchedule::create([
            'name' => 'Default Work Schedule',
            'company_id' => 1,
            'monday_clock_in_time' => '08:00',
            'monday_clock_out_time' => '17:00',
            'tuesday_clock_in_time' => '08:00',
            'tuesday_clock_out_time' => '17:00',
            'wednesday_clock_in_time' => '08:00',
            'wednesday_clock_out_time' => '17:00',
            'thursday_clock_in_time' => '08:00',
            'thursday_clock_out_time' => '17:00',
            'friday_clock_in_time' => '08:00',
            'friday_clock_out_time' => '17:00',
            'saturday_clock_in_time' => null,
            'saturday_clock_out_time' => null,
            'sunday_clock_in_time' => null,
            'sunday_clock_out_time' => null,
        ]);
    }
}
