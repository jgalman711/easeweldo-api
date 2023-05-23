<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeOfCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }
        $companyId = $request->route('company') instanceof Company
            ? $request->route('company')->id
            : Company::where('slug', $request->route('company'))->first()->id;

        if ($user->employee && $user->employee->company_id == $companyId) {
            return $next($request);
        }
        return response()->json([
            'message' => 'Access Denied. You are not authorized to perform this action in the current company'
        ], 401);
    }
}
