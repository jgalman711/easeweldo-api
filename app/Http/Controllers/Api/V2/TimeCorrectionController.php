<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeCorrectionRequest;
use App\Http\Resources\V2\TimeCorrectionResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TimeCorrection;
use Exception;
use Illuminate\Http\JsonResponse;

class TimeCorrectionController extends Controller
{
    public function index(Company $company, Employee $employee): JsonResponse
    {
        $timeCorrections = $employee->timeCorrections()->paginate();
        $timeCorrections->getCollection()->each->setRelation('employee', $employee);
        $timeCorrections->getCollection()->each->setRelation('company', $company);
        return $this->sendResponse(TimeCorrectionResource::collection($timeCorrections), 'Time correction requests retrieved successfully.');
    }

    public function show(Company $company, Employee $employee, TimeCorrection $timeCorrection): JsonResponse
    {
        try {
            $timeCorrection->setRelation('employee', $employee);
            $timeCorrection->setRelation('company', $company);
            return $this->sendResponse(new TimeCorrectionResource($timeCorrection), 'Time correction details retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store(TimeCorrectionRequest $request, Company $company, int $employeeId)
    {
        try {
            $input = $request->validated();
            $employee = $company->getEmployeeById($employeeId);
            $input['company_id'] = $company->id;
            $timeCorrection = $employee->timeCorrections()->create($input);

            return $this->sendResponse(
                new TimeCorrectionResource($timeCorrection),
                'Time correction created successfully.',
                200
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(
        TimeCorrectionRequest $request,
        Company $company,
        int $employeeId,
        int $timeCorrectionId
    ): JsonResponse {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $timeCorrection = $employee->timeCorrections()->find($timeCorrectionId);

            throw_unless(
                $timeCorrection,
                'Time correction not found.'
            );
            $timeCorrection->update($request->all());

            return $this->sendResponse(
                new TimeCorrectionResource($timeCorrection),
                'Time correction updated successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, int $employeeId, int $timeCorrectionId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $timeCorrection = $employee->timeCorrections()->find($timeCorrectionId);
            throw_unless(
                $timeCorrection,
                'Time correction not found.'
            );
            $timeCorrection->delete();

            return response()->json(null, 204);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
