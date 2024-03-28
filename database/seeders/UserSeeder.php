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
            'username' => 'admin',
            'password' => bcrypt('pixHC%u0'),
        ])->assignRole('super-admin');
    }
}
