<?php

namespace Database\Seeders;

use App\Models\SalaryComputation;
use Illuminate\Database\Seeder;

class SalaryComputationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalaryComputation::factory()->count(100)->create();
    }
}
