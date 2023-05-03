<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SameCompanyAsAdminUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }
        if ($user && $user->hasRole('business-admin')) {
            $employee = $request->route('employee') instanceof Employee
            ? $request->route('employee')
            : Employee::find($request->route('employee'));
            if ($user->employee->company_id == $employee->company_id) {
                return $next($request);
            }
        }
        return response()->json(['message' => 'Unauthorized.'], 401);
    }
}
