<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Http\Resources\EmployeeScheduleResource;
use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeScheduleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    protected $employeeScheduleService;

    public function __construct(EmployeeScheduleService $employeeScheduleService)
    {
        $this->employeeScheduleService = $employeeScheduleService;
        $this->setCacheIdentifier('employee-schedules');
    }

    public function index(Request $request, Company $company, Employee $employee)
    {
        try {
            $company->getEmployeeById($employee->id);
            $employeeSchedule = $this->applyFilters($request, $employee->employeeSchedules()->with('workSchedule'), [
                'status',
                'start_date',
                'workSchedule.name',
                'workSchedule.type',
            ]);

            return $this->sendResponse(
                EmployeeScheduleResource::collection($employeeSchedule),
                'Employee schedules retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store(EmployeeScheduleRequest $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $employeeSchedule = $this->employeeScheduleService->create($request, $employee);
            $this->forget($company);

            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Company $company, Employee $employee, $employeeSchedule): JsonResponse
    {
        try {
            $company->getEmployeeById($employee->id);
            if ($employeeSchedule === 'latest') {
                $employeeSchedule = $employee->employeeSchedules()
                    ->latest('start_date')
                    ->first();
            } else {
                $employeeSchedule = $employee->employeeSchedules()->find($employeeSchedule);
            }
            throw_unless(
                $employeeSchedule,
                'Work schedule not found.'
            );

            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(
        EmployeeScheduleRequest $request,
        Company $company,
        Employee $employee,
        int $employeeScheduleId
    ): JsonResponse {
        try {
            $input = $request->validated();
            $employeeSchedule = $employee->employeeSchedules()->findOrFail($employeeScheduleId);
            $employeeSchedule->update($input);
            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule updated successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, Employee $employee, int $employeeScheduleId): JsonResponse
    {
        try {
            $company->getEmployeeById($employee->id);
            $employeeSchedule = $employee->employeeSchedules()->find($employeeScheduleId);
            throw_unless(
                $employeeSchedule,
                'Work schedule not found.'
            );
            $employeeSchedule->delete();

            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
