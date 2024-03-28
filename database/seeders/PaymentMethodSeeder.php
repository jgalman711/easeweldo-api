<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        PaymentMethod::create([
            'bank_name' => 'Bank of the Philippine Islands',
            'account_name' => 'Jermaine Galman',
            'account_number' => '100111010124',
            'logo' => 'assets/images/payments/bpi-logo.png',
        ]);

        PaymentMethod::create([
            'bank_name' => 'Banco De Oro',
            'account_name' => 'Jermaine Galman',
            'account_number' => '1001110104412124',
            'logo' => 'assets/images/payments/bdo-logo.png',
        ]);

        PaymentMethod::create([
            'bank_name' => 'G-Cash',
            'account_name' => 'Jermaine Galman',
            'account_number' => '09173028253',
            'logo' => 'assets/images/payments/gcash-logo.png',
        ]);
    }
}
