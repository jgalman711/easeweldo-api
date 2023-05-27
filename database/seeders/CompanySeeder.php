<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name' => 'Easeweldo',
            'slug' => 'easeweldo',
            'status' => 'active',
            'legal_name' => 'Easeweldo Inc.',
            'address_line_1' => '123 Main Street',
            'address_line_2' => 'Greatland Village',
            'barangay_town_city_province' => 'Brgy Cuyab, San Pedro, Laguna',
            'contact_name' => 'Easeweldo HQ',
            'email_address' => 'ease@easeweldo.com',
            'mobile_number' => '09111111111',
            'landline_number' => '1111-2222',
            'bank_name' => 'BPI',
            'bank_account_name' => 'Easeweldo Inc',
            'bank_account_number' => '123456789000',
            'tin' => '123-456-789-000',
            'sss_number' => '34-5678901-2',
            'philhealth_number' => '12-345678901-2',
            'pagibig_number' => '10001111000011100'
        ]);
        $subscription = Subscription::find(1);
        CompanySubscription::create([
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYear(),
        ]);
    }
}
