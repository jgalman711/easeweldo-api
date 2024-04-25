<?php

namespace App\Traits;

use App\Enumerators\DisbursementEnumerator;

trait HasLatestPeriods
{
    public function getLatestPeriodAttribute()
    {
        return $this->periods()->latest()->first();
    }

    public function getCompletedRegularDisbursements(int $count = 1)
    {
        $query = $this->periods()->latest()->where([
            ['type', DisbursementEnumerator::TYPE_REGULAR],
            ['status', DisbursementEnumerator::STATUS_COMPLETED]
        ]);
        return $query->take($count)->get();
    }
}
