<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'mobile_number' => '09170000001',
            'password' => bcrypt('123456')
        ])->assignRole('super-admin');
    }
}
