<?php

namespace App\Services\Disbursements;

use App\Enumerators\DisbursementEnumerator;

class DisbursementFactory
{
    public function initialize(array $input)
    {
        switch ($input['type']) {
            case DisbursementEnumerator::TYPE_SPECIAL:
                return new SpecialDisbursement($input);
                break;
            case DisbursementEnumerator::TYPE_NTH_MONTH_PAY:
                return new AnnualExtraDisbursement($input);
                break;
            case DisbursementEnumerator::TYPE_FINAL:
                return new FinalDisbursement($input);
                break;
            default:
                throw new \Exception("Invalid period type");
        }
    }
}