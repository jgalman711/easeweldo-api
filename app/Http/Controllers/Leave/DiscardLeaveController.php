<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveResource;
use App\Models\Company;
use Illuminate\Http\Request;

class DiscardLeaveController extends Controller
{
    public function __invoke(Request $request, Company $company, int $leaveId)
    {
        $leave = $company->leaves()->findOrFail($leaveId);
        $leave->state()->discard($request->reason);
        return $this->sendResponse(new LeaveResource($leave), "Leave discarded successfully");
    }
}
