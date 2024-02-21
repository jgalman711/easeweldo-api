<?php

namespace App\Services\Contributions;

use App\Services\TaxService;

final class ContributionsService
{
    public $pagIbigCalculatorService;
    public $philHealthCalculatorService;
    public $sssCalculatorService;
    public $taxCalculatorService;

    public function __construct(
        PagIbigCalculatorService $pagIbigCalculatorService,
        PhilHealthCalculatorService $philHealthCalculatorService,
        SSSCalculatorService $sssCalculatorService,
        TaxService $taxCalculatorService
    ) {
        $this->pagIbigCalculatorService = $pagIbigCalculatorService;
        $this->philHealthCalculatorService = $philHealthCalculatorService;
        $this->sssCalculatorService = $sssCalculatorService;
        $this->taxCalculatorService = $taxCalculatorService;
    }
}
