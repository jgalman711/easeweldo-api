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

        $company = $request->route('company');
        $companySlug = $company instanceof Company ? $company->slug : $request->route('company');
        $employeeId = $request->route('employee');

        if (
            $user && optional($user->employee)->id == $employeeId &&
            $user->companies()->where('slug', $companySlug)->exists()
        ) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access Denied. You are not authorized to perform this action in the current company'
        ], 401);
    }
}
