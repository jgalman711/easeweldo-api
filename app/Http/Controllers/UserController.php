<?php

namespace App\Http\Controllers;

use App\Services\QrService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $qrService;

    public function __construct(QrService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function qrcode(): Response
    {
        $user = Auth::user();
        $employee = $user->employee;
        $company = $employee->company;
        $data = $this->qrService->generate($company->id, $employee->id);
        return response($data)->header('Content-type', 'image/png');
    }
}
