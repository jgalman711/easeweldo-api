<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Services\PayrollService;
use App\Services\PeriodService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePayroll extends Command
{
    protected $signature = 'app:generate-payroll';

    protected $description = 'Process the payroll of company at the end of the period';

    public function handle()
    {
        $this->info("Processing payroll of company");
        $payrollService = app()->make(PayrollService::class);
        $periodService = app()->make(PeriodService::class);
        $searchDate = Carbon::now()->subDay()->format('Y-m-d');
        $searchDate = '2023-07-10';
        $periods = Period::where('end_date', $searchDate)->get();
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
            $periodService->calculatePeriod($period);
        }
    }
}
