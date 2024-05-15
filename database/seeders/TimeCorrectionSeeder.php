<?php

namespace Database\Seeders;

use App\Models\TimeCorrection;
use Illuminate\Database\Seeder;

class TimeCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TimeCorrection::factory()->count(150)->create();
    }
}
