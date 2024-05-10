<?php

use App\Http\Controllers\Api\V2\LeaveController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'companies/{company}', 'middleware' => ['auth:sanctum', 'valid.company.user']], function () {
    Route::apiResource('employees.leaves', LeaveController::class);
});
