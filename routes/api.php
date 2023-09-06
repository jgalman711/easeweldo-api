<?php

use App\Http\Controllers\Admin\BiometricsController as AdminBiometricsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BiometricsController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PeriodsController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryComputationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPricesController;
use App\Http\Controllers\SynchBiometricsController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\TimesheetUploadController;
use App\Http\Controllers\UserController;
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
Route::get('reset-password', [PasswordResetController::class, 'index']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgot']);

Route::resource('/subscriptions', SubscriptionController::class)->only('index', 'show');
Route::resource('/subscription-prices', SubscriptionPricesController::class)->only('index');
Route::resource('/payment-methods', PaymentMethodController::class)->only('index');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['role:super-admin']], function () {
        Route::resource('biometrics', AdminBiometricsController::class);
        Route::resource('companies', CompanyController::class);
        Route::resource('holidays', HolidayController::class);
        Route::get('employees', [EmployeeController::class, 'all']);
    });
    Route::group(['middleware' => ['role:super-admin|business-admin', 'employee-of-company']], function () {
        Route::resource('companies', CompanyController::class)->only('index', 'show', 'update');
        Route::prefix('companies/{company}')->group(function () {
            Route::resource('/payrolls', PayrollController::class)->except('delete');
            Route::resource('/employees', EmployeeController::class);
            Route::prefix('employees/{employee}')->group(function () {
                Route::get('/qrcode', [QrController::class, 'show']);
                Route::post('/clock', [TimeRecordController::class, 'clock']);
                Route::resource('/leaves', LeaveController::class);
                Route::resource('/time-records', TimeRecordController::class);
                Route::resource('/work-schedules', EmployeeScheduleController::class);
                Route::prefix('/salary-computation')->group(function () {
                    Route::get('/', [SalaryComputationController::class, 'show']);
                    Route::post('/', [SalaryComputationController::class, 'store']);
                    Route::put('/', [SalaryComputationController::class, 'update']);
                    Route::delete('/', [SalaryComputationController::class, 'delete']);
                });
            });
            Route::resource('/subscriptions', CompanySubscriptionController::class);
            Route::resource('/work-schedules', WorkScheduleController::class);
            Route::resource('/periods', PeriodsController::class)->except('store');
            Route::resource('/reports', ReportController::class);
            Route::resource('/earnings', EarningController::class)->only('index', 'store');
            Route::resource('/settings', SettingController::class)->only('index', 'store');
            Route::get('/dashboard', [DashboardController::class, 'index']);
            Route::post('/timesheet/upload', [TimesheetUploadController::class, 'store']);

            Route::middleware('check-company-subscriptions')->group(function () {
                Route::post('/synch-biometrics/{module}/', [SynchBiometricsController::class, 'store']);
                Route::resource('/biometrics', BiometricsController::class);
            });
        });
    });
    Route::get('/user/qrcode', [UserController::class, 'qrcode']);
});
