<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\SalaryComputationController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\WorkScheduleController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('artisan-migrate', function () {
    Artisan::call('migrate', [
        '--force' => true
    ]);
});
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('forgot-password', [ForgotPasswordController::class, 'forgot']);

Route::group(['middleware' => 'auth:sanctum', ['role:super-admin']], function () {
    Route::resource('holidays', HolidayController::class);
    Route::resource('companies', CompanyController::class);
    Route::get('employees', [EmployeeController::class, 'all']);
    Route::group(['middleware' => ['role:super-admin|business-admin', 'employee-of-company']], function () {
        Route::get('companies/{company}/dashboard', [DashboardController::class, 'index']);
        Route::resource('companies', CompanyController::class)->only('view', 'update');
        Route::resource('companies.employees', EmployeeController::class);
        Route::resource('companies.work-schedules', WorkScheduleController::class);
        Route::resource('companies.payrolls', PayrollController::class);
        Route::resource('companies.employees.leaves', LeaveController::class);
        Route::resource('companies.employees.time-records', TimeRecordController::class);
        Route::resource('companies.employees.work-schedules', EmployeeScheduleController::class);
        Route::prefix('companies/{company}/employees')->group(function () {
            Route::post('{employee}/clock', [TimeRecordController::class, 'clock']);
            Route::prefix('{employee}/salary-computation')->group(function () {
                Route::get('/', [SalaryComputationController::class, 'show']);
                Route::post('/', [SalaryComputationController::class, 'store']);
                Route::put('/', [SalaryComputationController::class, 'update']);
                Route::delete('/', [SalaryComputationController::class, 'delete']);
            });
        });
        Route::resource('companies.payroll-periods', PeriodsController::class);
    });
});
