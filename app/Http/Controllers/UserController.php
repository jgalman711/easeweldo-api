<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    public function qrcode(): Response
    {
        $user = Auth::user();
        $employee = $user->employee;
        $company = $employee->company;
        $data = QrCode::size(256)
            ->format('png')
            ->merge('/storage/app/es-logo.jpg')
            ->errorCorrection('M')
            ->generate(url('api/companies/' . $company->id . '/employees/' . $employee->id . '/clock'));
        return response($data)->header('Content-type', 'image/png');
    }
}
