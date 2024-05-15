<?php

use App\Http\Controllers\Api\V2\LeaveController;
use App\Http\Controllers\Api\V2\SalaryComputationController;
use App\Http\Controllers\Api\V2\TimeCorrectionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'companies/{company}', 'middleware' => ['auth:sanctum', 'valid.company.user']], function () {
    Route::apiResource('employees.leaves', LeaveController::class);
    Route::apiResource('employees.salary-computation', SalaryComputationController::class)->only('index');
    Route::apiResource('employees.time-corrections', TimeCorrectionController::class)->only('index', 'show');
});
