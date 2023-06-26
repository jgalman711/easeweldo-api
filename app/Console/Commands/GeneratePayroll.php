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
    protected $signature = 'app:generate-payroll';

    protected $description = 'Process the payroll of company at the end of the period';

    public function handle()
    {
        $this->info("Processing payroll of company");
        $payrollService = app()->make(PayrollService::class);
        $searchDate = Carbon::now()->subDay()->format('Y-m-d');
        $periods = Period::where('end_date', $searchDate)->get();
        $errors = [];
        foreach ($periods as $period) {
            $employees = $period->company->employees;
            foreach ($employees as $employee) {
                try {
                    DB::beginTransaction();
                    $payrollService->generate($period, $employee);
                    DB::commit();
                } catch (Exception $e) {
                    array_push($errors, [
                        'employee_id' => $employee->id,
                        'employee_full_name' => $employee->fullName,
                        'error' => $e->getMessage()
                    ]);
                    $this->error($e->getMessage());
                    DB::rollBack();
                }
            }
        }
    }
}
