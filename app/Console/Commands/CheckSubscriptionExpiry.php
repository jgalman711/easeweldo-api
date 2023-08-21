<?php

namespace App\Console\Commands;

use App\Models\CompanySubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    protected $signature = 'app:check-subscription-expiry';

    protected $description = 'Check if company_subscription end_date is expired';

    public function handle()
    {
        $companySubscriptions = CompanySubscription::where('end_date', '<', Carbon::now())->get();

        foreach ($companySubscriptions as $companySubscription) {
            $companySubscription->status = Subscription::UNPAID_STATUS;
            $companySubscription->save();
        }

        $this->info('Subscription expiry check completed.');
    }
}
