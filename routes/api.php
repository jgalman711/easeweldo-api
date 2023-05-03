<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\SalaryComputationController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [LoginController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::group(['middleware' => ['role:super-admin|business-admin', 'employee-of-company']], function () {
        Route::resource('companies', CompanyController::class);
        Route::resource('companies.employees', EmployeeController::class);
        Route::resource('companies.work-schedules', WorkScheduleController::class);
        Route::resource('companies.payrolls', PayrollController::class);
        Route::prefix('companies/{company}/employees/{employee}')->group(function () {
            Route::get('/time-record', [TimeRecordController::class, 'getTimeRecords']);
            Route::post('/clock-in', [TimeRecordController::class, 'clockIn']);
            Route::post('/clock-out', [TimeRecordController::class, 'clockOut']);
            Route::prefix('salary-computation')->group(function () {
                Route::get('/', [SalaryComputationController::class, 'show']);
                Route::post('/', [SalaryComputationController::class, 'store']);
                Route::put('/', [SalaryComputationController::class, 'update']);
                Route::delete('/', [SalaryComputationController::class, 'delete']);
            });
        });
        Route::resource('companies.payroll-periods', PeriodsController::class);
    });
    Route::prefix('employee/{employee}')->middleware(['same-company-as-admin-user'])->group(function () {
        Route::resource('/payrolls', PayrollController::class)->only('show');
        Route::resource('/work-schedule', EmployeeScheduleController::class)->only('store');
    });
});

