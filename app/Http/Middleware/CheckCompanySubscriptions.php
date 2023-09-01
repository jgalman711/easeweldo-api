<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;

class CheckCompanySubscriptions
{
    public function handle(Request $request, Closure $next)
    {
        $company = $request->route('company') instanceof Company
            ? $request->route('company')
            : Company::where('slug', $request->route('company'))->first();

        if ($company && $company->hasTimeAndAttendanceSubscription) {
            return $next($request);
        }
        return response()->json(['message' => 'Company does not have required subscriptions'], 403);
    }
}
