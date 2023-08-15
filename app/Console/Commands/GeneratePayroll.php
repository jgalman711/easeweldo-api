<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Services\PayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        $payrollService = app()->make(PayrollService::class);
        foreach ($periods as $period) {
            $employees = $period->company->employees;
            foreach ($employees as $employee) {
                try {
                    DB::beginTransaction();
                    $payrollService->generate($period, $employee);
                    $this->info("Processed payroll for employee {$employee->fullName}");
                    DB::commit();
                } catch (Exception $e) {
                    $errors[] = [
                        'employee_id' => $employee->id,
                        'employee_full_name' => $employee->fullName,
                        'error' => $e->getMessage()
                    ];
                    $this->error($e->getMessage());
                    DB::rollBack();
                }
            }
        }
    }
}
