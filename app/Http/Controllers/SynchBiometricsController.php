<?php

namespace App\Http\Controllers;

use App\Http\Requests\SynchBiometricsRequest;
use App\Models\Company;
use App\Services\BiometricsService;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Support\Facades\DB;

class SynchBiometricsController extends Controller
{
    protected const EMPLOYEE_MODULE = 'employees';

    protected const ATTENDANCE_MODULE = 'attendance';

    protected $biometricsService;

    protected $timeRecordService;

    public function __construct(BiometricsService $biometricsService, TimeRecordService $timeRecordService)
    {
        $this->biometricsService = $biometricsService;
        $this->timeRecordService = $timeRecordService;
    }

    public function store(SynchBiometricsRequest $request, Company $company, string $module)
    {
        $request->validated();
        try {
            DB::beginTransaction();
            $biometricsDevices = $company->biometrics;

            if ($biometricsDevices->isEmpty()) {
                return $this->sendError('No registered biometrics device.');
            }

            if ($module == self::EMPLOYEE_MODULE) {
                foreach ($biometricsDevices as $biometricsDevice) {
                    $this->biometricsService->synchEmployees($biometricsDevice, $company->employees);
                }
            } elseif ($module == self::ATTENDANCE_MODULE) {
                foreach ($biometricsDevices as $biometricsDevice) {
                    $attendance = $this->biometricsService->getAttendance($biometricsDevice, $request);
                    $this->timeRecordService->synchFromBiometrics($attendance, $company);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User data synched with biometric devices successfully.',
            ], 200);
        } catch (Exception $e) {
            DB::rollback();

            return $this->sendError($e->getMessage());
        }
    }
}
