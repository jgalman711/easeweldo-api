<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkScheduleRequest;
use App\Http\Resources\WorkScheduleResource;
use App\Models\Company;
use App\Models\WorkSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    private const WORK_SCHEDULE_DOES_NOT_EXIST = "Work schedule does not exist.";

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/work-schedules",
     *     summary="Get a list of work schedules for a specific company",
     *     security={{"bearerAuth":{}}},
     *     tags={"Work Schedules"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for the results",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Work schedules retrieved successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Work schedules retrieved successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="monday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="monday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="tuesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="tuesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="wednesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="wednesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="thursday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="thursday_clock_out_time", type="string", format="time", example="19:00:00"),
     *                         @OA\Property(property="friday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="friday_clock_out_time", type="string", format="time", example="19:00:00"),
     *                         @OA\Property(property="saturday_clock_in_time", type="string", format="time", example=null),
     *                         @OA\Property(property="saturday_clock_out_time", type="string", format="time", example=null),
     *                         @OA\Property(property="sunday_clock_in_time", type="string", format="time", example="08:00:00"),
     *                         @OA\Property(property="sunday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Company not found")
     * )
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        $workSchedules = $this->applyFilters($request, $company->workSchedules());
        return $this->sendResponse(
            WorkScheduleResource::collection($workSchedules),
            'Work schedules retrieved successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/work-schedules/{id}",
     *     summary="Get details of a specific work schedule",
     *     security={{"bearerAuth":{}}},
     *     tags={"Work Schedules"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the work schedule",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Work schedule retrieved successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="success", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Work schedules retrieved successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="monday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="monday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="tuesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="tuesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="wednesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="wednesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                         @OA\Property(property="thursday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="thursday_clock_out_time", type="string", format="time", example="19:00:00"),
     *                         @OA\Property(property="friday_clock_in_time", type="string", format="time", example="13:00:00"),
     *                         @OA\Property(property="friday_clock_out_time", type="string", format="time", example="19:00:00"),
     *                         @OA\Property(property="saturday_clock_in_time", type="string", format="time", example=null),
     *                         @OA\Property(property="saturday_clock_out_time", type="string", format="time", example=null),
     *                         @OA\Property(property="sunday_clock_in_time", type="string", format="time", example="08:00:00"),
     *                         @OA\Property(property="sunday_clock_out_time", type="string", format="time", example="17:00:00"),
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Company not found")
     * )
     */
    public function show(Company $company, int $workScheduleId): JsonResponse
    {
        $workSchedule = $company->getWorkScheduleById($workScheduleId);
        if (!$workSchedule) {
            return $this->sendError(self::WORK_SCHEDULE_DOES_NOT_EXIST);
        }
        return $this->sendResponse(new WorkScheduleResource($workSchedule), 'Work schedules retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/work-schedules",
     *     summary="Create a new work schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="monday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="monday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="tuesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="tuesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="wednesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="wednesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="thursday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="thursday_clock_out_time", type="string", format="time", example="19:00:00"),
     *             @OA\Property(property="friday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="friday_clock_out_time", type="string", format="time", example="19:00:00"),
     *             @OA\Property(property="saturday_clock_in_time", type="string", format="time", example=null),
     *             @OA\Property(property="saturday_clock_out_time", type="string", format="time", example=null),
     *             @OA\Property(property="sunday_clock_in_time", type="string", format="time", example="08:00:00"),
     *             @OA\Property(property="sunday_clock_out_time", type="string", format="time", example="17:00:00"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Work schedule created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     * )
     */
    public function store(WorkScheduleRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $workSchedule = WorkSchedule::create($input);
        return $this->sendResponse(new WorkScheduleResource($workSchedule), 'Work schedule created successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{company-slug}/work-schedules/{id}",
     *     summary="Update an existing work schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the work schedule to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="monday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="monday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="tuesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="tuesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="wednesday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="wednesday_clock_out_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="thursday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="thursday_clock_out_time", type="string", format="time", example="19:00:00"),
     *             @OA\Property(property="friday_clock_in_time", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="friday_clock_out_time", type="string", format="time", example="19:00:00"),
     *             @OA\Property(property="saturday_clock_in_time", type="string", format="time", example=null),
     *             @OA\Property(property="saturday_clock_out_time", type="string", format="time", example=null),
     *             @OA\Property(property="sunday_clock_in_time", type="string", format="time", example="08:00:00"),
     *             @OA\Property(property="sunday_clock_out_time", type="string", format="time", example="17:00:00"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Work schedule updated successfully"),
     *     @OA\Response(response="404", description="Work schedule not found"),
     *     @OA\Response(response="422", description="Validation error"),
     * )
     */
    public function update(WorkScheduleRequest $request, Company $company, WorkSchedule $workSchedule): JsonResponse
    {
        $company->getWorkScheduleById($workSchedule->id);
        $workSchedule->update($request->all());
        return $this->sendResponse(new WorkScheduleResource($workSchedule), 'Work schedule updated successfully.');
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}/work-schedules/{id}",
     *     summary="Delete a work schedule",
     *     tags={"Work Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the work schedule to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Work schedule deleted successfully"),
     *     @OA\Response(response="404", description="Work schedule not found"),
     * )
     */
    public function destroy(Company $company, int $workScheduleId): JsonResponse
    {
        $workSchedule = $company->getWorkScheduleById($workScheduleId);
        if (!$workSchedule) {
            return $this->sendError(self::WORK_SCHEDULE_DOES_NOT_EXIST);
        }
        $workSchedule->delete();
        return $this->sendResponse(new WorkScheduleResource($workSchedule), 'Work schedule deleted successfully.');
    }
}
