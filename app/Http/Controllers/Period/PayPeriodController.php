<?php

namespace App\Http\Controllers\Period;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Period;

class PayPeriodController extends Controller
{
    public function __invoke(Company $company, Period $period)
    {
        $period->state()->pay();
    }
}
