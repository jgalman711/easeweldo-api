<?php

namespace Database\Seeders;

use App\Models\PhilHealth;
use Illuminate\Database\Seeder;

class PhilHealthSeeder extends Seeder
{
    public $philHealth = [
        'min_contribution' => 400,
        'max_contribution' => 3200,
        'contribution_rate' => 0.04,
        'status' => PhilHealth::ACTIVE
    ];

    public function run(): void
    {
        PhilHealth::create($this->philHealth);
    }
}
