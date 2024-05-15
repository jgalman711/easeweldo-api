<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeclineLeaveController extends Controller
{
    public function __invoke(Request $request, Company $company, int $leaveId)
    {
        try {
            $leave = $company->leaves()->findOrFail($leaveId);
            $leave->state()->decline($request->reason);
            return $this->sendResponse(new LeaveResource($leave), "Leave declined successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->sendError("Unable to decline leave.");
        }
    }
}
