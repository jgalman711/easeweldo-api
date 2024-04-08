<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class ApproveLeaveController extends Controller
{
    public function __invoke(Company $company, int $leaveId)
    {
        $user = Auth::user()->load('employee.company');
        $leave = $company->leaves()->findOrFail($leaveId);
        $leave->approve(user: $user);
        return $this->sendResponse($leave, "Leave approved successfully");
    }
}
