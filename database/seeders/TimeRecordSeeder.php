<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeRecordSeeder extends Seeder
{
    public function run(): void
    {
        $timeRecords = [
            ['employee_id' => 8, 'clock_in' => '2023-04-10 07:58:00', 'clock_out' => '2023-04-10 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-11 08:06:00', 'clock_out' => '2023-04-11 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-12 07:58:00', 'clock_out' => '2023-04-12 16:07:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-13 07:58:00', 'clock_out' => '2023-04-13 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-14 07:58:00', 'clock_out' => '2023-04-14 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-17 07:58:00', 'clock_out' => '2023-04-17 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-18 07:58:00', 'clock_out' => '2023-04-18 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-19 07:58:00', 'clock_out' => '2023-04-19 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-20 07:58:00', 'clock_out' => '2023-04-20 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-21 07:58:00', 'clock_out' => '2023-04-21 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-24 07:58:00', 'clock_out' => '2023-04-24 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-25 07:58:00', 'clock_out' => '2023-04-25 17:05:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-27 08:01:00', 'clock_out' => '2023-04-27 15:35:00'],
            ['employee_id' => 8, 'clock_in' => '2023-04-28 07:58:00', 'clock_out' => '2023-04-28 17:05:00']
        ];
        DB::table('time_records')->insert($timeRecords);
    }
}
