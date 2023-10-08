<?php

namespace App\Strategies\Qr;

use App\Interfaces\QrStrategy;
use Illuminate\Support\HtmlString;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CompanyQrStrategy implements QrStrategy
{
    private const M_SIZE = 256;

    public function generate(): HtmlString
    {
        $queryString = http_build_query([
            'action' => 'clock'
        ]);

        return QrCode::size(self::M_SIZE)
            ->format('png')
            ->merge('/storage/app/qr-logo.png')
            ->errorCorrection('M')
            ->generate($queryString);
    }
}
