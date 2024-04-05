<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Employee;
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

        $company = $request->route('company') instanceof Company
            ? $request->route('company')
            : Company::findOrFail($request->route('company'));
        $employee = $request->route('employee') instanceof Employee
            ? $request->route('employee')
            : Employee::findOrFail($request->route('employee'));

        if ($user->companies()->where('slug', $company->slug)->exists()
            && optional($employee->company)->slug == $company->slug
        ) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access Denied. You are not authorized to perform this action in the current company',
        ], 401);
    }
}
