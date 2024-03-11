<?php

namespace App\Repositories;

use App\Models\Period;

class DisbursementRepository
{
    public function getLatestDisbursement(string $type, string $status): ?Period
    {
        return Period::where([
            ['type', $type],
            ['status', $status]
        ])
        ->orderBy('salary_date', 'desc')
        ->first();
    }
}
