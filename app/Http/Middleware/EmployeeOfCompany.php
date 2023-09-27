<?php

namespace App\Http\Middleware;

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
        $companySlug = $request->route('company');
        $employeeId = $request->route('employee');

        if (
            $user && $user->employee->id == $employeeId &&
            $user->companies()->where('slug', $companySlug->slug)->exists()
        ) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access Denied. You are not authorized to perform this action in the current company'
        ], 401);
    }
}
