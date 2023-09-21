<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PayrollExpenseReportResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return $this->transformData($this);
    }

    protected function transformData($data)
    {
        $result = [];
        foreach ($data->resource as $key => $monthsData) {
            foreach ($monthsData as $month => $value) {
                $result[strtolower($month)][$key] = $value;
            }
        }
        return $result;
    }
}
