<?php

use App\Http\Controllers\Admin\BiometricsController as AdminBiometricsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BiometricsController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\EmployeeVerification\EmploymentDetailsVerificationController;
use App\Http\Controllers\EmployeeVerification\PersonalInformationVerificationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ImportEmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Payroll\GeneratePayrollController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\RegeneratePayrollController;
use App\Http\Controllers\Period\PeriodActionController;
use App\Http\Controllers\Period\PeriodController;
use App\Http\Controllers\PersonalLoginController;
use App\Http\Controllers\Qr\CompanyQrController;
use App\Http\Controllers\Qr\EmployeeQrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPricesController;
use App\Http\Controllers\SynchBiometricsController;
use App\Http\Controllers\TimeCorrectionController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\TimesheetUploadController;
use App\Http\Controllers\User\UserChangePasswordController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserPayrollController;
use App\Http\Controllers\User\UserTemporaryPasswordResetController;
use App\Http\Controllers\VerifyTokenController;
use App\Http\Controllers\WorkScheduleController;
use App\Models\SalaryComputation;
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

Route::post('register', RegisterController::class, 'register');
Route::post('login', [AuthController::class, 'login']);

Route::post('personal/login', [PersonalLoginController::class, 'login']);
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::get('reset-password', [PasswordResetController::class, 'index']);
Route::post('forgot-password', [ForgotPasswordController::class, 'forgot']);

Route::apiResource('/subscriptions', SubscriptionController::class)->only('index', 'show');
Route::apiResource('/subscription-prices', SubscriptionPricesController::class)->only('index');
Route::apiResource('/payment-methods', PaymentMethodController::class)->only('index');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('verify', [VerifyTokenController::class, 'verify']);
    /**
     * Super Admin Only Route
     */
    Route::group(['middleware' => ['role:super-admin']], function () {
        Route::apiResource('biometrics', AdminBiometricsController::class);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('holidays', HolidayController::class);
        Route::apiResource('users', UserController::class);
        Route::get('employees', [EmployeeController::class, 'all']);
    });
    Route::group(['middleware' => ['role:super-admin|business-admin', 'valid.company.user']], function () {
        Route::apiResource('companies', CompanyController::class)->only('show', 'update');
        Route::prefix('companies/{company}')->group(function () {
            Route::apiResource('employees', EmployeeController::class);
            Route::post('employees/import', ImportEmployeeController::class);
            Route::apiResource('disbursements', DisbursementController::class);
            Route::apiResource('periods', PeriodController::class)->except('store');
            Route::post('periods/{period}/generate-payroll', GeneratePayrollController::class);
            
            // Payroll Routes
            // Route::get('payrolls/{payroll}/download', [ActionPayrollController::class, 'download']);
            // Route::post('payrolls/{payroll}/regenerate', [ActionPayrollController::class, 'regenerate']);
            // Route::put('payrolls/{payroll}/{status}', [ActionPayrollController::class, 'update']);
            Route::apiResource('payrolls', PayrollController::class)->except('delete');
            Route::post('payrolls/{payroll}/regenerate', RegeneratePayrollController::class);
            // End Payroll Routes

            Route::apiResource('subscriptions', CompanySubscriptionController::class);
            Route::apiResource('work-schedules', WorkScheduleController::class);
            Route::put('periods/{period}/{action}', [PeriodActionController::class, 'update']);
            
            Route::apiResource('reports', ReportController::class)->only('show');
            Route::apiResource('settings', SettingController::class)->only('index', 'store');
            Route::post('timesheet/upload', [TimesheetUploadController::class, 'store']);
            Route::post('synch-biometrics/{module}/', [SynchBiometricsController::class, 'store']);
            Route::apiResource('biometrics', BiometricsController::class);
            Route::apiResource('overtime-requests', OvertimeRequestController::class);
            Route::get('qrcode', [CompanyQrController::class, 'index']);
            Route::get('dashboard', [DashboardController::class, 'index']);

            Route::prefix('verification')->group(function () {
                Route::post('personal-information', PersonalInformationVerificationController::class);
                Route::post('employee-details', EmploymentDetailsVerificationController::class);
            });
        });
    });
    /**
     * @TODO
     *
     * Should have the business-admin middleware as well.
     * Employee of company middleware should also check if the logged in user is the employee.
     */
    Route::group(['prefix' => 'companies/{company}/employees/{employee}', 'middleware' => ['employee-of-company']], function () {
        Route::get('/', [EmployeeController::class, 'show']);
        Route::get('dashboard', [UserDashboardController::class, 'index']);
        Route::post('clock', [TimeRecordController::class, 'clock']);
        Route::apiResource('leaves', LeaveController::class);
        Route::apiResource('time-records', TimeRecordController::class);
        Route::apiResource('time-corrections', TimeCorrectionController::class);
        Route::apiResource('work-schedules', EmployeeScheduleController::class);
        Route::apiResource('payrolls', UserPayrollController::class)->only('index', 'show');
        // Needs refactor
        Route::controller(SalaryComputation::class)->group(function () {
            Route::get('salary-computation', 'show');
            Route::post('salary-computation', 'store');
            Route::put('salary-computation', 'update');
            Route::delete('salary-computation', 'delete');
        });

        // Needs deletion
        Route::put('change-password', [UserChangePasswordController::class, 'update']);
        Route::put('reset-temporary-password', [UserTemporaryPasswordResetController::class, 'update']);

            // QR - START
        Route::get('qrcode', [EmployeeQrController::class, 'index']);
        Route::post('qrcode', [CompanyQrController::class, 'store']);
        // END
    });
});
