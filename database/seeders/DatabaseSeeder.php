<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            TaxSeeder::class,
            SSSSeeder::class,
            PagibigSeeder::class,
            PhilHealthSeeder::class,
            SubscriptionSeeder::class,
            CompanySeeder::class,
            WorkScheduleSeeder::class,
            UserSeeder::class
        ]);
    }
}
