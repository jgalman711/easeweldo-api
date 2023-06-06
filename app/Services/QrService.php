<?php

namespace App\Services;

use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    private const M_SIZE = 256;

    public function generate(int $companyId, int $employeeId): HtmlString
    {
        return QrCode::size(self::M_SIZE)
            ->format('png')
            ->merge('/storage/app/es-logo.jpg')
            ->errorCorrection('M')
            ->generate(url('api/companies/' . $companyId . '/employees/' . $employeeId . '/clock'));
    }
}
