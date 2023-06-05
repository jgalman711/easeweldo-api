<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function show(Company $company, int $employeeId): Response
    {
        $employee = $company->getEmployeeById($employeeId);
        $data = QrCode::size(256)
            ->format('png')
            ->merge('/storage/app/es-logo.jpg')
            ->errorCorrection('M')
            ->generate(url('api/companies/' . $company->id . '/employees/' . $employee->id . '/clock'));
        return response($data)->header('Content-type', 'image/png');
    }
}
