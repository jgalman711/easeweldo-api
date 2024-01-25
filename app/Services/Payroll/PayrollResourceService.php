<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Resources\PayrollDetailsResouce;
use App\Http\Resources\PayrollResource;
use App\Models\Payroll;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResourceService
{
    protected $regularPayrollService;

    public function format(Payroll $payroll, ?string $format = 'default'): JsonResource
    {
        if ($format == PayrollEnumerator::FORMAT_DETAILS) {
            return new PayrollDetailsResouce($payroll);
        } else {
            return new PayrollResource($payroll);
        }
    }
}
