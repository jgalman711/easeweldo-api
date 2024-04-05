<?php

namespace App\Services\Disbursements;

class BaseDisbursement
{
    protected $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }
}
