<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\WorkSchedule;
use Illuminate\Http\JsonResponse;

class WorkScheduleController extends Controller
{
    private const WORK_SCHEDULE_DOES_NOT_EXIST = "Work schedule does not exist.";

    public function index(Company $company): JsonResponse
    {
        $workSchedule = $company->workSchedules;
        return $this->sendResponse(
            BaseResource::collection($workSchedule),
            'Work schedules retrieved successfully.'
        );
    }

    public function show(Company $company, int $workScheduleId): JsonResponse
    {
        $workSchedule = $company->getWorkScheduleById($workScheduleId);
        if (!$workSchedule) {
            return $this->sendError(self::WORK_SCHEDULE_DOES_NOT_EXIST);
        }
        return $this->sendResponse(new BaseResource($workSchedule), 'Work schedules retrieved successfully.');
    }

    public function store(WorkScheduleRequest $request, Company $company): JsonResponse
    {
        $workSchedule = WorkSchedule::where([
            'name' => $request->name,
            'company_id' => $company->id
        ])->first();

        if ($workSchedule) {
            return $this->sendError("Work schedule name already exists.");
        }
        
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $workSchedule = WorkSchedule::create($input);
        return $this->sendResponse(new BaseResource($workSchedule), 'Work schedule created successfully.');
    }

    public function update(WorkScheduleRequest $request, Company $company, int $workScheduleId): JsonResponse
    {
        $workSchedule = $company->getWorkScheduleById($workScheduleId);
        if (!$workSchedule) {
            return $this->sendError(self::WORK_SCHEDULE_DOES_NOT_EXIST);
        }

        $sameWorkScheduleName = WorkSchedule::where([
            ['id', '!=', $workScheduleId],
            ['name', $request->name],
            ['company_id', $company->id],
        ])->first();
        
        if (!$sameWorkScheduleName) {
            return $this->sendError("Work schedule name already exists.");
        }
        $input = $request->validated();
        $workSchedule->update($input);
        return $this->sendResponse(new BaseResource($workSchedule), 'Work schedule updated successfully.');
    }

    public function destroy(Company $company, int $workScheduleId): JsonResponse
    {
        $workSchedule = $company->getWorkScheduleById($workScheduleId);
        if (!$workSchedule) {
            return $this->sendError(self::WORK_SCHEDULE_DOES_NOT_EXIST);
        }
        $workSchedule->delete();
        return $this->sendResponse(new BaseResource($workSchedule), 'Work schedule deleted successfully.');
    }
}