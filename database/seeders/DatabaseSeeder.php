<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            WorkScheduleSeeder::class,
            SubscriptionSeeder::class,
            CompanySeeder::class
        ]);
    }
}
