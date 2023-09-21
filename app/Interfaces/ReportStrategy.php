<?php

namespace App\Interfaces;

use App\Models\Company;

interface ReportStrategy
{
    public function generate(Company $company, array $data);
}
