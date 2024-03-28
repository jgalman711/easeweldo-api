<?php

namespace App\Http\Controllers\Qr;

use App\Factories\QrStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeQrRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\ClockService;
use Exception;
use Illuminate\Http\Response;

class EmployeeQrController extends Controller
{
    protected $clockService;

    protected $qrService;

    public function __construct(QrStrategyFactory $qrStrategyFactory, ClockService $clockService)
    {
        $this->clockService = $clockService;
        $this->qrService = $qrStrategyFactory->createStrategy('employee');
    }

    /**
     * Generate a QR code for the employee id.
     * Display the user's QR Code to the company terminal's Qr Scanner.
     */
    public function index(Company $company, int $employeeId): Response
    {
        $employee = $company->employees()->find($employeeId);
        $qr = $this->qrService->generate([
            'action' => 'clock',
            'employee_id' => $employee->id,
        ]);

        return response($qr)->header('Content-type', 'image/png');
    }

    /**
     * User will scan the company's qr code
     */
    public function store(EmployeeQrRequest $employeeQrRequest, Company $company)
    {
        try {
            $employee = $company->getEmployeeById($employeeQrRequest->employee_id);
            [$timeRecord, $message] = $this->clockService->clockAction($employee);

            return $this->sendResponse(new BaseResource($timeRecord), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
