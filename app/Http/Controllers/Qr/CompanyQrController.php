<?php

namespace App\Http\Controllers\Qr;

use App\Factories\QrStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qr\CompanyQrRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\ClockService;
use Exception;
use Illuminate\Http\Response;

class CompanyQrController extends Controller
{
    protected $clockService;

    protected $qrService;

    public function __construct(ClockService $clockService, QrStrategyFactory $qrStrategyFactory)
    {
        $this->clockService = $clockService;
        $this->qrService = $qrStrategyFactory->createStrategy('company');
    }

    /**
     * Generate a QR code for the company.
     * Display the company's QR Code to the company terminal.
     */
    public function index(Company $company): Response
    {
        $qr = $this->qrService->generate([
            'company_slug' => $company->slug
        ]);
        return response($qr)->header('Content-type', 'image/png');
    }

    /**
     * Company will scan the employee's qr code
     */
    public function store(CompanyQrRequest $request, Company $company)
    {
        try {
            $input = $request->validated();
            $employee = $company->getEmployeeById($request->employee_id);
            list($timeRecord, $message) = $this->clockService->clockAction($employee, $input);
            return $this->sendResponse(new BaseResource($timeRecord), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
