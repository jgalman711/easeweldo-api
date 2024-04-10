<?php

use App\Http\Controllers\Admin\BiometricsController as AdminBiometricsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BiometricsController;
use App\Http\Controllers\CompanyApprovers;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\CompanySubscriptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeScheduleController;
use App\Http\Controllers\EmployeeVerification\EmploymentDetailsVerificationController;
use App\Http\Controllers\EmployeeVerification\OtherDetailsVerificationController;
use App\Http\Controllers\EmployeeVerification\PersonalInformationVerificationController;
use App\Http\Controllers\EmployeeVerification\SalaryDetailsVerificationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\Leave\ApproveLeaveController;
use App\Http\Controllers\Leave\DeclineLeaveController;
use App\Http\Controllers\Leave\DiscardLeaveController;
use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Payroll\CancelPayrollController;
use App\Http\Controllers\Payroll\PayPayrollController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\RegeneratePayrollController;
use App\Http\Controllers\Period\CancelPeriodController;
use App\Http\Controllers\Period\GeneratePayrollsController;
use App\Http\Controllers\Period\PayPeriodController;
use App\Http\Controllers\Period\PeriodController;
use App\Http\Controllers\PersonalLoginController;
use App\Http\Controllers\Qr\CompanyQrController;
use App\Http\Controllers\Qr\EmployeeQrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryComputationController;
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
        '--force' => true,
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
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('holidays', HolidayController::class);
        Route::apiResource('users', UserController::class);
    });

    Route::group(['middleware' => ['role:super-admin|business-admin', 'valid.company.user']], function () {
        Route::apiResource('companies', CompanyController::class)->only('show', 'update');
        Route::prefix('companies/{company}')->group(function () {
            Route::get('approvers', CompanyApprovers::class);
            Route::get('dashboard', DashboardController::class);
            Route::apiResource('employees', CompanyEmployeeController::class);

            Route::apiResource('disbursements', DisbursementController::class)->only('store');
            Route::apiResource('periods', PeriodController::class)->except('store');
            Route::post('periods/{period}/generate-payroll', GeneratePayrollsController::class);
            Route::post('periods/{period}/pay', PayPeriodController::class);
            Route::post('periods/{period}/cancel', CancelPeriodController::class);

            Route::apiResource('payrolls', PayrollController::class)->except('delete');
            Route::post('payrolls/{payroll}/regenerate', RegeneratePayrollController::class);
            Route::post('payrolls/{payroll}/pay', PayPayrollController::class);
            Route::post('payrolls/{payroll}/cancel', CancelPayrollController::class);

            Route::apiResource('banks', BankController::class)->only('index', 'store');
            Route::apiResource('reports', ReportController::class)->only('show');
            Route::apiResource('settings', SettingController::class)->only('index', 'store');
            Route::apiResource('subscriptions', CompanySubscriptionController::class);
            Route::apiResource('work-schedules', WorkScheduleController::class);

            //Verify
            Route::post('timesheet/upload', [TimesheetUploadController::class, 'store']);
            Route::post('synch-biometrics/{module}/', [SynchBiometricsController::class, 'store']);
            Route::apiResource('biometrics', BiometricsController::class);
            Route::get('qrcode', [CompanyQrController::class, 'index']);
            //

            // can use precognition instead for the validation of information
            // in the employee creation
            Route::prefix('verification')->group(function () {
                Route::post('personal-information', PersonalInformationVerificationController::class);
                Route::post('employee-details', EmploymentDetailsVerificationController::class);
                Route::post('salary-details', SalaryDetailsVerificationController::class);
                Route::post('other-details', OtherDetailsVerificationController::class);
            });
        });
    });

    Route::group(['middleware' => ['role:super-admin|business-admin|approver', 'valid.company.user']], function () {
        Route::prefix('companies/{company}')->group(function () {
            Route::apiResource('leaves', LeaveController::class);
            Route::prefix('leaves/{leave}')->group(function () {
                Route::post('approve', ApproveLeaveController::class);
                Route::post('decline', DeclineLeaveController::class);
                Route::post('discard', DiscardLeaveController::class);
            });
        });
    });

    Route::group([
        'prefix' => 'companies/{company}/employees/{employee}',
        'middleware' => ['valid.company.user']
    ], function () {
        Route::get('dashboard', [UserDashboardController::class, 'index']);
        Route::post('clock', [TimeRecordController::class, 'clock']);
        Route::apiResource('time-records', TimeRecordController::class);
        Route::apiResource('time-corrections', TimeCorrectionController::class);
        Route::apiResource('work-schedules', EmployeeScheduleController::class);
        Route::apiResource('payrolls', UserPayrollController::class)->only('index', 'show');
        
        // Needs refactor
        Route::controller(SalaryComputationController::class)->group(function () {
            Route::get('salary-computation', 'show');
            Route::post('salary-computation', 'store');
            Route::put('salary-computation', 'update');
            Route::delete('salary-computation', 'delete');
        });

        // QR - START
        Route::get('qrcode', [EmployeeQrController::class, 'index']);
        Route::post('qrcode', [CompanyQrController::class, 'store']);
        // END
    });

    Route::group(['prefix' => 'employees/{employee}', 'middleware' => ['valid.company.user']], function () {
        Route::get('/', [EmployeeController::class, 'show']);
        Route::get('dashboard', [UserDashboardController::class, 'index']);
        Route::post('clock', [TimeRecordController::class, 'clock']);
        Route::apiResource('time-records', TimeRecordController::class);
        Route::apiResource('time-corrections', TimeCorrectionController::class);
        Route::apiResource('work-schedules', EmployeeScheduleController::class);
        Route::apiResource('payrolls', UserPayrollController::class)->only('index', 'show');
        // Needs refactor
        Route::controller(SalaryComputationController::class)->group(function () {
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
