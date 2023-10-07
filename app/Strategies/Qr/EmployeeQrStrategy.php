<?php

namespace App\Strategies\Qr;

use App\Interfaces\QrStrategy;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EmployeeQrStrategy implements QrStrategy
{
    private const M_SIZE = 256;

    public function generate(array $data): HtmlString
    {
        $url = "companies/{$data['company_slug']}/employees/{$data['employee_id']}/qr-clock";
        return QrCode::size(self::M_SIZE)
            ->format('png')
            ->merge('/storage/app/qr-logo.png')
            ->errorCorrection('M')
            ->generate($url);
    }
}
