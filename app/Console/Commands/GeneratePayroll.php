<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Period;
use App\Services\Payroll\GeneratePayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GeneratePayroll extends Command
{
    protected $signature = 'app:generate-payroll {--searchDate=}'; // format Y-m-d; ex. 2023-08-10

    protected $description = 'Process the payroll of company at the end of the period';

    public function handle()
    {
        $this->info("Processing payroll of company");

        $searchDate = $this->option('searchDate');
        $searchDate = $searchDate ? Carbon::parse($searchDate) : Carbon::now();

        $minDate = $searchDate->copy()->subDays(3)->format('Y-m-d');
        $searchDate = $searchDate->format('Y-m-d');

        $periods = Period::where('end_date', '>=', $minDate)
             ->where('end_date', '<=', $searchDate)
             ->where('status', Period::STATUS_PENDING)
             ->get();

        $payrollService = app()->make(GeneratePayrollService::class);
        foreach ($periods as $period) {
            $employees = $period->company->employees->where('status', Employee::ACTIVE);
            foreach ($employees as $employee) {
                try {
                    $payrolls[] = $payrollService->generate($period->company, $period, $employee);
                } catch (Exception $e) {
                    $period->status = Period::STATUS_FAILED;
                    $payrolls[] = $e->getMessage();
                    $this->error($e->getMessage());
                }
            }
            $period->save();
        }
    }
}
