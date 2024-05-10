<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email_address' => 'admin@easeweldo.com',
            'first_name' => 'super',
            'last_name' => 'admin',
            'password' => bcrypt('password'),
        ])->assignRole('super-admin');
    }
}
