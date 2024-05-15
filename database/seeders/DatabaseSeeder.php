<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TaxSeeder::class,
            SSSSeeder::class,
            PagibigSeeder::class,
            PhilHealthSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            SalaryComputationSeeder::class,
            LeaveSeeder::class
        ]);
    }
}
