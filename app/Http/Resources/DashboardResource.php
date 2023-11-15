<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class DashboardResource extends BaseResource
{
    protected $expectedClockIn;

    protected $expectedClockOut;

    public function toArray(Request $request): array
    {
        $period = $this['period'];
        return [
            'id' => $period->id,
            'company_id' => $period->company_id,
            'company_period_id' => $period->company_period_id,
            'description' => $period->description,
            'type' => $period->type,
            'start_date' => $period->start_date,
            'end_date' => $period->end_date,
            'salary_date' => $period->salary_date,
            'status' => $period->status,
            'employees_count' => $period->employees_count,
            'employees_net_pay' => $period->employees_net_pay,
            'withheld_taxes' => $period->withheld_taxes,
            'total_contributions' => $period->total_contributions,
            'payroll_cost' => $period->payroll_cost,
            'base_pay' => $period->payrolls->sum('base_pay'),
            'leaves_pay' => $period->payrolls->sum('leaves_pay'),
            'holiday_pay' => $period->payrolls->sum('holiday_pay'),
            'overtime_pay' => $period->payrolls->sum('overtime_pay')
        ];
    }
}
