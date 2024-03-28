<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Console\Command;

class UpdatePendingEmployeesStatus extends Command
{
    protected $signature = 'app:update-pending-employees-status';

    protected $description = 'Update status of pending employees for companies not in settlement period';

    public function handle()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            if (! $company->isInSettlementPeriod()) {
                $company->employees()->where('status', Employee::PENDING)
                    ->update(['status' => Employee::ACTIVE]);
            }
        }
        $this->info('Pending employee statuses updated.');
    }
}
