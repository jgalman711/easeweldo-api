<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::create([
            'name' => 'Basic Ease 200',
            'amount' => 200,
            'details' => json_encode([
                'title' => 'Basic payroll ease',
                'description' => "Description basic type"
            ])
        ]);
    }
}
