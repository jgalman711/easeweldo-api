<?php

namespace App\Http\Controllers;

use App\Http\Requests\HolidayRequest;
use App\Http\Resources\BaseResource;
use App\Models\Holiday;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HolidayController extends Controller
{
    public function index(): JsonResponse
    {
        $holidays = Cache::remember('holidays', 3660, function () {
            return Holiday::all();
        });

        return $this->sendResponse(BaseResource::collection($holidays), 'Holidays retrieved successfully.');
    }

    public function store(HolidayRequest $request): JsonResponse
    {
        $data = $request->validated();
        $holiday = Holiday::create($data);

        return $this->sendResponse(new BaseResource($holiday), 'Holiday created successfully.');
    }

    public function show(Holiday $holiday): JsonResponse
    {
        return $this->sendResponse(new BaseResource($holiday), 'Holiday retrieved successfully.');
    }

    public function update(HolidayRequest $request, Holiday $holiday): JsonResponse
    {
        $data = $request->validated();
        $holiday->update($data);

        return $this->sendResponse(new BaseResource($holiday), 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday): JsonResponse
    {
        $holiday->delete();

        return $this->sendResponse(new BaseResource($holiday), 'Holiday updated successfully.');
    }
}
