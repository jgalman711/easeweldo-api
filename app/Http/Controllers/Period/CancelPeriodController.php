<?php

namespace App\Http\Controllers\Period;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeriodResource;
use App\Models\Company;
use App\Models\Period;
use Exception;

class CancelPeriodController extends Controller
{
    public function __invoke(Company $company, Period $period)
    {
        try {
            $period->state()->cancel();

            return $this->sendResponse(new PeriodResource($period), 'Period cancelled successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
