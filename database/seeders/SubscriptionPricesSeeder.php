<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionPrices;
use Illuminate\Database\Seeder;

class SubscriptionPricesSeeder extends Seeder
{
    public function run(): void
    {
        $priceMarkup = 0;
        $months = [
            1 => 200,
            12 => 190,
            24 => 175,
            36 => 150
        ];
        $subscriptions = Subscription::all();
        foreach ($subscriptions as $subscription) {
            foreach ($months as $month => $amount) {
                SubscriptionPrices::create([
                    'subscription_id' => $subscription->id,
                    'months' => $month,
                    'price_per_employee' => $amount + $priceMarkup
                ]);
            }
            $priceMarkup += 50;
        }
    }
}
