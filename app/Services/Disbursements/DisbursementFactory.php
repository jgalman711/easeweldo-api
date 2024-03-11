<?php

namespace App\Services\Disbursements;

use App\Enumerators\DisbursementEnumerator;

class DisbursementFactory
{
    public function initialize(array $input)
    {
        return match ($input['type']) {
            DisbursementEnumerator::TYPE_SPECIAL => new SpecialDisbursement($input),
            DisbursementEnumerator::TYPE_NTH_MONTH_PAY => new AnnualExtraDisbursement($input),
            DisbursementEnumerator::TYPE_FINAL => new FinalDisbursement($input),
            default => throw new \Exception("Invalid period type")
        };
    }
}
