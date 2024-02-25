<?php

namespace App\Repositories;

use App\Enumerators\DisbursementEnumerator;
use App\Models\Period;

class DisbursementRepository
{
    public function getLatestPaidDisbursement(string $type): Period
    {
        return Period::where([
            ['type', $type],
            ['status', DisbursementEnumerator::STATUS_COMPLETED]
        ])
        ->orderBy('salary_date', 'desc')
        ->first();
    }
}
