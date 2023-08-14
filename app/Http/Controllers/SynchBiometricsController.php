<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\BiometricsService;
use Exception;
use Illuminate\Support\Facades\DB;

class SynchBiometricsController extends Controller
{
    protected $biometricsService;

    public function __construct(BiometricsService $biometricsService)
    {
        $this->biometricsService = $biometricsService;
    }

    public function store(Company $company, string $module)
    {
        try {
            DB::beginTransaction();
            $biometricsDevices = $company->biometrics;

            if ($biometricsDevices->isEmpty()) {
                return $this->sendError("No registered biometrics devices");
            }

            if ($module == 'employees') {
                foreach ($biometricsDevices as $biometricsDevice) {
                    $this->biometricsService->synchEmployees($biometricsDevice, $company->employees);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "User data synched with biometric devices successfully."
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendError("Unable to synch data. ", $e->getMessage());
        }
    }
}
