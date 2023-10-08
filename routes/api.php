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
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Payroll\FinalPayrollController;
use App\Http\Controllers\Payroll\NthMonthPayrollController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\SpecialPayrollController;
use App\Http\Controllers\Period\PeriodActionController;
use App\Http\Controllers\Period\PeriodsController;
use App\Http\Controllers\Qr\CompanyQrController;
use App\Http\Controllers\Qr\EmployeeQrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryComputationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPricesController;
use App\Http\Controllers\SynchBiometricsController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\TimesheetUploadController;
use App\Http\Controllers\Upload\UploadEmployeeController;
use App\Http\Controllers\User\EmployeeChangePasswordController;
use App\Http\Controllers\User\UserChangePasswordController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserPayrollController;
use App\Http\Controllers\User\UserTemporaryPasswordResetController;
use App\Http\Controllers\VerifyTokenController;
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
    Route::post('verify', [VerifyTokenController::class, 'verify']);
    /**
     * Super Admin Only Route
     */
    Route::group(['middleware' => ['role:super-admin']], function () {
        Route::resource('biometrics', AdminBiometricsController::class);
        Route::resource('companies', CompanyController::class);
        Route::resource('holidays', HolidayController::class);
        Route::resource('users', UserController::class);
        Route::put('users/{user}/change-password', [UserChangePasswordController::class, 'update']);
        Route::put('users/{user}/reset-temporary-password', [UserTemporaryPasswordResetController::class, 'update']);
        Route::get('employees', [EmployeeController::class, 'all']);
    });
    Route::prefix('companies/{company}')->group(function () {
        Route::group(['middleware' => ['role:super-admin|business-admin']], function () {
            Route::get('/', [CompanyController::class, 'show']);
            Route::patch('/', [CompanyController::class, 'update']);
            Route::get('dashboard', [DashboardController::class, 'index']);
            Route::resource('employees', EmployeeController::class);
            Route::resource('payrolls', PayrollController::class)->except('delete');
            Route::resource('special-payrolls', SpecialPayrollController::class)->only('index', 'store');
            Route::resource('nth-month-payrolls', NthMonthPayrollController::class)->only('index', 'store');
            Route::resource('final-payrolls', FinalPayrollController::class)->only('index', 'store');
            Route::post('payrolls/{payroll}/regenerate', [PayrollController::class, 'regenerate']);
            Route::resource('subscriptions', CompanySubscriptionController::class);
            Route::resource('work-schedules', WorkScheduleController::class);
            Route::resource('periods', PeriodsController::class)->except('store');
            Route::put('periods/{period}/{action}', [PeriodActionController::class, 'update']);
            Route::resource('reports', ReportController::class);
            Route::resource('settings', SettingController::class)->only('index', 'store');
            Route::post('timesheet/upload', [TimesheetUploadController::class, 'store']);
            Route::middleware('check-company-subscriptions')->group(function () {
                Route::post('synch-biometrics/{module}/', [SynchBiometricsController::class, 'store']);
                Route::resource('biometrics', BiometricsController::class);
            });
            Route::post('upload/employees', [UploadEmployeeController::class, 'store']);
            Route::resource('overtime-requests', OvertimeRequestController::class);
            Route::resource('leave-requests', LeaveController::class);
            Route::get('qrcode', [CompanyQrController::class, 'index']);
        });
        Route::group(['prefix' => 'employees/{employee}', 'middleware' => ['employee-of-company']], function () {
            Route::get('/', [EmployeeController::class, 'show']);
            Route::get('dashboard', [UserDashboardController::class, 'index']);
            Route::get('qrcode', [EmployeeQrController::class, 'index']);
            Route::post('clock', [TimeRecordController::class, 'clock']);
            Route::resource('leaves', LeaveController::class);
            Route::resource('time-records', TimeRecordController::class);
            Route::resource('work-schedules', EmployeeScheduleController::class);
            Route::resource('payrolls', UserPayrollController::class)->only('index', 'show');
            Route::get('salary-computation', [SalaryComputationController::class, 'show']);
            Route::post('salary-computation', [SalaryComputationController::class, 'store']);
            Route::put('salary-computation', [SalaryComputationController::class, 'update']);
            Route::delete('salary-computation', [SalaryComputationController::class, 'delete']);
            Route::patch('change-password', [EmployeeChangePasswordController::class, 'update']);

             // START 'middleware' => ['employee-of-company']
            Route::post('qrcode', [CompanyQrController::class, 'store']);
            // END
        });
    });
});
