<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeCorrectionRequest;
use App\Http\Resources\TimeCorrectionResource;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeCorrectionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-corrections",
     *     summary="Get time correction requests for a specific employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Corrections"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for the results",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Time correction requests retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time records retrieved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(response="404", description="Time Correction not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
    public function index(Request $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeCorrections = $this->applyFilters($request, $employee->timeCorrections());

        return $this->sendResponse(
            TimeCorrectionResource::collection($timeCorrections),
            'Time correction requests retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-corrections",
     *     summary="Store a new time correction record",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Corrections"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="date", type="string", format="date-time", example="2023-09-01 13:00:00"),
     *             @OA\Property(property="clock_in", type="string", format="date-time", example="2023-09-01 13:00:00"),
     *             @OA\Property(property="clock_out", type="string", format="date-time", example="2023-09-01 17:00:00"),
     *             @OA\Property(property="title", type="string", example="Correction Title"),
     *             @OA\Property(property="remarks", type="string", example="Correction Remarks"),
     *             @OA\Property(property="status", type="string", example="pending"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Time correction requests retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time records retrieved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error."),
     *         ),
     *     ),
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-corrections/{time-correction-id}",
     *     summary="Get details of a specific time correction record for an employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Corrections"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="time-correction-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time correction record",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Time correction details retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time correction retrieved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Time correction not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Time correction not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     *
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     */
    public function show(Company $company, int $employeeId, int $timeCorrectionId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $company->getEmployeeById($employee->id);
            $timeCorrection = $employee->timeCorrections()->find($timeCorrectionId);
            throw_unless(
                $timeCorrection,
                'Time correction not found.'
            );

            return $this->sendResponse(
                new TimeCorrectionResource($timeCorrection),
                'Time correction details retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-corrections/{time-correction-id}",
     *     summary="Update a specific time correction record for an employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Corrections"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="time-correction-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time correction record",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *      @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="date", type="string", format="date-time", example="2023-09-01 13:00:00"),
     *             @OA\Property(property="clock_in", type="string", format="date-time", example="2023-09-01 13:00:00"),
     *             @OA\Property(property="clock_out", type="string", format="date-time", example="2023-09-01 17:00:00"),
     *             @OA\Property(property="title", type="string", example="Correction Title"),
     *             @OA\Property(property="remarks", type="string", example="Correction Remarks"),
     *             @OA\Property(property="status", type="string", example="pending"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Time correction updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time correction updated successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Time correction not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Time correction not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-corrections/{time-correction-id}",
     *     summary="Delete a specific time correction record for an employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Corrections"},
     *
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="time-correction-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time correction record",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Time correction deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Time correction not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Time correction not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     */
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
