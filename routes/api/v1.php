<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Attendance\AttendanceController;
use App\Http\Controllers\Api\V1\Employee\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'HRIS API v1 is reachable.',
        'version' => 'v1',
    ]);
});

// Authentication Routes
Route::prefix('auth')
    ->name('auth.')
    ->controller(AuthController::class)
    ->group(function (): void {
        // Guest routes
        Route::post('/login', 'login')->name('login');
        Route::post('/password/forgot', 'forgotPassword')->name('password.forgot');
        Route::get('/password/reset/{token}', 'verifyResetToken')->name('password.reset');
        Route::post('/password/reset', 'updatePassword')->name('password.update');

        // Authenticated routes
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('/check', function (Request $request) {
                return response()->json([
                    'success' => true,
                    'authenticated' => true,
                    'data' => [
                        'id' => $request->user()?->id,
                        'email' => $request->user()?->email,
                    ],
                ]);
            })->name('check');

            Route::post('/logout', 'logout')->name('logout');
        });
    });

Route::middleware('auth:sanctum')->group(function (): void {
    // Attendance Route
    Route::prefix('attendance')
        ->name('attendance.')
        ->controller(AttendanceController::class)
        ->group(function (): void {
            Route::get('/', 'list')->name('list');
            Route::get('/today', 'today')->name('today');
            Route::post('/time-in', 'timeIn')->name('time-in');
            Route::post('/time-out', 'timeOut')->name('time-out');
        });

    // Employee Route
    Route::prefix('employee')
        ->name('employee.')
        ->controller(EmployeeController::class)
        ->group(function (): void {
            Route::get('/', 'show')->name('show');
            Route::get('/documents', 'listDocuments')->name('documents.list');
            Route::post('/documents', 'uploadDocuments')->name('documents');
        });
});
