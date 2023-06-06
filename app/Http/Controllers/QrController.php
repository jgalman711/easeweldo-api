<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\QrService;
use Illuminate\Http\Response;

class QrController extends Controller
{
    protected $qrService;

    public function __construct(QrService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function show(Company $company, int $employeeId): Response
    {
        $employee = $company->getEmployeeById($employeeId);
        $data = $this->qrService->generate($company->id, $employee->id);
        return response($data)->header('Content-type', 'image/png');
    }
}
